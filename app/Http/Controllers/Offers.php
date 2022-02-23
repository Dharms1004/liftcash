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

        $allOffersData = [];
        $allHotOffersData = [];
        $allReccomemndedOffersData = [];
        $allspecialOffersData = [];
        $allSaleOffersData = [];
        
        $allOffers = DB::table('offer')->orderBy('OFFER_ID', 'desc')->where(['STATUS' => 1])->whereNotIn('OFFER_CATEGORY', [4,5])->limit($limit)->get();

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
                $res['offerInstructions'] = $allOfferData->OFFER_INSTRUCTIONS;
                $res['offerThumbnail'] = env('THUMB_URL').$allOfferData->OFFER_THUMBNAIL;
                $res['offerBanner'] = env('BANNER_URL').$allOfferData->OFFER_BANNER;
                $res['offerUrl'] = $allOfferData->OFFER_URL;
                $res['fallbackUrl'] = $allOfferData->FALLBACK_URL;
                $allOffersData[] = $res;
            }

           
        } else {
            $allOffersData = "N\A";
        }

        $hotOffers = DB::table('offer')->orderBy('OFFER_ID', 'desc')->where(['OFFER_DISPLAY_TYPE' => 3, 'STATUS' => 1])->limit($limit)->get();
       
        if(!empty($hotOffers)){
            foreach ($hotOffers as $hotOfferData) {
                $res['offerId'] = $hotOfferData->OFFER_ID;
                $res['offerAmount'] = $hotOfferData->OFFER_AMOUNT;
                $res['offerName'] = $hotOfferData->OFFER_NAME;
                $res['packageName'] = $hotOfferData->OFFER_PACKAGE;
                $res['payoutType'] = $hotOfferData->OFFER_NAME;
                $res['offerInstructions'] = $hotOfferData->OFFER_INSTRUCTIONS;
                $res['offerThumbnail'] = env('THUMB_URL').$hotOfferData->OFFER_THUMBNAIL;
                $res['offerBanner'] = env('BANNER_URL').$hotOfferData->OFFER_BANNER;
                $res['offerUrl'] = $hotOfferData->OFFER_URL;
                $res['fallbackUrl'] = $hotOfferData->FALLBACK_URL;
                $allHotOffersData[] = $res;
            }
        }else{
            $allHotOffersData = "N\A";
        }

        $reccomemndedOffers = DB::table('offer')->orderBy('OFFER_ID', 'desc')->where(['OFFER_DISPLAY_TYPE' => 2, 'STATUS' => 1])->limit($limit)->get();

        if(!empty($reccomemndedOffers)){
            foreach ($reccomemndedOffers as $reccomemndedOfferData) {
                $res['offerId'] = $reccomemndedOfferData->OFFER_ID;
                $res['offerAmount'] = $reccomemndedOfferData->OFFER_AMOUNT;
                $res['offerName'] = $reccomemndedOfferData->OFFER_NAME;
                $res['packageName'] = $reccomemndedOfferData->OFFER_PACKAGE;
                $res['payoutType'] = $reccomemndedOfferData->OFFER_NAME;
                $res['offerInstructions'] = $reccomemndedOfferData->OFFER_INSTRUCTIONS;
                $res['offerThumbnail'] = env('THUMB_URL').$reccomemndedOfferData->OFFER_THUMBNAIL;
                $res['offerBanner'] = env('BANNER_URL').$reccomemndedOfferData->OFFER_BANNER;
                $res['offerUrl'] = $reccomemndedOfferData->OFFER_URL;
                $res['fallbackUrl'] = $reccomemndedOfferData->FALLBACK_URL;
                $allReccomemndedOffersData[] = $res;
            }
        }else{
            $allReccomemndedOffersData = "N\A";
        }

        $specialOffers = DB::table('offer')->orderBy('OFFER_ID', 'desc')->where(['OFFER_DISPLAY_TYPE' => 4, 'STATUS' => 1])->limit($limit)->get();

        if(!empty($specialOffers)){
            foreach ($specialOffers as $specialOffersData) {
                $res['offerId'] = $specialOffersData->OFFER_ID;
                $res['offerAmount'] = $specialOffersData->OFFER_AMOUNT;
                $res['offerName'] = $specialOffersData->OFFER_NAME;
                $res['packageName'] = $specialOffersData->OFFER_PACKAGE;
                $res['payoutType'] = $specialOffersData->OFFER_NAME;
                $res['offerInstructions'] = $specialOffersData->OFFER_INSTRUCTIONS;
                $res['offerThumbnail'] = env('THUMB_URL').$specialOffersData->OFFER_THUMBNAIL;
                $res['offerBanner'] = env('BANNER_URL').$specialOffersData->OFFER_BANNER;
                $res['offerUrl'] = $specialOffersData->OFFER_URL;
                $res['fallbackUrl'] = $specialOffersData->FALLBACK_URL;
                $allspecialOffersData[] = $res;
            }
        }else{
            $allspecialOffersData = "N\A";
        }

        $saleOffers = DB::table('offer')->orderBy('OFFER_ID', 'desc')->where(['OFFER_DISPLAY_TYPE' => 6, 'STATUS' => 1])->whereIn('OFFER_CATEGORY', [4,5])->limit($limit)->get();

        if(!empty($saleOffers)){
            foreach ($saleOffers as $saleOffersData) {
                $res['offerId'] = $saleOffersData->OFFER_ID;
                $res['offerAmount'] = $saleOffersData->OFFER_AMOUNT;
                $res['offerName'] = $saleOffersData->OFFER_NAME;
                $res['packageName'] = $saleOffersData->OFFER_PACKAGE;
                $res['payoutType'] = $saleOffersData->OFFER_NAME;
                $res['offerInstructions'] = $saleOffersData->OFFER_INSTRUCTIONS;
                $res['offerThumbnail'] = env('THUMB_URL').$saleOffersData->OFFER_THUMBNAIL;
                $res['offerBanner'] = env('BANNER_URL').$saleOffersData->OFFER_BANNER;
                $res['offerUrl'] = $saleOffersData->OFFER_URL;
                $res['fallbackUrl'] = $saleOffersData->FALLBACK_URL;
                $allSaleOffersData[] = $res;
            }
        }else{
            $allSaleOffersData = "N\A";
        }

        $statusData['status'] = '200';
        $statusData['message'] = 'Success';
        $statusData['type'] = 'get_all_offers';
        $data = ['data' => $statusData, 'offers' => $allOffersData, 'hotOffers' => $allHotOffersData, 'reccomendedOffers' => $allReccomemndedOffersData, 'specialOffers' => $allspecialOffersData, 'saleOffers' => $allSaleOffersData];
        return response($data, 200);
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
        $promotion = DB::table('offer')->where('OFFER_ID', $offerId)->orderBy('OFFER_ID', 'desc')->limit($limit)->first();
        if (!empty($promotion)) {
            $statusData['status'] = '200';
            $statusData['message'] = 'Success';
            $statusData['type'] = 'get_single_offers';

                $res['offerId'] = $promotion->OFFER_ID;
                $res['offerAmount'] = $promotion->OFFER_AMOUNT;
                $res['offerName'] = $promotion->OFFER_NAME;
                $res['packageName'] = $promotion->OFFER_PACKAGE;
                $res['payoutType'] = $promotion->OFFER_NAME;
                $res['offerThumbnail'] = env('THUMB_URL').$promotion->OFFER_THUMBNAIL;
                $res['offerBanner'] = env('BANNER_URL').$promotion->OFFER_BANNER;
                $res['offerDetails'] = $promotion->OFFER_DETAILS;
                $res['offerInstructions'] = $promotion->OFFER_INSTRUCTIONS;
                $res['offerUrl'] = $promotion->OFFER_URL;
                $res['fallbackUrl'] = $promotion->FALLBACK_URL;
                $res['offerSteps'] = $this->createSteps($promotion->OFFER_STEPS);/** convert into readable steps */
                $promoData[] = $res;
           
                $data = ['data' => $statusData, 'offers' => $promoData];
            return response($data, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'get_all_offers';
            return response($res);
        }
    }

    public function createSteps($steps){

        $allSteps = json_decode($steps);
        $newSteps = [];
        foreach($allSteps as $singleStep){
            $stepResult = explode("@#",$singleStep->offerSteps);
            $newSteps[] = [
                "propertyName" => $stepResult[0] ?? "N\A",
                "propertyValue" => $stepResult[1] ?? 0
            ];
        }
        return $newSteps;
    }
}
