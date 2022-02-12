<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Promotions;
use App\Models\User;
use DB;

class MiniBanner extends Controller
{
    public function getAllBanners(Request $request)
    {
        $banners = DB::table('mini_banner')->where('STATUS', 1)->get();

        if (count($banners)) {
            foreach ($banners as $banner) {

                $res['bannerTitle'] = $banner->HEADING;
                $res['bannerImage'] = env('BANNERS_URL').$banner->THUMBNAIL;
                $res['bannerUrl'] = $banner->ACTION_URL;
                $res['type'] = 'get_all_banners';
                $allBanners[] = $res;

            }

            $bannerResult = ['status' => '200', 'message' => 'Success', 'banners' => $allBanners ?? []];

            return response($bannerResult, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'No active banners found';
            $res['type'] = 'failed';
            return response($res);
        }
    }
}
