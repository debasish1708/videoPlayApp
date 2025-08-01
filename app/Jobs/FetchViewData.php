<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchViewData implements ShouldQueue
{
    use Queueable;
    private $bunnyVideo;
    private $videosData;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->bunnyVideo = new \App\Services\BunnyVideoService();
        $this->videosData = [];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $allVideoInformation = $this->bunnyVideo->getAllVideosData();
            foreach($allVideoInformation as $video){
                $db_video = Video::where('bunny_video_id',$video['id'])->first();
                if ($db_video) {
                    $db_video->views = $video['views'] ?? 0;
                    $db_video->save();
                }
            }

        }catch(\Exception $e){
            info('Exception at FetchViews Data'.$e->getMessage());
            return;
        }
    }
}
