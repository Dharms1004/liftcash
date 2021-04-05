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
        $allOffers = DB::table('offer')->orderBy('OFFER_ID', 'desc')->limit($limit)->get();

        $hotOffers = DB::table('offer')->orderBy('OFFER_ID', 'desc')->where('OFFER_DISPLAY_TYPE',2)->limit($limit)->get();
        if(!empty($hotOffers)){
            foreach ($hotOffers as $hotOfferData) {
                $res['offerId'] = $hotOfferData->OFFER_ID;
                $res['offerAmount'] = $hotOfferData->OFFER_AMOUNT;
                $res['offerName'] = $hotOfferData->OFFER_NAME;
                $res['packageName'] = $hotOfferData->OFFER_PACKAGE;
                $res['payoutType'] = $hotOfferData->OFFER_NAME;
                $res['offerThumbnail'] = $hotOfferData->OFFER_THUMBNAIL;
                $res['offerBanner'] = $hotOfferData->OFFER_BANNER;
                $allHotOffersData[] = $res;
            }
        }else{
            $allHotOffersData = "N\A";
        }

        $reccomemndedOffers = DB::table('offer')->orderBy('OFFER_ID', 'desc')->where('OFFER_DISPLAY_TYPE',1)->limit($limit)->get();

        if(!empty($reccomemndedOffers)){
            foreach ($reccomemndedOffers as $reccomemndedOfferData) {
                $res['offerId'] = $reccomemndedOfferData->OFFER_ID;
                $res['offerAmount'] = $reccomemndedOfferData->OFFER_AMOUNT;
                $res['offerName'] = $reccomemndedOfferData->OFFER_NAME;
                $res['packageName'] = $reccomemndedOfferData->OFFER_PACKAGE;
                $res['payoutType'] = $reccomemndedOfferData->OFFER_NAME;
                $res['offerThumbnail'] = $reccomemndedOfferData->OFFER_THUMBNAIL;
                $res['offerBanner'] = $reccomemndedOfferData->OFFER_BANNER;
                $allReccomemndedOffersData[] = $res;
            }
        }else{
            $allReccomemndedOffersData = "N\A";
        }

        if (!empty($allOffers)) {
            $statusData['status'] = '200';
            $statusData['message'] = 'Success';
            $statusData['type'] = 'get_all_offers';
            foreach ($allOffers as $allOfferData) {
                $res['offerId'] = $allOfferData->OFFER_ID;
                $res['offerAmount'] = $allOfferData->OFFER_AMOUNT;
                $res['offerName'] = $allOfferData->OFFER_NAME;
                $res['packageName'] = $allOfferData->OFFER_PACKAGE;
                $res['payoutType'] = $allOfferData->OFFER_NAME;
                $res['offerThumbnail'] = $allOfferData->OFFER_THUMBNAIL;
                $res['offerBanner'] = $allOfferData->OFFER_BANNER;
                $allOffersData[] = $res;
            }

            $data = ['data' => $statusData, 'offers' => $allOffersData, 'hotOffers' => $allHotOffersData, 'reccomendedOffers' => $allReccomemndedOffersData];
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
            'offerId' => 'required',

        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $limit = $request->input('limit');
        $offerId = $request->input('offerId');
        $promotion = DB::table('offer')->where('OFFER_ID', $offerId)->orderBy('OFFER_ID', 'desc')->limit($limit)->get();
        if (!empty($promotion)) {
            $statusData['status'] = '200';
            $statusData['message'] = 'Success';
            $statusData['type'] = 'get_all_offers';
            foreach ($promotion as $promotionData) {
                $res['offerId'] = $promotionData->OFFER_ID;
                $res['offerAmount'] = $promotionData->OFFER_AMOUNT;
                $res['offerName'] = $promotionData->OFFER_NAME;
                $res['packageName'] = $promotionData->OFFER_PACKAGE;
                $res['payoutType'] = $promotionData->OFFER_NAME;
                $res['offerThumbnail'] = $promotionData->OFFER_THUMBNAIL;
                $res['offerBanner'] = $promotionData->OFFER_BANNER;
                $res['offerDetails'] = $promotionData->OFFER_DETAILS;
                $res['offerSteps'] = $promotionData->OFFER_STEPS;
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
