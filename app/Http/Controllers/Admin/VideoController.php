<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VideoStatus;
use App\Http\Requests\CreateVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Models\Video;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class VideoController extends Controller
{
    private $bunnyVideo;

    public function __construct()
    {
        $this->bunnyVideo = new \App\Services\BunnyVideoService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            if($request->ajax()){
                $videos = Video::latest()->get();
                return DataTables::of($videos)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return view('content.admin.videos.actions', [
                            'video' => $row,
                        ])->render();
                    })
                    ->editColumn('created_at',function($row){
                        return Carbon::parse($row->created_at)->format('d M Y h:i A') ?? 'N/A';
                    })
                    ->addColumn('web_url', function($row) {
                        return '<a href="' . $row->iframe_play_url . '" target="_blank">Watch Video</a>';
                    })
                    ->rawColumns(['actions', 'web_url'])
                    ->make(true);
            }
            return view('content.admin.videos.index');
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $access_types = \App\Models\AccessType::all();
        $genres = \App\Models\Genre::all();
        return view('content.admin.videos.create', compact('access_types', 'genres'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateVideoRequest $request)
    {
        try{
            $data = $request->validated();

            $file = $data['post_video'];
            $title = $data['title'];

            // Step 1: Create video entry
            $videoEntry = $this->bunnyVideo->createVideo($title);
            $videoId = $videoEntry['guid'] ?? null;
            if (!$videoEntry) {
                return back()->with('error', 'Failed to create video entry.')->withInput();
            }
            info("Video entry created successfully: " . json_encode($videoEntry, JSON_INVALID_UTF8_SUBSTITUTE));

            // Step 2: Upload binary
            info("Uploading video file to Bunny CDN: " . $file->getRealPath() . " for video ID: " . $videoId);
            $uploadResult = $this->bunnyVideo->uploadVideoFile($videoId, $file->getRealPath());
            if (!$uploadResult) {
                return back()->with('error', 'Failed to upload video file.')->withInput();
            }
            info("Video file uploaded successfully: " . $videoId);

            // Step 3: Get public URL
            // $url = $this->bunnyVideo->getPlaybackUrl($videoId);
            // info("Video uploaded successfully:". json_encode($url));

            $tags = [];
            if ($data['tags']) {
                $tags = collect(json_decode($data['tags']))->pluck('value')->toArray();
            }

            Video::create([
                'access_type_id' => $data['access_type_id'],
                'genre_id' => $data['genre_id'],
                'title' => $title,
                'description' => $data['description'],
                'bunny_video_id' => $videoId,
                'tags' => json_encode($tags)
            ]);

            return redirect()->route('videos.index')->with('success', 'Video uploaded successfully!');
        }catch(\Exception $e){
            info("Error in VideoController@store: " . $e->getMessage());
            return back()->with('error', 'An error occurred while uploading the video.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        try {
            if (!$video) {
                return back()->with('error', 'Video not found.')->withInput();
            }
            $access_type = $video->accessType;
            $genre = $video->genre;
            if (!$access_type) {
                return back()->with('error', 'Access Type not found for this video.')->withInput();
            }
            return view('content.admin.videos.show', compact('video', 'access_type', 'genre'));
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while fetching the video details.')->withInput();
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        try {
            $access_types = \App\Models\AccessType::all();
            $genres = \App\Models\Genre::all();
            if (!$video) {
                return back()->with('error', 'Video not found.')->withInput();
            }
            return view('content.admin.videos.edit', compact('video', 'access_types', 'genres'));
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while fetching the video details.')->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
        try {
            $data = $request->validated();

            // Handle tags
            $tags = [];
            if ($data['tags']) {
                $tags = collect(json_decode($data['tags']))->pluck('value')->toArray();
            }

            $video->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'access_type_id' => $data['access_type_id'],
                'genre_id' => $data['genre_id'],
                'tags' => json_encode($tags)
            ]);

            // Handle video file replacement if provided
            if (isset($data['post_video']) && $data['post_video']) {
                $file = $data['post_video'];

                // if($data['title']!== $video->title) {
                //    $this->bunnyVideo->updateVideoTitle($video->bunny_video_id, $data['title']);
                // }

                $this->bunnyVideo->deleteVideo($video->bunny_video_id);
                $videoEntry = $this->bunnyVideo->createVideo($data['title']);
                $videoId = $videoEntry['guid'] ?? null;
                $uploadResult = $this->bunnyVideo->uploadVideoFile($videoId, $file->getRealPath());
                if (!$uploadResult) {
                    return back()->with('error', 'Failed to upload new video file.')->withInput();
                }
                info("Video file updated successfully for video ID: " . $videoId);

                $video->update([
                    'bunny_video_id' => $videoId,
                ]);
            }

            return redirect()->route('videos.index')->with('success', 'Video updated successfully!');
        } catch (\Exception $e) {
            info("Error in VideoController@update: " . $e->getMessage());
            return back()->with('error', 'An error occurred while updating the video.')->withInput();
        }
    }

    public function publish(Request $request, Video $video)
    {
        try {
            $request->validate([
                'status' => 'required|boolean',
            ]);

            $video->status = $request->input('status') ? VideoStatus::PUBLISHED->value : VideoStatus::UNPUBLISHED->value;
            $video->save();

            return response()->json([
                'message' => 'Video ' . ucfirst($video->status) . " Successfully",
                'status' => $video->status
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy(Video $video)
    {
        try{
            $video->delete();
            return response()->json([
                'message' => 'Video deleted successfully'
            ], 200);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
