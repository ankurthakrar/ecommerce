<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    //
    public function success($data = [], $message = null, $code = 200)
	{
		return response()->json([
			'status'=> true, 
			'message' => $message, 
			'data' => $data
		], $code);
	}

	public function error($errors = [],$message = null, $code = 400)
	{
		return response()->json([
			'status'=> false,
			'message' => $message,
			'data' => $errors
		], $code);
	}

	public function uploadMediaFiles($image,$id,$shortcut,$type,$folder_path)
	{
		$image_data = [];
	
		foreach ($image as $media) {
			if (!is_dir($folder_path)) {
				mkdir($folder_path, 0777, true);
			}
			$extension  = $media->getClientOriginalExtension();
			$filename = $shortcut.$id.'_'.random_int(10000, 99999). '.' . $extension;
			$media->move(public_path($type), $filename);
	
			$image_data[] = [
				'type_id'=> $id,
				'file_name'=> $filename,
				'type'=> $type,
				'created_at'=> now(),
				'updated_at'=> now(),
			];
		}
	
		return $image_data;
	}

	public function addImage($image,$id,$shortcut,$type,$folder_path){
		if (!is_dir($folder_path)) {
			mkdir($folder_path, 0777, true);
		}
		$extension  = $image->getClientOriginalExtension();
		$filename = $shortcut.$id.'_'.random_int(10000, 99999). '.' . $extension;
		$image->move(public_path($type), $filename);

		return $data = [
			'type_id'=> $id,
			'file_name'=> $filename,
			'type'=> $type,
			'created_at'=> now(),
			'updated_at'=> now(),
		];
	}
	
	public function getOriginalAmountOld($finalPrice,$taxPercentage,$discountPercentage){
		// Calculate tax amount
		$taxRate = $taxPercentage / 100;
		$taxAmount = round($finalPrice / (1 + $taxRate) * $taxRate, 2);
		$without_tax_amount = $finalPrice - $taxAmount;

		// Calculate discount amount
		$discountRate = $discountPercentage / 100;
		$discountAmount = round($without_tax_amount * $discountRate, 2);

		// Calculate original amount
		$originalAmount = $without_tax_amount - $discountAmount;

		return $data = [
			'tax'=>$taxPercentage,
			'discount'=>$discountPercentage,
			'tax_amount'=> $taxAmount,
			'discount_amount'=> $discountAmount,
			'original_price'=> $originalAmount,
		];
	}
	
	public function getOriginalAmount($finalPrice,$taxPercentage,$discountPercentage){
		// Calculate discount amount
		$discountRate = $discountPercentage / 100;
		$discountAmount = round($finalPrice * $discountRate, 2);

		$after_discount_amount = $finalPrice - $discountAmount;

	    // Calculate tax amount
		$taxRate = $taxPercentage / 100;
		$taxAmount = round($after_discount_amount / (1 + $taxRate) * $taxRate, 2);
		$without_tax_amount = $after_discount_amount - $taxAmount;
		return $data = [
			'tax'=>$taxPercentage,
			'discount'=>$discountPercentage,
			'tax_amount'=> $taxAmount,
			'discount_amount'=> $discountAmount,
			'original_price'=> $without_tax_amount,
			'after_discount_amount'=> $after_discount_amount,
		];
	}

}
