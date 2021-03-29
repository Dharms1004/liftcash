<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Promotions;
use App\Models\User;
use DB;

class Offers extends Controller
{
    public function getAllOffer(Request $request)
    {
        $rules = [
            'limit' => 'required|max:100'

        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $limit = $request->input('limit');
        $promotion = DB::table('offer')->orderBy('OFFER_ID', 'desc')->limit($limit)->get();
        if (!empty($promotion)) {
            $statusData['status'] = '200';
            $statusData['message'] = 'Success';
            $statusData['type'] = 'get_all_offers';
            foreach ($promotion as $promotionData) {
                $res['offerId'] = $promotionData->OFFER_CATEGORY;
                $res['offerAmount'] = $promotionData->OFFER_CATEGORY;
                $res['offerName'] = $promotionData->OFFER_NAME;
                $res['packageName'] = $promotionData->OFFER_NAME;
                $res['payoutType'] = $promotionData->OFFER_NAME;
                $res['imageUrl'] = $promotionData->OFFER_BANNER;
                $promoData[] = $res;
            }

            $data = ['data' => $statusData, 'offers' => $promoData];
            return response($data, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'get_all_offers';
            return response($res);
        }
    }
    public function getOfferDetails(Request $request)
    {
        $rules = [
            'limit' => 'required|max:100',
            'offerId' => 'required|max:100',

        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $limit = $request->input('limit');
        $offerId = $request->input('offerId');
        $promotion = DB::table('offer')->where('OFFER_CATEGORY', $offerId)->orderBy('OFFER_ID', 'desc')->limit($limit)->get();
        if (!empty($promotion)) {
            $statusData['status'] = '200';
            $statusData['message'] = 'Success';
            $statusData['type'] = 'get_all_offers';
            foreach ($promotion as $promotionData) {
                $res['offerId'] = $promotionData->OFFER_CATEGORY;
                $res['offerAmount'] = $promotionData->OFFER_CATEGORY;
                $res['offerName'] = $promotionData->OFFER_NAME;
                $res['packageName'] = $promotionData->OFFER_NAME;
                $res['payoutType'] = $promotionData->OFFER_NAME;
                $res['imageUrl'] = $promotionData->OFFER_BANNER;
                $promoData[] = $res;
            }

            $data = ['data' => $statusData, 'offers' => $promoData];
            return response($data, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'get_all_offers';
            return response($res);
        }
    }
}
