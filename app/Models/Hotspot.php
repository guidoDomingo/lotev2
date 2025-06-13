<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'pitch', 
        'yaw', 
        'type', 
        'title', 
        'text',
        'model_url',
        'audio_url', 
        'video_url',
        'scene_id'
    ];
}
