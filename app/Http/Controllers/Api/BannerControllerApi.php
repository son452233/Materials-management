<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerControllerApi extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $totalBanners = Banner::count();
        $totalPages = ceil($totalBanners / $limit);

        $offset = ($page - 1) * $limit;

        $banners = Banner::with('product')->offset($offset)->limit($limit)->get();

        $banners->transform(function ($banner) {
            if ($banner->image) {
                $banner->image = url($banner->image);
            }
            return $banner;
        });

        $result = [
            'banners' => $banners,
            'totalPages' => $totalPages,
        ];

        return response()->json($result);
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'description' => 'required|string',
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $imagePath = $request->file('image')->store('public/banners');
        $data['image'] = str_replace('public/', 'storage/', $imagePath);

        $banner = Banner::create($data);

        return response()->json($banner, 201);
    }

    public function show(Banner $banner)
    {
        $banner->load('product');

        if ($banner->image) {
            $banner->image = url($banner->image);
        }

        return response()->json($banner);
    }

    public function update(Request $request, Banner $banner)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'description' => 'nullable|string',
            'product_id' => 'nullable|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            $oldImageFilePath = str_replace('storage/', 'public/', $banner->image);
            Storage::delete($oldImageFilePath);

            // Lưu ảnh mới
            $imagePath = $request->file('image')->store('public/banners');
            $data['image'] = str_replace('public/', 'storage/', $imagePath);
        }

        $banner->update($data);

        return response()->json([
            'message' => 'Update success',
            'data' => $data,
        ]);
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            // Lấy đường dẫn ảnh cũ
            $imagePath = str_replace('storage/', 'public/', $banner->image);

            // Kiểm tra xem ảnh cũ có tồn tại không và sau đó xóa nó
            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
            }
        }

        // Xóa banner
        $banner->delete();

        return response()->json(['message' => 'Delete successfully']);
    }
}
