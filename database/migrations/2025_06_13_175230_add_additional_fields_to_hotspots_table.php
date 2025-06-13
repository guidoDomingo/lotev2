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
        Schema::table('hotspots', function (Blueprint $table) {
            $table->string('model_url')->nullable()->after('text');
            $table->string('audio_url')->nullable()->after('model_url');
            $table->string('video_url')->nullable()->after('audio_url');
            $table->string('scene_id')->nullable()->after('video_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotspots', function (Blueprint $table) {
            $table->dropColumn(['model_url', 'audio_url', 'video_url', 'scene_id']);
        });
    }
};
