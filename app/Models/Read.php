<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Read extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'is_read',
    ];

    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }

}
