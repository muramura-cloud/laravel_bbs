<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_id',
        'category',
        'table_name',
        'comment',
    ];

    public function post()
    {
        return $this->belongsTo('App\Models\User');
    }
}
