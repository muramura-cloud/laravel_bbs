<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Helpers\Helper;

class HasReportTarget implements Rule
{
    public function __construct()
    {
    }

    public function passes($attribute, $id)
    {
        $target = request('target');
        $model = $this->getModel($target);

        return ($model instanceof Model && $model->where('id', (int) $id)->exists());
    }

    public function message()
    {
        return '該当データが見つかりません。';
    }

    // モデルがあるかどうか確認してモデルを返却する
    private function getModel($table)
    {
        $targets = config('report.targets');

        if (Arr::exists($targets, $table)) {
            $class = config('report.targets')[$table]['model'];

            if (class_exists($class)) {
                return new $class();
            }
        }

        return null;
    }
}
