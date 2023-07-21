<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\File;
use App\Http\Controllers\BaseController;
use App\Models\Cart;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Helper; 
use Validator;

class CustomerController extends BaseController
{
    // CART ITEM DELETE

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
                                ->select('carts.id', 'carts.user_id', 'carts.product_id', 'carts.product_variation_id', 'carts.qty', 'products.title')
                                ->selectRaw('IFNULL(product_variants.original_price, products.original_price) as original_price')
                                ->selectRaw('IF(product_variants.original_price IS NULL, 0, 1) as is_variant')
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
                                return $item->qty * $item->original_price;
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
