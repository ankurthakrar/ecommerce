<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;  
use App\Models\Categories;
use App\Models\City;
use App\Models\Product;
use App\Models\State;
use App\Models\Tag;
use Validator;

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
            $data['brand_list'] = Product::where('brand','!=',null)->groupBy('brand')->pluck('brand');
            $data['variant_list'] = Product::where('version','!=',null)->groupBy('version')->pluck('version');
            return $this->success($data,'Category and tag list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // GET STATE LIST

    public function getStateList(){
        try{
            $data['state_list'] = State::select('id','name')->get();
            return $this->success($data,'State list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
    
    // GET CITY LIST

    public function getCityList($id){
        try{
            $data['city_list'] = City::select('id','name','state_id')->where('state_id',$id)->get();
            return $this->success($data,'City list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
    
    // GET PRODUCT LIST

    public function getProductList(Request $request){
        try{
            $validateData = Validator::make($request->all(), [
                'sort_by'       => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            } 

            $sorting = $request->input('sort_by', 'new'); 

            $query   = Product::select('id', 'title', 'is_active','final_price','original_price','tax','discount','tags','brand');
            if ($sorting == 'popularity') {
                // Pending
            } elseif ($sorting == 'price_low_to_high') {
                $query->orderByRaw("CAST(final_price AS DECIMAL(10, 2)) ASC");
            } elseif ($sorting == 'price_high_to_low') {
                $query->orderByRaw("CAST(final_price AS DECIMAL(10, 2)) DESC");
            } else {
                $query->orderBy('created_at', 'desc');
            }

            if (isset($request->categories_id)) {
                $categoryIdArray = $request->categories_id;
                $query->where(function ($q) use ($categoryIdArray) {
                    foreach ($categoryIdArray as $categoryId) {
                        $q->orWhereRaw("FIND_IN_SET('$categoryId', category_id) > 0");
                    }
                });
            }

            if (isset($request->tags_id)) {
                $tagsIdArray = $request->tags_id;
                $query->where(function ($q) use ($tagsIdArray) {
                    foreach ($tagsIdArray as $tagId) {
                        $q->orWhereRaw("FIND_IN_SET('$tagId', tags) > 0");
                    }
                });
            }

            if (isset($request->brand)) {
                $brandIdArray = $request->brand;
                $query->whereIn('brand',$brandIdArray);
            }
          
            if (isset($request->version)) {
                $versionIdArray = $request->version;
                $query->whereIn('version',$versionIdArray);
            }

            if (isset($request->min_budget) && isset($request->max_budget)) {
                $query->whereBetween('final_price', [$request->min_budget, $request->max_budget]);
            }

            $product_list = $query->paginate($request->input('perPage'), ['*'], 'page', $request->input('page'));

            $data['product_list']  =  $product_list->values();
            $data['current_page']  =  $product_list->currentPage();
            $data['per_page']      =  $product_list->perPage();
            $data['total']         =  $product_list->total();
            $data['last_page']     =  $product_list->lastPage();
            return $this->success($data,'Product list');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
}
