<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasUuids, SoftDeletes, HasFactory;
    protected $guarded = ['id'];

    public function image(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (strlen($value) == 0) {
                    return null;
                }
                return asset('storage/banners/' . $value);
                // return Storage::disk('s3')->url('public/school/logo/' . $value);
            }
        );
        
    }
}
