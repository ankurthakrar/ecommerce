<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\File;
use App\Http\Controllers\BaseController;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Helper;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\Wishlist;
use Exception;
use Validator;

class CustomerController extends BaseController
{
    // CART ITEM LIST

    public function cartItemList(Request $request){
        try{
            $user_id            = Auth::user()->id;

            $data['cartItems'] = Cart::where('user_id', $user_id)
                                ->Join('products', 'carts.product_id', '=', 'products.id')
                                ->leftJoin('product_variants', function ($join) {
                                    $join->on('carts.product_variation_id', '=', 'product_variants.id')
                                        ->whereNotNull('carts.product_variation_id')
                                        ->whereColumn('carts.product_id', 'product_variants.product_id');
                                })
                                ->select('carts.id', 'carts.user_id', 'carts.product_id', 'carts.product_variation_id', 'carts.qty','carts.is_booking_price', 'products.title')
                                ->selectRaw('IFNULL(product_variants.final_price, products.final_price) as final_price')
                                ->selectRaw('IF(product_variants.final_price IS NULL, 0, 1) as is_variant')
                                ->selectRaw('IFNULL(product_variants.discount, products.discount) as discount')
                                ->selectRaw('IFNULL(product_variants.after_discount_amount, products.after_discount_amount) as after_discount_amount')
                                ->selectRaw('IFNULL(product_variants.colour, null) as color')
                                ->selectRaw('IFNULL(product_variants.color_name, null) as color_name')
                                ->selectRaw('IFNULL(product_variants.size, null) as size')
                                ->selectRaw("
                                        CASE
                                            WHEN (product_variants.id IS NOT NULL AND carts.product_id = product_variants.product_id) THEN (
                                                SELECT file_name FROM images WHERE type = 'product_variant_image' AND type_id = carts.product_variation_id LIMIT 1
                                            )
                                            ELSE (
                                                SELECT file_name FROM images WHERE type = 'product_image' AND type_id = carts.product_id LIMIT 1
                                            )
                                        END as image_url
                                    ")
                                ->selectRaw("
                                    CASE
                                        WHEN (carts.is_booking_price = 1 AND carts.product_variation_id IS NULL) THEN
                                            IF(products.pay_booking_price_tax > 0, (products.pay_booking_price * (1 + (products.pay_booking_price_tax / 100))), products.pay_booking_price)
                                        WHEN (carts.is_booking_price = 1 AND carts.product_variation_id IS NOT NULL) THEN
                                            IF(product_variants.pay_booking_price_tax > 0, (product_variants.pay_booking_price * (1 + (product_variants.pay_booking_price_tax / 100))), product_variants.pay_booking_price)
                                        ELSE 0
                                    END as booking_price
                                ")
                                ->where(function ($query) {
                                    $query->where(function ($q) {
                                        $q->whereNull('product_variants.id')
                                            ->whereNull('carts.product_variation_id');
                                    })
                                    ->orWhere(function ($q) {
                                        $q->whereNotNull('product_variants.id')
                                            ->whereColumn('carts.product_id', 'product_variants.product_id');
                                    });
                                })
                                ->get(); 

            $data['cartItems']->transform(function ($item) {
                $item->cart_image_url = $item->getCartImageUrlAttribute();
                return $item;
            });

            $data['total'] = $data['cartItems']->sum(function ($item) { 
                                $after_discount_amount = $item->booking_price > 0 ? $item->booking_price : $item->after_discount_amount;
                                return $item->qty * $after_discount_amount;
                            });
            
            return $this->success($data,'Cart item list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // CART ITEM STORE AND UPDATE

    public function cartItemStore(Request $request){
        try{
            $input            = $request->all();
            $user_id          = Auth::user()->id;

            $validateData = Validator::make($input, [
                'cart'              => 'required|array',
                'cart.*.product_id' => 'required',
                'cart.*.qty'        => 'required',
                'type'              => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $input['user_id'] = $user_id;

            foreach ($input['cart'] as $item) {
                $item['user_id'] = $user_id;
            
                $cartItem = Cart::where('user_id', $user_id)
                    ->where('product_id', $item['product_id'])
                    ->where(function ($query) use ($item) {
                        $query->where('product_variation_id', $item['product_variation_id'] ?? null);
                    })
                    ->first();
            
                if ($cartItem) {
                    // Cart item already exists, update the quantity
                    if ($input['type'] == 'add') {
                        $cartItem->qty += $item['qty'];
                    }
                    if ($input['type'] == 'edit') {
                        $cartItem->qty = $item['qty'];
                    }
                    $cartItem->save();
                } else {
                    // Cart item doesn't exist, create a new entry
                    Cart::create($item);
                }
            }
            return $this->success([],'Item added to cart successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // CART ITEM DELETE

    public function cartItemDelete(Request $request){
        try{
            $validateData = Validator::make($request->all(), [
                'product_id'            => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $input            = $request->all();
            $user_id          = Auth::user()->id;
            $input['user_id'] = $user_id;
            $cartItem         = Cart::where('user_id', $user_id)
                                ->where('product_id', $input['product_id'])
                                ->where(function ($query) use ($input) {
                                    $query->where('product_variation_id', $input['product_variation_id'] ?? null)
                                        ->orWhereNull('product_variation_id');
                                })
                                ->first();
        
            if ($cartItem) {
                $cartItem->delete();
                return $this->success([], 'Cart item deleted successfully');
            } else {
                return $this->error([], 'No data found', 404);
            }

            return $this->success([],'Item deleted from cart successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // WISHLIST

    public function wishList(Request $request){
        try{
            $user_id            = Auth::user()->id;

            $data['wishlists'] = Wishlist::where('user_id', $user_id)
                                    ->Join('products', 'wishlists.product_id', '=', 'products.id')
                                    ->leftJoin('product_variants', function ($join) {
                                        $join->on('wishlists.product_variation_id', '=', 'product_variants.id')
                                            ->whereNotNull('wishlists.product_variation_id')
                                            ->whereColumn('wishlists.product_id', 'product_variants.product_id');
                                    })
                                    ->select('wishlists.id', 'wishlists.user_id', 'wishlists.product_id', 'wishlists.product_variation_id', 'products.title')
                                    ->selectRaw('IFNULL(product_variants.final_price, products.final_price) as final_price')
                                    ->selectRaw('IF(product_variants.final_price IS NULL, 0, 1) as is_variant')
                                    ->selectRaw('IFNULL(product_variants.discount, products.discount) as discount')
                                    ->selectRaw('IFNULL(product_variants.after_discount_amount, products.after_discount_amount) as after_discount_amount')
                                    ->selectRaw('IFNULL(product_variants.colour, null) as color')
                                    ->selectRaw('IFNULL(product_variants.color_name, null) as color_name')
                                    ->selectRaw('IFNULL(product_variants.size, null) as size')
                                    ->selectRaw("
                                            CASE
                                                WHEN (product_variants.id IS NOT NULL AND wishlists.product_id = product_variants.product_id) THEN (
                                                    SELECT file_name FROM images WHERE type = 'product_variant_image' AND type_id = wishlists.product_variation_id LIMIT 1
                                                )
                                                ELSE (
                                                    SELECT file_name FROM images WHERE type = 'product_image' AND type_id = wishlists.product_id LIMIT 1
                                                )
                                            END as image_url
                                        ")
                                    ->where(function ($query) {
                                        $query->where(function ($q) {
                                            $q->whereNull('product_variants.id')
                                                ->whereNull('wishlists.product_variation_id');
                                        })
                                        ->orWhere(function ($q) {
                                            $q->whereNotNull('product_variants.id')
                                                ->whereColumn('wishlists.product_id', 'product_variants.product_id');
                                        });
                                    })
                                    ->get(); 

            $data['wishlists']->transform(function ($item) {
                $item->wish_list_url = $item->getWishListImageUrlAttribute();
                return $item;
            });
            return $this->success($data,'Wishlist');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // WISHLIST STORE

    public function wishlistStore(Request $request){
        try{
            $input            = $request->all();
            $user_id          = Auth::user()->id;

            $validateData = Validator::make($input, [
                'product_id' => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $input['user_id']              = $user_id;
            $input['product_id']           = $request->product_id;
            $input['product_variation_id'] = $request->product_variation_id ?? null;
            $input['type']                 = $request->product_variation_id ? 'product_variant' : 'product' ;

            $wishlist = Wishlist::where('user_id', $user_id)
                        ->where('product_id', $input['product_id'])
                        ->where(function ($query) use ($input) {
                            $query->where('product_variation_id', $input['product_variation_id'] ?? null);
                        })
                        ->first();
    
            if ($wishlist) {
                $wishlist->delete();
                return $this->success([],'Item is removed from wishlist');
            }

            Wishlist::create($input); 
            return $this->success([],'Item added to wishlist successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // WISHLIST DELETE

    public function wishlistDelete(Request $request){
        try{
            $validateData = Validator::make($request->all(), [
                'product_id'            => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $input            = $request->all();
            $user_id          = Auth::user()->id;
            $input['user_id'] = $user_id;
            $wishlistItem     = Wishlist::where('user_id', $user_id)
                                ->where('product_id', $input['product_id'])
                                ->where(function ($query) use ($input) {
                                    $query->where('product_variation_id', $input['product_variation_id'] ?? null)
                                        ->orWhereNull('product_variation_id');
                                })
                                ->first();
        
            if ($wishlistItem) {
                $wishlistItem->delete();
                return $this->success([], 'Wishlist item deleted successfully');
            } else {
                return $this->error([], 'No data found', 404);
            }

            return $this->success([],'Item deleted from wishlist successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // ADDRESS LIST

    public function addressList(Request $request)
    {
        try{
            $user_id              = Auth::id();
            $data['address_list'] = UserAddress::where('user_id',$user_id)->get();
            return $this->success($data,'Address list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // ADDRESS STORE

    public function addressStore(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'full_name'         => 'required',
                'email'             => 'required|email|max:255',
                'phone_no'          => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:13', 
                'address_line_1'    => 'required',
                // 'address_line_2'    => 'required',
                'city_id'           => 'required',
                'state_id'          => 'required',
                'pincode'           => 'required|max:7',
                'address_type'      => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $input = $request->all();
            $input['user_id']      = Auth::user()->id;
            $input['address_type'] = strtolower($input['address_type']);
            UserAddress::create($input);
            return $this->success([],'Address store successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // ADDRESS DETAILS

    public function addressDetail(Request $request,$id)
    {
        try{
            if ($id < 1) {
                return $this->error('Please select valid address','Please select valid address');
            }
            $data = UserAddress::where('id',$id)->first();
            if(!empty($data)){
                return $this->success($data,'Address details');
            }
            return $this->error('Address not found','Address not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // ADDRESS UPDATE

    public function addressUpdate(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'address_id'        => 'required',
                'full_name'         => 'required',
                'email'             => 'required|email|max:255',
                'phone_no'          => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:13', 
                'address_line_1'    => 'required',
                // 'address_line_2'    => 'required',
                'city_id'           => 'required',
                'state_id'          => 'required',
                'pincode'           => 'required|max:7',
                'address_type'      => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            } 

            $user_address_details = UserAddress::where('id',$request->address_id)->first();
            if(!empty($user_address_details)){
                $input = $request->all();
                $input['address_type'] = strtolower($input['address_type']);
                $user_address_details->update($input);
                return $this->success([],'Address updated successfully');
            }

            return $this->error('Address not found','Address not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // ADDRESS DELETE

    public function addressDelete(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'address_id'  => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            if(UserAddress::where('user_id',Auth::user()->id)->count() < 2){
                return $this->error('You can not delete main address', 'You can not delete main address', 404);
            }

            $userAddress = UserAddress::where('id',$request['address_id'])->first();
        
            if ($userAddress) {
                $userAddress->delete();
                return $this->success([], 'Address deleted successfully');
            } else {
                return $this->error([], 'No data found', 404);
            }

            return $this->success([],'Address deleted successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // CHECKOUT PAGE

    public function checkout(){
        try{
            $user_id              = Auth::id();

            
            $data['cartItems'] = Cart::where('user_id', $user_id)
                                ->Join('products', 'carts.product_id', '=', 'products.id')
                                ->leftJoin('product_variants', function ($join) {
                                    $join->on('carts.product_variation_id', '=', 'product_variants.id')
                                        ->whereNotNull('carts.product_variation_id')
                                        ->whereColumn('carts.product_id', 'product_variants.product_id');
                                })
                                ->select('carts.id', 'carts.user_id', 'carts.product_id', 'carts.product_variation_id', 'carts.qty','carts.is_booking_price', 'products.title')
                                ->selectRaw('IFNULL(product_variants.final_price, products.final_price) as final_price')
                                ->selectRaw('IF(product_variants.final_price IS NULL, 0, 1) as is_variant')
                                ->selectRaw('IFNULL(product_variants.discount, products.discount) as discount')
                                ->selectRaw('IFNULL(product_variants.after_discount_amount, products.after_discount_amount) as after_discount_amount')
                                ->selectRaw('IFNULL(product_variants.colour, null) as color')
                                ->selectRaw('IFNULL(product_variants.color_name, null) as color_name')
                                ->selectRaw('IFNULL(product_variants.size, null) as size')
                                ->selectRaw("
                                        CASE
                                            WHEN (product_variants.id IS NOT NULL AND carts.product_id = product_variants.product_id) THEN (
                                                SELECT file_name FROM images WHERE type = 'product_variant_image' AND type_id = carts.product_variation_id LIMIT 1
                                            )
                                            ELSE (
                                                SELECT file_name FROM images WHERE type = 'product_image' AND type_id = carts.product_id LIMIT 1
                                            )
                                        END as image_url
                                    ")
                                ->selectRaw("
                                    CASE
                                        WHEN (carts.is_booking_price = 1 AND carts.product_variation_id IS NULL) THEN
                                            IF(products.pay_booking_price_tax > 0, (products.pay_booking_price * (1 + (products.pay_booking_price_tax / 100))), products.pay_booking_price)
                                        WHEN (carts.is_booking_price = 1 AND carts.product_variation_id IS NOT NULL) THEN
                                            IF(product_variants.pay_booking_price_tax > 0, (product_variants.pay_booking_price * (1 + (product_variants.pay_booking_price_tax / 100))), product_variants.pay_booking_price)
                                        ELSE 0
                                    END as booking_price
                                ")
                                ->where(function ($query) {
                                    $query->where(function ($q) {
                                        $q->whereNull('product_variants.id')
                                            ->whereNull('carts.product_variation_id');
                                    })
                                    ->orWhere(function ($q) {
                                        $q->whereNotNull('product_variants.id')
                                            ->whereColumn('carts.product_id', 'product_variants.product_id');
                                    });
                                })
                                ->get(); 

            $data['cartItems']->transform(function ($item) {
                $item->cart_image_url = $item->getCartImageUrlAttribute();
                return $item;
            });

            $data['total'] = $data['cartItems']->sum(function ($item) { 
                                $after_discount_amount = $item->booking_price > 0 ? $item->booking_price : $item->after_discount_amount;
                                return $item->qty * $after_discount_amount;
                            });

            $data['address_list'] = UserAddress::where('user_id',$user_id)->get();
            return $this->success($data,'Address list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // PLACE ORDER

    public function placeOrder(Request $request){
        try{
            $input                = $request->all();
            $validateData = Validator::make($input, [ 
                'order_status'            => 'required',
                'payment_method'          => 'required',
                'payment_status'          => 'required',
                'address_id'              => 'required',
                'cart'                    => 'required|array',
                'cart.*.id'               => 'required',
                'cart.*.final_item_price' => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $user_id              = Auth::id();
            $input['user_id']     = $user_id;

            foreach ($request->cart as $cartData) {
                Cart::where('id', $cartData['id'])->update(['final_item_price' => $cartData['final_item_price']]);
            }
            
            $input['order']          = Cart::where('user_id', $user_id)->get();
           
            $user_address = UserAddress::where('id',$input['address_id'])->first();
            if(empty($user_address)){
                return $this->error([],'Address not found');
            }

            $first_name = Auth::user()->first_name;
            $last_name = Auth::user()->last_name;
            if(empty($first_name) && empty($last_name)){
                $full_name = explode(' ', $user_address->full_name, 2);
                Auth::user()->update(['first_name' => $full_name[0],'last_name' => $full_name[1] ?? null]);
            }
          
            $order = new Order([
                'user_id'       => $user_id,
                'order_id'      => 'order_' . $user_id .'_'. str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'address_id'    => $input['address_id'],
                'order_status'  => $input['order_status'],
                'payment_method'=> $input['payment_method'],
                'payment_status'=> $input['payment_status'],
                'payment_id'    => $input['payment_id'] ?? null,
                'full_name'     => $user_address->full_name,
                'email'         => $user_address->email,
                'phone_no'      => $user_address->phone_no,
                'address_line_1'=> $user_address->address_line_1,
                'address_line_2'=> $user_address->address_line_2,
                'city_id'       => $user_address->city_id,
                'state_id'      => $user_address->state_id,
                'pincode'       => $user_address->pincode,
                'address_type'  => $user_address->address_type,
                'total_amount'  => $input['total_amount'],
            ]);
            $order->save();
            $order_data = $order->toArray(); 
           
            if(!empty($order_data)){
                $orderItems = [];
                foreach ($input['order'] as $item) {
                    $productId = $item['product_id'];
                    $productVariantId = $item['product_variation_id'] ?? null;
                
                    $product = Product::select('title', 'category_id', 'final_price', 'discount', 'tax', 'discount_amount', 'tax_amount', 'after_discount_amount', 'original_price', 'pay_booking_price', 'pay_booking_price_tax', 'sku', 'weight', 'stock', 'minimum_stock', 'brand', 'version', 'tags', 'description', 'description1', 'description2', 'is_active', 'is_varient')
                        ->where('id', $productId)
                        ->first();

                    $remaining_variant_stock = null;
                    $remaining_product_stock = null;
                    if ($productVariantId) {
                        
                        $productVariant = ProductVariant::select('final_price', 'discount', 'tax', 'discount_amount', 'tax_amount', 'after_discount_amount', 'original_price', 'pay_booking_price', 'pay_booking_price_tax', 'sku', 'weight', 'stock', 'minimum_stock', 'colour', 'color_name', 'size', 'available_in')
                        ->where('id', $productVariantId)
                        ->first();
                        
                        if ($productVariant) {
                            $product = $product->toArray();
                            $product = array_merge($product, $productVariant->toArray());
                        }
                        if($productVariant->stock > 0){
                           $remaining_variant_stock =  $productVariant->stock - $item['qty'];
                        }
                    }elseif($product->stock > 0){
                        $remaining_product_stock =  $product->stock - $item['qty'];
                    };

                    $orderItemsData[] = [
                        'user_id'               =>  $user_id,
                        'order_id'              =>  $order_data['id'],
                        'is_booking_price'      =>  $item['is_booking_price'] ?? 0,
                        'product_id'            =>  $productId,
                        'product_variation_id'  =>  $productVariantId,
                        'qty'                   =>  $item['qty'],
                        'title'                 =>  $product['title'],
                        'category_id'           =>  $product['category_id'],
                        'final_price'           =>  $product['final_price'],
                        'discount'              =>  $product['discount'],
                        'tax'                   =>  $product['tax'],
                        'discount_amount'       =>  $product['discount_amount'],
                        'tax_amount'            =>  $product['tax_amount'],
                        'after_discount_amount' =>  $product['after_discount_amount'],
                        'original_price'        =>  $product['original_price'],
                        'pay_booking_price'     =>  $product['pay_booking_price'],
                        'pay_booking_price_tax' =>  $product['pay_booking_price_tax'],
                        'sku'                   =>  $product['sku'],
                        'weight'                =>  $product['weight'],
                        'stock'                 =>  $product['stock'],
                        'minimum_stock'         =>  $product['minimum_stock'],
                        'colour'                =>  $productVariant['colour'] ?? null,
                        'color_name'            =>  $productVariant['color_name'] ?? null,
                        'size'                  =>  $productVariant['size'] ?? null,
                        'available_in'          =>  $productVariant['available_in'] ?? null,
                        'brand'                 =>  $product['brand'],
                        'version'               =>  $product['version'],
                        'tags'                  =>  $product['tags'],
                        'description'           =>  $product['description'],
                        'description1'          =>  $product['description1'],
                        'description2'          =>  $product['description2'],
                        'final_item_price'      =>  $item['final_item_price'],
                        'created_at'            =>  now(),
                        'updated_at'            =>  now(),
                    ];

                    if($remaining_product_stock !== null){
                        Product::where('id', $productId)->update(['stock'=>$remaining_product_stock]);
                    }
                    if($remaining_variant_stock !== null){
                        ProductVariant::where('id', $productVariantId)->update(['stock'=>$remaining_variant_stock]);
                    }
                }
                OrderItem::insert($orderItemsData);
                $cartItem         = Cart::where('user_id', $user_id)->delete();

                $data['order_id']  =  $order_data['id'];
                $data['is_send_mail']  = 0;

                if(($input['payment_method'] == 'online' && $input['payment_status'] == 'approved') || ($input['payment_method'] == 'cod' && $input['payment_status'] == 'pending')){
                    $data['is_send_mail']  = 1;
                }
                return $this->success($data,'Order successfully');
            }
            return $this->error('Something went wrong','Something went wrong');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // ORDER MAIL AND SMS SEND

    public function orderMailSMS(Request $request){
        try{
            $user_id          = Auth::user()->id;

            $validateData = Validator::make($request->all(), [
                'order_id'      => 'required',  
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $order_data = Order::with('orderItems','user')->where('id',$request->order_id)->where('user_id',$user_id)->first();
            $order_data['original_full_name'] = Auth::user()->first_name.' '.Auth::user()->last_name;
            $state_name = State::where('id',$order_data->state_id)->first();
            $city_name = City::where('id',$order_data->city_id)->first(); 
            $order_data['city'] = $city_name->name ;
            $order_data['state'] = $state_name->name;

            $messageTemplate = "Dear Customer, thank you for shopping with Hub Sports! Your order # {{orderNumber}} has been received. We'll notify you once it ships.";
            $orderNumber  = $order_data['order_id']; 
            $message      = str_replace('{{orderNumber}}', $orderNumber, $messageTemplate);
            $responseData = Helper::sendOTP($message,Auth::user()->phone_no);
 
            $email_data   = [
                'email'                 => Auth::user()->email,
                'order_confirmation'    => 'order_confirmation',
                'order'                 => $order_data,
            ]; 
            Helper::sendMail('emails.order_confirmation', $email_data, Auth::user()->email, '');

            return $this->success([],'Sent successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    //  ORDER LIST

    public function getOrderList(Request $request)
    {
        try{
            $user_id               = Auth::id();
            $order_list            = Order::with('orderItems')->where('user_id',$user_id)->latest()->paginate($request->input('perPage'), ['*'], 'page', $request->input('page'));

            $data['order_list']    =  $order_list->values();
            $data['current_page']  =  $order_list->currentPage();
            $data['per_page']      =  $order_list->perPage();
            $data['total']         =  $order_list->total();
            $data['last_page']     =  $order_list->lastPage();

            return $this->success($data,'Order list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // ORDER DETAILS

    public function getOrderDetails(Request $request,$id)
    {
        try{
            if ($id < 1) {
                return $this->error('Please select valid order','Please select valid order');
            }
            $user_id               = Auth::id();
            $data['order_details'] = Order::with('orderItems')->where('id',$id)->where('user_id',$user_id)->first();
            if(!empty($data['order_details'])){
                return $this->success($data,'Order details');
            }
            return $this->error('Order not found','Order not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
    
    //  DOCUMENT LIST

    public function getDocumentList(Request $request)
    {
        try{
            $user_id               = Auth::id();
            $document_list         = UserDocument::where('user_id',$user_id)->latest()->paginate($request->input('perPage'), ['*'], 'page', $request->input('page'));

            $data['document_list'] =  $document_list->values();
            $data['current_page']  =  $document_list->currentPage();
            $data['per_page']      =  $document_list->perPage();
            $data['total']         =  $document_list->total();
            $data['last_page']     =  $document_list->lastPage();

            return $this->success($data,'Document list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // DOCUMENT DETAILS

    public function getDocmentDetails(Request $request,$id)
    {
        try{
            if ($id < 1) {
                return $this->error('Please select valid document','Please select valid document');
            }
            $user_id               = Auth::id();
            $data['document_details'] = UserDocument::where('id',$id)->where('user_id',$user_id)->first();
            if(!empty($data['document_details'])){
                return $this->success($data,'Document details');
            }
            return $this->error('Document not found','Document not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // DOCUMENT UPLOAD

    public function uploadDocument(Request $request){
        try{
            $input            = $request->all();
            $user_id          = Auth::user()->id;

            $atLeastOneDocumentProvided = collect($input['document'])->filter(function ($document) {
                return !empty($document['document']);
            })->count() > 0;

            if(!$atLeastOneDocumentProvided){
                $atleast = 'At least one document is required when any document file is provided.';
                return $this->error($atleast, 'Validation error', 422);
            }

            $validateData = Validator::make($input, [
                'document.*.title'      => 'required', 
                'document.*.document'   => 'sometimes|mimes:jpg,jpeg,pdf',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $data['user_id'] = $user_id;
            foreach($input['document'] as $inputt){
                $data['title'] = $inputt['title'];
                $doc = $inputt['document'] ?? null;
                $filename = "";

                if(!empty($doc)){
                    $folderPath = public_path().'/user_document';
                    if (!is_dir($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
                    $extension  = $doc->getClientOriginalExtension();
                    $filename = 'user_doc_'.$user_id.'_'.random_int(10000, 99999). '.' . $extension;
                    $doc->move(public_path('user_document'), $filename);
                }
    
                $data['doc_name'] = $filename;
    
                UserDocument::create($data);
            }

            return $this->success([],'Document added successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // DOCUMENT UPDATE

    public function updateDocument(Request $request){
        try{
            $input            = $request->all();
            $user_id          = Auth::user()->id;
            $validateData = Validator::make($input, [
                'id'         => 'required',
                'title'      => 'required',
                'document'   => 'sometimes|mimes:jpg,jpeg,pdf',
            ]);
            
            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $document_details = UserDocument::where('id',$request->id)->first();
            if(!empty($document_details)){
               
                $input['user_id'] = $user_id;
                $input['doc_name'] = "";

                if($document_details->doc_name != ''){
                    $filePath = public_path().'/user_document/'.$document_details->doc_name;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                if(isset($request->document)){
                    $doc = $request->document;
                    $folderPath = public_path().'/user_document';
                    if (!is_dir($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
                    $extension  = $doc->getClientOriginalExtension();
                    $filename = 'user_doc_'.$user_id.'_'.random_int(10000, 99999). '.' . $extension;
                    $doc->move(public_path('user_document'), $filename);
        
                    $input['doc_name'] = $filename;
                }

                $document_details = $document_details->update($input);
                return $this->success([],'Document updated successfully');
            }
            return $this->error('Document not found','Document not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // USER DETAILS

    public function getUserProfile()
    {
        try{
            $user_id               = Auth::id();
            $data['user_details']  = User::where('id',$user_id)->first();
            if(!empty($data['user_details'])){
                return $this->success($data,'User details');
            }
            return $this->error('Something went wrong','Something went wrong');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
    
    // USER DETAIL UPDATE

    public function userDetailUpdate(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'first_name'      => 'required|string|max:255',
                'last_name'       => 'required|string|max:255',
                'email'           => 'sometimes|nullable|email|max:255|unique:users,email,'.Auth::id(),
                'phone_no'        => 'sometimes|nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:13|unique:users,phone_no,'.Auth::id(),
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $user_id               = Auth::id();
            $data['user_details']  = User::where('id',$user_id)->first();
            if(!empty($data['user_details'])){
                $data['user_details']->update($request->all());
                return $this->success($data,'User details');
            }
            return $this->error('Something went wrong','Something went wrong');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
  
    // USER CHANGE PASSWORD

    public function changePassword(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'old_password'        => 'nullable|string|min:8|bail|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$%^&+=!]).*$/',
                'new_password'        => 'required|string|min:8|bail|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$%^&+=!]).*$/',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $user_id               = Auth::id();
            $user_details          = User::where('id',$user_id)->first();

            if(!empty($user_details->password) && Hash::check($request->old_password, $user_details->password)) {
                $user_details->update(['password'=> bcrypt($request->new_password)]);
                return $this->success([],'User details');
            }
            if(empty($user_details->password)){
                $user_details->update(['password'=> bcrypt($request->new_password)]);
                return $this->success([],'User details');
            }
            return $this->error('Old password is wrong','Old password is wrong');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // USER LOGOUT

    public function logout(){
        try{
            if (Auth::user()) {
                $user = Auth::user()->token();
                $user->revoke();
                return $this->success([],'You are successfully logout');
            }
            return $this->error('Something went wrong','Something went wrong');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
}
