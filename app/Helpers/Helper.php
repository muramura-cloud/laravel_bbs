<?php

namespace App\Helpers;

class Helper
{
    public function run($string)
    {
        return strtoupper($string);
    }

    public static function dump($array)
    {
        echo '<pre style="background: #fff; color: #333; ' .
            'border: 1px solid #ccc; margin: 5px; padding: 10px;">';
        print_r($array);
        echo '</pre>';
    }

    public static function getQuery()
    {
        $url = parse_url(request()->fullUrl());
        parse_str($url['query'], $params);

        return $params;
    }
}
