<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoCollection;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoSearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:1|max:255'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Invalid query parameter', 400);
            }

            $query = $request->query('query');

            $videos = Video::where('status', 'published')
                ->where(function ($video) use ($query) {
                    $video->where('title', 'ILIKE', "%$query%")
                    ->orWhere('description', 'ILIKE', "%$query%")
                    ->orWhereJsonContains('tags', $query);
                })
            ->latest()
            ->paginate(5);

            // if ($videos->isEmpty()) {
            //     return response()->json(['message' => 'No videos found'], 404);
            // }

            return $this->respondWithMessageAndPayload(new VideoCollection($videos), 'Search data retrieved successfully.');
        }catch (\Throwable $e) {
            return response()->json(['error' => 'An error occurred', 'details' => $e->getMessage()], 500);
        }
    }
}
