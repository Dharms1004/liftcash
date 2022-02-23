<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Promotions;
use App\Models\User;
use DB;
use Cache;

class NewsController extends Controller
{
    public function getAllNews(Request $request)
    {
        $rules = [
            'category' => 'required|in:business,entertainment,general,health,science,sports,technology'
        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];

        $this->validate($request, $rules, $customMessages);

        $country = "&country=in";
        $language = "&language=en";
        $category = "&category=$request->category";
        $catName = $request->category;
        $params = $category.$language;
        // $newNews = $this->getNewsFromVendor($params);
        // Cache::flush();
        if (Cache::has($catName))
        {
            $allOffer = Cache::get($catName);

        }else{
            $newNews = $this->getNewsFromVendor($params);
            Cache::put($catName, $newNews, 180);
            $allOffer = Cache::get($catName);
        }

        $news = json_decode($allOffer, true);
        // dd($news);
        if($news['status'] == "ok"){
            $statusData['status'] = '200';
            $statusData['message'] = 'Success';
            $statusData['type'] = 'get_all_news';
            $statusData['category'] = $catName;
            $statusData['news'] = $news['articles'] ?? [];
            return response($statusData, 200);
        }else{
            $statusData['status'] = '200';
            $statusData['message'] = 'failed to get news';
            $statusData['category'] = $catName;
            $statusData['news'] = [];
            return response($statusData, 422);
        }

    }

    private function getNewsFromVendor($params){

        $url = env('NEWS_URL');
        $endPoint =  env('NEWS_ENDPOINT');
        $apiKey =  env('NEWS_API_KEY');

        $finalUrl = $url.$endPoint."?apiKey=".$apiKey.$params;
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
        CURLOPT_URL => $finalUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
