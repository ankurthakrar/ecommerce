<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;  
use App\Models\Categories;
use App\Models\Tag;

class GeneralController extends BaseController
{
    //
    // THIS WILL RETURN ALL PARENT CATEGORY

    public function getParentCategoryList()
    {
        try{
            $data['parent_category_list'] = Categories::where('parent_id',0)->select('id','name')->get();
            $data['active_parent_category_list'] = Categories::where('is_active',1)->where('parent_id',0)->select('id','name')->get();
            return $this->success($data,'All parent category list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
   
    // THIS WILL RETURN ALL SUB CATEGORY OF GIVEN PARENT
 
    public function getParentSubcategoryList($id)
    {
        try{
            if ($id < 1) {
                return $this->error('Please select valid category','Please select valid category');
            }
            $data['parent_all_sub_category_list'] = Categories::where('parent_id',$id)->select('id','name')->get();
            $data['parent_active_category_list'] = Categories::where('is_active',1)->where('parent_id',$id)->select('id','name')->get();
            return $this->success($data,'Parent`s sub category details');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
    
    // THIS WILL RETURN ALL ACTIVE    CATEGORY AND TAG

    public function getCategoryTagList()
    {
        try{
            $data['category_list'] = Categories::where('is_active',1)->where('parent_id',0)->with(['parent:id,name,parent_id', 'childrens:id,name,parent_id','image'])->get();
            $data['tag_list'] = Tag::where('is_active',1)->get();
            return $this->success($data,'Category and tag list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
}
