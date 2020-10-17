<?php

namespace App\Http\Controllers;

use App\Services\ColorConvertService;
use Illuminate\Http\Request;

class ColorConvertController extends Controller
{
    public function hexToRgba()
    {
        $hex = "#123123";
        $alpha = "0.5";

        $colorConvertService = new ColorConvertService($hex, $alpha);

        echo $colorConvertService->hexToRgba();
    }
}
