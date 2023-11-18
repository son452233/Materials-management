<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductImageApi extends Controller
{
    public function index()
{
    $images = ProductImage::all();

    return response()->json($images, 200);
}

}
