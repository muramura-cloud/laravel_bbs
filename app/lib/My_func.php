<?php

namespace App\lib;

class My_func
{
    public static function dump($array)
    {
        echo '<pre style="background: #fff; color: #333; ' .
            'border: 1px solid #ccc; margin: 5px; padding: 10px;">';
        print_r($array);
        echo '</pre>';
    }
}
