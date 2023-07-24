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
use Exception;
use Helper; 
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
                                $finalPrice = $item->booking_price > 0 ? $item->booking_price : $item->final_price;
                                return $item->qty * $finalPrice;
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
                'address_line_1'    => 'required',
                'address_line_2'    => 'required',
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
                'address_line_1'    => 'required',
                'address_line_2'    => 'required',
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

            if(UserAddress::where('id',Auth::user()->id)->count() < 2){
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
                                $finalPrice = $item->booking_price > 0 ? $item->booking_price : $item->final_price;
                                return $item->qty * $finalPrice;
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
                'order'              => 'required|array',
                'order.*.product_id' => 'required',
                'order.*.qty'        => 'required',
                'order_status'      => 'required',
                'payment_method'    => 'required',
                'payment_status'    => 'required',
                'address_id'        => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $user_id              = Auth::id();
            $input['user_id']     = $user_id;

            $user_address = UserAddress::where('id',$input['address_id'])->first();
            if(empty($user_address)){
                return $this->error([],'Address not found');
            }
          
            $order = new Order([
                'user_id'       => $user_id,
                'order_id'      => 'order_' . $user_id .'_'. str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'address_id'    => $input['address_id'],
                'total_amount'  => 100,
                'order_status'  => $input['order_status'],
                'payment_method'=> $input['payment_method'],
                'payment_status'=> $input['payment_status'],
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
                
                    $product = Product::select('title', 'category_id', 'final_price', 'discount', 'tax', 'discount_amount', 'tax_amount', 'original_price', 'pay_booking_price', 'pay_booking_price_tax', 'unit', 'weight', 'stock', 'minimum_stock', 'brand', 'version', 'tags', 'description', 'description1', 'description2', 'is_active', 'is_varient')
                        ->where('id', $productId)
                        ->first();
                
                    if ($productVariantId) {

                        $productVariant = ProductVariant::select('final_price', 'discount', 'tax', 'discount_amount', 'tax_amount', 'original_price', 'pay_booking_price', 'pay_booking_price_tax', 'unit', 'weight', 'stock', 'minimum_stock')
                            ->where('id', $productVariantId)
                            ->first();
                
                        if ($productVariant) {
                            $product = $product->toArray();
                            $product = array_merge($product, $productVariant->toArray());
                        }
                    }
                
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
                        'original_price'        =>  $product['original_price'],
                        'pay_booking_price'     =>  $product['pay_booking_price'],
                        'pay_booking_price_tax' =>  $product['pay_booking_price_tax'],
                        'unit'                  =>  $product['unit'],
                        'weight'                =>  $product['weight'],
                        'stock'                 =>  $product['stock'],
                        'minimum_stock'         =>  $product['minimum_stock'],
                        'brand'                 =>  $product['brand'],
                        'version'               =>  $product['version'],
                        'tags'                  =>  $product['tags'],
                        'description'           =>  $product['description'],
                        'description1'          =>  $product['description1'],
                        'description2'          =>  $product['description2'],
                        'created_at'            =>  now(),
                        'updated_at'            =>  now(),
                    ];
                }
                OrderItem::insert($orderItemsData);

                return $this->success([],'Order successfully');
            }
            return $this->error('Something went wrong','Something went wrong');
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
            $order_list            = Order::where('user_id',$user_id)->latest()->paginate($request->input('perPage'), ['*'], 'page', $request->input('page'));

            $data['order_list']    =  $order_list->values();
            $data['current_page']  =  $order_list->currentPage();
            $data['per_page']      =  $order_list->perPage();
            $data['total']         =  $order_list->total();
            $data['last_page']     =  $order_list->lastPage();

            return $this->success($data,'Address list');
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
