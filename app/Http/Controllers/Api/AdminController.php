<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;  
use Illuminate\Http\Request;
use App\Models\Categories;
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
}
