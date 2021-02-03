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

    public function getReportedContentIds($table_name)
    {
        return array_column($this::where('table_name', $table_name)->get('target_id')->toArray(), 'target_id');
    }

    public function getReportedContents($model, $ids, $page)
    {
        $contents = $model::where(function ($post_query) use ($ids) {
            foreach ($ids as $id) {
                $post_query->orWhere('id', $id);
            }
        })->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page', $page);

        return $contents;
    }
}
