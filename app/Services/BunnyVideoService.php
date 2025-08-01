<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BunnyVideoService
{
    private $accessKey;
    private $libraryId;
    private $baseUrl;

    public function __construct()
    {
        $this->accessKey = config('constant.bunny.access_key');
        $this->libraryId = config('constant.bunny.library_id');
        $this->baseUrl = config('constant.bunny.base_url.video');
    }

    private function getHeaders($contentType = 'application/json')
    {
        $headers = [
            'accept' => 'application/json',
            'AccessKey' => $this->accessKey,
        ];
        if ($contentType) {
            $headers['Content-Type'] = $contentType;
        }
        return $headers;
    }

    public function createVideo($title)
    {
        try {
            $response = Http::withHeaders(
                $this->getHeaders('application/json')
            )->post("{$this->baseUrl}/videos", [
                'title' => $title,
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            info("Error in BunnyVideoService@createVideo: " . $e->getMessage());
            return null;
        }
    }

    public function uploadVideoFile($videoId, $filePath)
    {
        try {
            info("Uploading video file to Bunny CDN: $filePath for video ID: $videoId");

            $response = Http::withHeaders(
                $this->getHeaders('video/mp4')
            )->withBody(
                file_get_contents($filePath), 'video/mp4'
            )->put(
                "{$this->baseUrl}/videos/{$videoId}",
            );

            if ($response->failed()) {
                info("Bunny upload failed", [
                    'status' => $response->status(),
                    'body_excerpt' => mb_substr($response->body(), 0, 200),
                ]);
                return false;
            }

            return $response->successful();
        } catch (\Exception $e) {
            info("Error in BunnyVideoService@uploadVideoFile: " . $e->getMessage());
            return false;
        }
    }

    public function updateVideoTitle($videoId, $newTitle)
    {
        try {
            $response = Http::withHeaders($this->getHeaders('application/json'))->post(
                "{$this->baseUrl}/videos/{$videoId}",
                ['title' => $newTitle]
            );

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error("Bunny update video title failed: " . $e->getMessage());
            return false;
        }
    }

    public function deleteVideo($videoId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders())->delete("{$this->baseUrl}/videos/{$videoId}");

            return $response->successful();
        } catch (\Exception $e) {
            info("Error in BunnyVideoService@deleteVideo: " . $e->getMessage());
            return false;
        }
    }

    public function getPlaybackUrl($videoId)
    {
        try {
            $url['web_url'] = "https://iframe.mediadelivery.net/play/{$this->libraryId}/{$videoId}";

            $response = Http::withHeaders(
                $this->getHeaders(null)
            )->get("{$this->baseUrl}/videos/{$videoId}/play");
            if ($response->failed()) {
                info("Failed to get playback URL for video ID: $videoId", [
                    'status' => $response->status(),
                    'body_excerpt' => mb_substr($response->body(), 0, 200),
                ]);
                return null;
            }
            $url['mobile_url'] = $response->json()['videoPlaylistUrl'];
            info("Video play data retrieved successfully.", ["video_url" => $url['web_url'], "mobile_url" => $url['mobile_url']]);

            return $url;
        } catch (\Exception $e) {
            info("Error in BunnyVideoService@getPlaybackUrl: " . $e->getMessage());
            return null;
        }
    }

    public function getVideoDetails($videoId = null, $dateFrom = null, $dateTo = null)
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/statistics?videoGuid={$videoId}&dateFrom={$dateFrom}&dateTo={$dateTo}");

            if ($response->failed()) {
                info("Failed to get video details for ID: $videoId", [
                    'status' => $response->status(),
                    'body_excerpt' => mb_substr($response->body(), 0, 200),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            info("Error in BunnyVideoService@getVideoDetails: " . $e->getMessage());
            return null;
        }
    }


    public function getAllVideosData()
    {
        try {
            $allVideos = [];
            $page = 1;
            $perPage = 100;

            do {
                $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/videos", [
                    'page' => $page
                ]);

                if ($response->failed()) {
                    info("Failed to fetch videos data at page {$page}", [
                        'status' => $response->status(),
                        'body_excerpt' => mb_substr($response->body(), 0, 200),
                    ]);
                    return null;
                }

                $data = $response->json();

                 // Add current page's videos to the final result
                $videos = $data['items'] ?? $data; // handle structure if it has 'items' or not

                foreach($videos as $video){
                    $guid = $video['guid'] ?? null;
                    if(!$guid) continue;

                    $allVideos[] = [
                        'id' => $guid,
                        'title' => $video['title'] ?? '',
                        'views' => $video['views'] ?? 0
                    ];
                }

                // $allVideos = array_merge($allVideos, $videos);

                // If fewer items than perPage, it's the last page
                $hasMore = count($videos) === $perPage;
                $page++;

            } while($hasMore);

            return $allVideos;
        }catch (\Exception $e) {
            info("Error in BunnyVideoService@getAllVideosData: " . $e->getMessage());
            return null;
        }
    }

}
