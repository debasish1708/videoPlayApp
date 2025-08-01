<?php

namespace App\Http\Controllers\Api;

use App\Enums\VideoStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoCollection;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExploreController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $search = $request->query('search');
            $genreSlug = $request->route('genre'); // or $request->search('genre') if using search param

            $videos = Video::query()->where('status', VideoStatus::PUBLISHED->value);

            $is_search = false;

            // Filter by genre if provided
            if ($genreSlug) {
                $genre = Genre::where('slug', $genreSlug)->first();

                if (!$genre) {
                    return response()->json(['message' => 'Genre not found'], 404);
                }

                $videos->where('genre_id', $genre->id);
            }

            // Apply search if search is provided
            if ($search) {
                $validator = Validator::make(['search' => $search], [
                    'search' => 'required|string|min:1|max:255'
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => 'Invalid search parameter'], 400);
                }
                $is_search = true;

                 $videos->where(function ($video) use ($search) {
                    $video->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw("LOWER(tags::text) LIKE ?", ['%' . strtolower($search) . '%']);
                });
            }

            $paginated = $videos->latest()->paginate(5)->appends($request->query());

            // 🧠 Fallback to global search if genre + search has no results
            if ($search && $genre && $paginated->isEmpty()) {
               $videos = Video::query()
                    ->where('status', VideoStatus::PUBLISHED->value)
                    ->where(function ($video) use ($search) {
                        $video->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%'])
                            ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%'])
                            ->orWhereRaw("LOWER(tags::text) LIKE ?", ['%' . strtolower($search) . '%']);
                    });

                $paginated = $videos->latest()->paginate(5)->appends($request->query());

                return $this->respondWithMessageAndPayload(new VideoCollection($paginated), 'No results in selected genre, showing all matching videos.');
            }
            $success_message = $is_search ? 'Search results retrieved successfully.' : 'Explore data retrieved successfully.';
            return $this->respondWithMessageAndPayload(new VideoCollection($paginated), $success_message);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'An error occurred', 'details' => $e->getMessage()], 500);
        }
    }
}
