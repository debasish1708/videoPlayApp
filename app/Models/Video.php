<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    public function accessType()
    {
        return $this->belongsTo(AccessType::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * Get iframe embed URL
     */
    public function getIframeUrlAttribute()
    {
        if (!$this->bunny_video_id) {
            return null;
        }
        $libraryId = config('constant.bunny.library_id');
        return "https://iframe.mediadelivery.net/embed/{$libraryId}/{$this->bunny_video_id}?autoplay=false&loop=false&muted=false&preload=true&responsive=true";
    }

    public function getIframePlayUrlAttribute()
    {
        if (!$this->bunny_video_id) {
            return null;
        }
        $libraryId = config('constant.bunny.library_id');
        // https://iframe.mediadelivery.net/play/473465/8af2203e-4639-47a8-919b-70c454c68850
        return "https://iframe.mediadelivery.net/play/{$libraryId}/{$this->bunny_video_id}";
    }
}
