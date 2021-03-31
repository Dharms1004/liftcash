<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Promotions;
use App\Models\User;
use DB;

class Promotion extends Controller
{
    public function getAllPromotion(Request $request)
    {
        $promotion = DB::table('promotions')->get();;
        if ($promotion) {


            foreach ($promotion as $promotionData) {
                $res['status'] = '200';
                $res['message'] = 'Success';
                $res['promoType'] = $promotionData->PROMOTION_TYPE;
                $res['promoCat'] = $promotionData->PROMOTION_CATEGORY;
                $res['promoName'] = $promotionData->PROMOTION_NAME;
                $res['PromoDetails'] = $promotionData->PROMOTION_DETAILS;
                $res['promoSteps'] = $promotionData->PROMOTION_STEPS;
                $res['PromoThumb'] = $promotionData->PROMOTION_THUMBNAIL;
                $res['PromoBanner'] = $promotionData->PROMOTION_BANNER;
                $res['type'] = 'get_all_promotion';
                $promoData[] = ['data' => $res];
            }
            
            return response($promoData, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'get_all_promotion';
            return response($res);
        }
    }
}
