<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotspots', function (Blueprint $table) {
            $table->index('type');
            $table->index(['type', 'model_url']);
            $table->index(['pitch', 'yaw']);
        });
    }

    public function down(): void
    {
        Schema::table('hotspots', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['type', 'model_url']);
            $table->dropIndex(['pitch', 'yaw']);
        });
    }
};
