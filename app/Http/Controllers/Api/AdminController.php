<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\File;
use App\Http\Controllers\BaseController;  
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tag;
use Validator;

class AdminController extends BaseController
{
    //

    // CATEGORY LIST

    public function categoryList(Request $request)
    {
        try{
            $category_list = Categories::paginate($request->input('perPage'), ['*'], 'page', $request->input('page'));

            $data['category_list'] =  $category_list->values();
            $data['current_page']  =  $category_list->currentPage();
            $data['per_page']      =  $category_list->perPage();
            $data['total']         =  $category_list->total();
            $data['last_page']     =  $category_list->lastPage();

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
                'name'       => 'required|string|max:255|unique:categories',
                'parent_id'  => 'required',
                'is_active'  => 'required',
                'image'      => 'sometimes|file|mimes:jpeg,png,jpg|max:100000',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            
            $input = $request->all();
            $cat_data = Categories::create($input);
           
            $folderPath = public_path().'/category_image';

            $image = $request->file('image');
            $image_data = [];
            if (!empty($image)) {
                $image_data[] = $this->addImage($image,$cat_data->id,'ch_','category_image',$folderPath);
                Image::insert($image_data);
            }
            
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
                'category_id'       => 'required',
                'name'              => 'required|string|max:255|unique:categories,name,'.$request->category_id,
                'parent_id'         => 'required',
                'is_active'         => 'required',
                'is_image_change'   => 'required',
                'image'             => 'sometimes|file|mimes:jpeg,png,jpg|max:100000',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            } 

            $category_details = Categories::where('id',$request->category_id)->first();
            if(!empty($category_details)){
                $input = $request->all();
                $category_details->name      = $input['name'];
                $category_details->parent_id = $input['parent_id'];
                $category_details->is_active = $input['is_active'];
                $category_details->save();

                $folderPath = public_path().'/user_profile';
                
                if($request->is_image_change == 1 || $request->is_image_change == 2){
                    $category_image = Image::where('type_id', $request->category_id)->where('type','category_image');
                    $category_old_image = $category_image->pluck('file_name')->toArray();
                    if(isset($category_old_image[0])){
                        $path = public_path('category_image/' . $category_old_image[0]);
                        if (File::exists($path)) {
                            if (!is_writable($path)) {
                                chmod($path, 0777);
                            }
                            File::delete($path);
                            $deletedFiles[] = $path;
                        }
                        $category_image->delete();
                    }
                } 

                if (isset($request->image)) {
                    $image = $request->file('image');
                    $image_data = [];
                    if (!empty($image)) {
                        $image_data[] = $this->addImage($image,$request->category_id,'ch_','category_image',$folderPath);
                        Image::insert($image_data);
                    }
                }

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

    public function tagList(Request $request)
    {
        try{
            $tag_list = Tag::paginate($request->input('perPage'), ['*'], 'page', $request->input('page'));

            $data['tag_list']      =  $tag_list->values();
            $data['current_page']  =  $tag_list->currentPage();
            $data['per_page']      =  $tag_list->perPage();
            $data['total']         =  $tag_list->total();
            $data['last_page']     =  $tag_list->lastPage();
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
                'name'       => 'required|string|max:255|unique:tags',
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
                'name'       => 'required|string|max:255|unique:tags,name,'.$request->tag_id,
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

    public function productList(Request $request)
    {
        try{
            $product_list = Product::select('id','title','is_active')->paginate($request->input('perPage'), ['*'], 'page', $request->input('page'));

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

    // PRODUCT ADD 

    public function productStore(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                'title'       => 'required|string|max:255|unique:products',
                'category_id' => 'required',
                'final_price'   => 'required',
                'brand'   => 'required'
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }

            $input = $request->all();
            $input['brand'] = ucfirst($input['brand']);

            $finalPrice = $input['final_price'];
            $taxPercentage = isset($input['tax']) ? $input['tax'] : 0;
            $discountPercentage = isset($input['discount']) ? $input['discount'] : 0;

            $data = $this->getOriginalAmount($finalPrice,$taxPercentage,$discountPercentage);

            $input = array_merge($input,$data);
            $product_data = Product::create($input);

            $product_id = $product_data['id'];
            
            $folderPath = public_path().'/product_image';

            $image = $request->file('image');
            
            $image_data = [];
            if (!empty($image)) {
                $image_data = $this->uploadMediaFiles($image,$product_id,'pro_','product_image',$folderPath);
                Image::insert($image_data);
            }
            
            $product_variant = isset($input['variant']) ? $input['variant'] : null;
            $variant_array = [];
            if(!empty($product_variant)){
                $product_data->update(['is_varient' => 1]);
                foreach($product_variant as $key=>$variant){
                    $finalPrice = $variant['final_price'];
                    $taxPercentage = isset($variant['tax']) ? $variant['tax'] : 0;
                    $discountPercentage = isset($variant['discount']) ? $variant['discount'] : 0;
        
                    $data = $this->getOriginalAmount($finalPrice,$taxPercentage,$discountPercentage);
        
                    $variant = array_merge($variant,$data);

                    $variant['product_id'] = $product_id; 
                    
                    $variantModel = ProductVariant::create($variant);

                    $image_data = [];
                    $image = isset($variant['image']) ? $variant['image'] : null;
                    if (!empty($image)) {
                        $folderPath = public_path().'/product_variant_image';
                        $image_data = $this->uploadMediaFiles($image,$variantModel->id,'pro_vari_','product_variant_image',$folderPath);
                        Image::insert($image_data);
                        unset($variant['image']);
                    }
                    
                    $variant_array[] = $variant;
                } 
            }

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
            $data['prodct_details'] = Product::with(['variant'])->where('id',$id)->first();
            if(!empty($data['prodct_details'])){
                $data['prodct_details']['category_id_array'] = explode(',',$data['prodct_details']['category_id']);
                $data['prodct_details']['tags_array'] = explode(',',$data['prodct_details']['tags']);
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
                'title'       => 'required|string|max:255|unique:products,title,'.$request->product_id,
                'product_id' => 'required',
                'category_id' => 'required',
                'final_price'   => 'required',
                'brand'   => 'required'
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            } 

            $product_details = Product::where('id',$request->product_id)->first();
            if(!empty($product_details)){
                $input = $request->all();
                $input['brand'] = ucfirst($input['brand']);
                
                $finalPrice = $input['final_price'];
                $taxPercentage = isset($input['tax']) ? $input['tax'] : 0;
                $discountPercentage = isset($input['discount']) ? $input['discount'] : 0;
    
                $data = $this->getOriginalAmount($finalPrice,$taxPercentage,$discountPercentage);
    
                $input = array_merge($input,$data);
                $product_data = $product_details->update($input);
    
                $folderPath = public_path().'/product_image';
    
                $image = $request->file('image');
                
                $image_data = [];
                if(isset($input['change_image_id'])){
                    $change_image_ids = explode(',', $input['change_image_id']);
                    $old_images = Image::whereIn('id', $change_image_ids)->get();

                    // Delete images and their corresponding files
                    foreach ($old_images as $old_image) {
                        $fileName = $old_image->file_name;
                        $old_image->delete();
                
                        // Delete file from the product_image folder
                        $filePath = public_path().'/product_image/'.$fileName;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }

                if (!empty($image)) {
                    $image_data = $this->uploadMediaFiles($image,$request->product_id,'pro_','product_image',$folderPath);
                    Image::insert($image_data);
                }
                
                $product_variant = isset($input['variant']) ? $input['variant'] : null;
                $variant_array = [];
                $product_details->update(['is_varient' => 0]);
                $existingVariantIds = [];

                if(!empty($product_variant)){
                    $product_details->update(['is_varient' => 1]);
                    foreach($product_variant as $key=>$variant){
                        $variantId = isset($variant['id']) ? $variant['id'] : null;

                        $finalPrice = $variant['final_price'];
                        $taxPercentage = isset($variant['tax']) ? $variant['tax'] : 0;
                        $discountPercentage = isset($variant['discount']) ? $variant['discount'] : 0;
            
                        $data = $this->getOriginalAmount($finalPrice,$taxPercentage,$discountPercentage);
            
                        $variant = array_merge($variant,$data);
    
                        $variant['product_id'] = $request->product_id; 
                        
                        if ($variantId) {
                            // Update existing variant
                            $variantModel = ProductVariant::where('id', $variantId)->update(\Arr::except($variant, ['image', 'change_image_id']));
                            $existingVariantIds[] = $variantId;
                        } else {
                            // Insert new variant
                            $variantModel = ProductVariant::create($variant);
                            $variantId = $variantModel->id;
                            $existingVariantIds[] = $variantModel->id;
                        } 
    
                        $image_data = [];
                        if(isset($variant['change_image_id'])){
                            $change_image_ids = explode(',', $variant['change_image_id']);
                            $old_images = Image::whereIn('id', $change_image_ids)->get();
        
                            // Delete images and their corresponding files
                            foreach ($old_images as $old_image) {
                                $fileName = $old_image->file_name;
                                $old_image->delete();
                        
                                // Delete file from the product_variant_image folder
                                $filePath = public_path().'/product_variant_image/'.$fileName;
                                if (file_exists($filePath)) {
                                    unlink($filePath);
                                }
                            }
                        }

                        $image = isset($variant['image']) ? $variant['image'] : null;
                        if (!empty($image)) {
                            $folderPath = public_path().'/product_variant_image';
                            $image_data = $this->uploadMediaFiles($image,$variantId,'pro_vari_','product_variant_image',$folderPath);
                            Image::insert($image_data);
                            unset($variant['image']);
                        }
                        
                        $variant_array[] = $variant;
                    } 
                }
                // Remove variants that are not in the updated variants
                ProductVariant::where('product_id', $request->product_id)->whereNotIn('id', $existingVariantIds)->delete();
                
                return $this->success($data,'Product updated successfully');
            }
            return $this->error('Product not found','Product not found');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
}
