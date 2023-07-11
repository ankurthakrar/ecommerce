<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;  
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Tag;
use Validator;

class AdminController extends BaseController
{
    //

    // CATEGORY LIST

    public function categoryList()
    {
        try{
            $data['category_list'] = Categories::with(['parent:id,name,parent_id', 'children:id,name,parent_id'])->get();
            return $this->success($data,'Category list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // CATEGORY ADD

    public function categoryStore(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'name'       => 'required|string|max:255',
                'parent_id'  => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $input = $request->all();
            Categories::create($input);
            return $this->success([],'Category added successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
   
    // CATEGORY DETAILS

    public function categoryDetail(Request $request,$id)
    {
        try{
            if ($id < 1) {
                return $this->error('Please select valid category','Please select valid category');
            }
            $data['category_details'] = Categories::with(['parents:id,name,parent_id', 'childrens:id,name,parent_id'])->where('id',$id)->first();
            if(!empty($data['category_details'])){
                return $this->success($data,'Category details');
            }
            return $this->error('Category not found','Category not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // CATEGORY UPDATE

    public function categoryUpdate(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'category_id' => 'required',
                'name'        => 'required|string|max:255',
                'parent_id'   => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            } 

            $category_details = Categories::where('id',$request->category_id)->first();
            if(!empty($category_details)){
                $input = $request->all();
                $category_details->name      = $input['name'];
                $category_details->parent_id = $input['parent_id'];
                $category_details->save();
                $data['category_details'] = Categories::with(['parents:id,name,parent_id', 'childrens:id,name,parent_id'])->where('id',$request->category_id)->first();
                return $this->success($data,'Category updated successfully');
            }
            return $this->error('Category not found','Category not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // TAGS LIST

    public function tagList()
    {
        try{
            $data['tag_list'] = Tag::all();
            return $this->success($data,'Tag list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // TAGS ADD

    public function tagStore(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'name'       => 'required|string|max:255',
                'is_active'  => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $input = $request->all();
            Tag::create($input);
            return $this->success([],'Tag added successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
   
    // TAGS DETAILS

    public function tagDetail(Request $request,$id)
    {
        try{
            if ($id < 1) {
                return $this->error('Please select valid category','Please select valid category');
            }
            $data['tag_details'] = Tag::where('id',$id)->first();
            if(!empty($data['tag_details'])){
                return $this->success($data,'Tag details');
            }
            return $this->error('Tag not found','Tag not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // TAGS UPDATE

    public function tagUpdate(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'name'       => 'required|string|max:255',
                'is_active'  => 'required',
                'tag_id'     => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            } 

            $tag_details = Tag::where('id',$request->tag_id)->first();
            if(!empty($tag_details)){
                $input = $request->all();
                $tag_details->name      = $input['name'];
                $tag_details->is_active = $input['is_active'];
                $tag_details->save();
                $data['tag_details'] = Tag::where('id',$request->tag_id)->first();
                return $this->success($data,'Tag updated successfully');
            }

            return $this->error('Tag not found','Tag not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // PRODUCT LIST

    public function productList()
    {
        try{
            $data['product_list'] = Product::with(['parent:id,name,parent_id', 'children:id,name,parent_id'])->get();
            return $this->success($data,'Product list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // PRODUCT ADD

    public function productStore(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'title'       => 'required|string|max:255',
                'category_id' => 'required',
                'new_price'   => 'required'
                // 'discount'    => 'required',
                // 'tax'         => 'required',
                // 'original_price' => 'required',
                // 'minimum_stock'  => 'required',
                // 'tags'        => 'required',
                // 'description' => 'required',
                // 'description1'=> 'required',
                // 'description2'=> 'required'
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $input = $request->all();
            // Product::create($input);
            return $this->success([],'Product added successfully');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
   
    // PRODUCT DETAILS

    public function productDetail(Request $request,$id)
    {
        try{
            if ($id < 1) {
                return $this->error('Please select valid product','Please select valid product');
            }
            $data['prodct_details'] = Product::with(['parents:id,name,parent_id', 'childrens:id,name,parent_id'])->where('id',$id)->first();
            if(!empty($data['prodct_details'])){
                return $this->success($data,'Product details');
            }
            return $this->error('Product not found','Product not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // PRODUCT UPDATE

    public function productUpdate(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'category_id' => 'required',
                'name'        => 'required|string|max:255',
                'parent_id'   => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            } 

            $category_details = Categories::where('id',$request->category_id)->first();
            if(!empty($category_details)){
                $input = $request->all();
                $category_details->name      = $input['name'];
                $category_details->parent_id = $input['parent_id'];
                $category_details->save();
                $data['category_details'] = Categories::with(['parents:id,name,parent_id', 'childrens:id,name,parent_id'])->where('id',$request->category_id)->first();
                return $this->success($data,'Category updated successfully');
            }
            return $this->error('Category not found','Category not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // PRODUCT DELETE

    public function productDelete($id)
    {
        try{
            if ($id < 1) {
                return $this->error('Please select valid product','Please select valid product');
            }
            $data['category_details'] = Categories::with(['parents:id,name,parent_id', 'childrens:id,name,parent_id'])->where('id',$id)->first();
            if(!empty($data['category_details'])){
                return $this->success($data,'Category details');
            }
            return $this->error('Product not found','Product not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
}
