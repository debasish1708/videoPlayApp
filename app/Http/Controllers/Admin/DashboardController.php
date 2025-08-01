<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    private $bunnyVideo;

    public function __construct()
    {
        $this->bunnyVideo = new \App\Services\BunnyVideoService();
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'videoId' => 'nullable|string|exists:videos,bunny_video_id',
        ]);
        try{
            if($request->ajax()){
                $query = Video::query();

                // Filter by access_type name from the relationship
                if ($request->has('columns') && isset($request->columns[4]['search']['value'])) {
                    $accessTypeName = $request->columns[4]['search']['value'];
                    info($accessTypeName);
                    if (!empty($accessTypeName)) {
                        $query->whereHas('accessType', function ($q) use ($accessTypeName) {
                            $q->where('name', $accessTypeName);
                        });
                    }
                }

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->editColumn('created_at',function($row){
                        return Carbon::parse($row->created_at)->format('d M Y h:i A') ?? 'N/A';
                    })
                    ->addColumn('web_url', function($row) {
                        return '<a href="' . $row->iframe_play_url . '" target="_blank">Watch Video</a>';
                    })
                    ->addColumn('access_type',function($row){
                        return $row->accessType->name;
                    })
                    ->rawColumns(['web_url'])
                    ->make(true);
            }
            
            $videoDetails = $this->bunnyVideo->getVideoDetails($request->input('videoId'));

            $videoDetailsOver7Days = $this->bunnyVideo->getVideoDetails($request->input('videoId'), now()->subDays(7)->toDateString(), now()->toDateString());

            if (!$videoDetails) {
                return redirect()->route('home')->with('error', 'Video details not found.');
            }

            $totalViewCount = 0;
            $totalViewCountOver7Days = 0;

            $totalViewCountWithCountry = [];
            $countryList = $this->getCountryNames();
            $countryFlagCodes = [];

            foreach ($videoDetailsOver7Days['countryViewCounts'] as $country => $views) {
                $totalViewCountOver7Days += $views;
            }

            foreach ($videoDetails['countryViewCounts'] as $code => $views) {
                $name = $countryList[$code] ?? $code;
                $totalViewCount += $views;
                $totalViewCountWithCountry[$name] = $views;
                $countryFlagCodes[$name] = strtolower($code); // e.g., 'India' => 'in'
            }

            // $totalViewCountWithCountry['United States'] = 3;
            // $countryFlagCodes['United States'] = 'us'; // Example for US flag code

            $totalWatchTime = 0;
            $totalWatchTimeWithCountry = [];
            $totalWatchTimeOver7Days = 0;

            foreach ($videoDetailsOver7Days['countryWatchTime'] as $country => $watchTime) {
                $totalWatchTimeOver7Days += $watchTime;
            }

            foreach ($videoDetails['countryWatchTime'] as $country => $watchTime) {
                $totalWatchTime += $watchTime;
                $name = $countryList[$country] ?? $country;
                $totalWatchTimeWithCountry[$name] = $watchTime;
            }

            // $totalWatchTimeWithCountry['United States'] = 150;

            $videoDetails['totalViewCount'] = $totalViewCount;
            $videoDetails['totalWatchTime'] = $totalWatchTime;
            $videoDetailsOver7Days['totalViewCount'] = $totalViewCountOver7Days;
            $videoDetailsOver7Days['totalWatchTime'] = $totalWatchTimeOver7Days;

            // dd($videoDetails);
            return view('content.pages.pages-home', compact('videoDetails', 'videoDetailsOver7Days','totalViewCountWithCountry', 'countryFlagCodes', 'totalWatchTimeWithCountry'));
        }catch(\Exception $e){
            info("Error in DashboardController: " . $e->getMessage());
            return redirect()->route('home')->with('error', 'An error occurred while fetching video details.');
        }
    }


    public function getCountryNames()
    {
        $countries = [
            'IN' => 'India',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'CA' => 'Canada',
            'DE' => 'Germany',
            'FR' => 'France',
            'AU' => 'Australia',
            'JP' => 'Japan',
        ];
        return $countries;
    }
}
