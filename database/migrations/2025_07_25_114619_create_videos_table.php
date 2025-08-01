<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('access_type_id')->constrained('access_types');
            $table->foreignUuid('genre_id')->constrained('genres');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('bunny_video_id')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('unpublished');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
