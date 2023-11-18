<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\products;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;

class ProductControllerApi extends Controller
{
        public function index(Request $request)
        {
            $page = $request->input('page', 1); // Trang mặc định là trang 1
            $limit = $request->input('limit', 10000); // Số sản phẩm trên mỗi trang mặc định là 10
        
            $totalProducts = products::count(); // Tổng số sản phẩm
            $totalPages = ceil($totalProducts / $limit); // Tổng số trang
        
            $offset = ($page - 1) * $limit;
        
            // Sử dụng Eloquent để truy vấn cơ sở dữ liệu với giới hạn số lượng bản ghi
            $products = products::offset($offset)->limit($limit)->get();
        
            // Chuyển đổi đường dẫn hình ảnh sản phẩm và QR code thành liên kết trực tiếp đến Google
            $products->each(function ($product) {
                $product->qr_code = asset($product->qr_code);
                $product->image = asset($product->image);
                // Lấy danh sách hình ảnh của sản phẩm từ bảng 'product_images'
                $product->images = $product->images()->get(['image_path']);


                if ($product->category) {
                    $product->category_name = $product->category->name;
                } else {
                    $product->category_name = 'Danh mục không tồn tại';
                }
            });
        
            // Tạo một mảng kết quả với danh sách sản phẩm và thông tin số trang
            $result = [
                'products' => $products,
                'totalPages' => $totalPages,
            ];
        
            return response()->json($result);
        }
    
    
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'qr_code' => 'required|image|mimes:jpeg,png,jpg,gif',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif', // Đảm bảo chấp nhận nhiều ảnh
        'inventory_id' => 'required|exists:inventories,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    // Lưu ảnh QR code vào storage và cập nhật đường dẫn
    $qrCodePath = $request->file('qr_code')->store('public/images');
    $qrCodePath = str_replace('public/', 'storage/', $qrCodePath);

    // Lưu ảnh 'image' vào storage và cập nhật đường dẫn
    $imagePath = $request->file('image')->store('public/images');
    $imagePath = str_replace('public/', 'storage/', $imagePath);

    $product = products::create([
        'name' => $data['name'],
        'description' => $data['description'],
        'price' => $data['price'],
        'category_id' => $data['category_id'],
        'qr_code' => $qrCodePath,
        'image' => $imagePath,
    ]);

    // Lưu các ảnh từ mảng 'images[]' vào storage và cập nhật đường dẫn
    $imagePaths = [];

    foreach ($data['images'] as $image) {
        $new_name = rand() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('public/images', $new_name);
        $imagePath = str_replace('public/', 'storage/', $imagePath);
        $imagePaths[] = asset($imagePath); // Sử dụng asset để tạo đường dẫn đầy đủ
    }

    // Tạo các bản ghi ảnh sản phẩm trong bảng 'product_images'
    $imageData = collect($imagePaths)->map(function ($path) {
        return ['image_path' => $path];
    })->all();

    $product->images()->createMany($imageData);

    return response()->json('Product created successfully', 200);
}

    public function show(products $product)
    {
        // Chuyển đổi đường dẫn hình ảnh thành liên kết trực tiếp đến Google
        $product->image = asset($product->image);
        $product->qr_code = asset($product->qr_code);

        return response()->json($product);
    }

    public function update(Request $request, products $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'image|mimes:jpeg,png,jpg,gif',
            'qr_code' => 'image|mimes:jpeg,png,jpg,gif',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif', // Ensure it accepts multiple images
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $data = $validator->validated();
    
        if ($request->hasFile('image')) {
            // Delete old images from product_images table
            $product->images()->delete();
    
            $image = $request->file('image')->store('public/images');
            $data['image'] = str_replace('public/', 'storage/', $image);
        }
    
        if ($request->hasFile('qr_code')) {
            $qrCode = $request->file('qr_code')->store('public/images');
            $data['qr_code'] = str_replace('public/', 'storage/', $qrCode);
        }
    
        // Update the product with the new data
        $product->update($data);
    
        // Handle the updating of associated images (images[])
        if ($request->hasFile('images')) {
            $imagePaths = [];
    
            foreach ($request->file('images') as $image) {
                $new_name = rand() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('public/images', $new_name);
                $imagePath = str_replace('public/', 'storage/', $imagePath);
                $imagePaths[] = asset($imagePath);
            }
    
            $imageData = collect($imagePaths)->map(function ($path) {
                return ['image_path' => $path];
            })->all();
    
            $product->images()->createMany($imageData);
        }
    
        return response()->json([
            'message' => 'Update success',
            'data' => $data,
        ]);
    }
    
    
    

    public function destroy(products $product)
    {
        $product->images()->delete();

        // Delete the product itself
        $product->delete();
    
        return response()->json(['message' => 'Product and associated images deleted successfully']);
        }
}
