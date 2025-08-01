<?php

namespace App\Http\Controllers\Api;

use App\Enums\VideoStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\HomeCollection;
use App\Http\Resources\HomeResource;
use App\Models\Banner;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try{

            $banners = Banner::latest()->limit(5)->get();
            $videos = Video::where('status', VideoStatus::PUBLISHED->value)->latest()->limit(5)->get();

            $most_popular = Video::where('status',VideoStatus::PUBLISHED->value)
                                ->orderBy('views','desc')
                                ->limit(5)
                                ->get();

            $HomeData = [
               'banners' => $banners,
               'videos' => $videos,
               'most_popular' => $most_popular
            ];

            return $this->respondWithMessageAndPayload(new HomeCollection($HomeData), 'Dashboard data retrieved successfully.');
        }catch(\Throwable $e){
            return response()->json(['error' => 'An error occurred', 'details' => $e->getMessage()], 500);
        }
    }
}
