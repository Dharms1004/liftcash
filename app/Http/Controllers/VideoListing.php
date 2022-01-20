<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Promotions;
use App\Models\User;
use DB;

class VideoListing extends Controller
{
    public function getAllVideos(Request $request)
    {
        $videos = DB::table('video_listing')->orderBy('id','DESC')->get();

        if (count($videos)) {
            foreach ($videos as $video) {

                $res['videoTitle'] = $video->title;
                $res['videodesc'] = $video->desc;
                $res['videoBanner'] = env('VIDEO_BANNER_URL').$video->banner;
                $res['videoUrl'] = $video->video_url;
                $res['url'] = $video->url;
                $res['type'] = 'get_all_videos';
                $allVideos[] = $res;

            }

            $videoResult = ['status' => '200', 'message' => 'Success', 'type' => 'get_all_videos','videos' => $allVideos ?? []];

            return response($videoResult, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'No active videos found';
            $res['type'] = 'failed';
            return response($res);
        }
    }
}
