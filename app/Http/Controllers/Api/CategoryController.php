<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1); // Trang mặc định là trang 1
        $limit = $request->input('limit', 10); // Số danh mục trên mỗi trang mặc định là 10
    
        $totalCategories = categories::count(); // Tổng số danh mục
        $totalPages = ceil($totalCategories / $limit); // Tổng số trang
    
        $offset = ($page - 1) * $limit;
    
        // Sử dụng Eloquent để truy vấn cơ sở dữ liệu với giới hạn số lượng bản ghi
        $categories = categories::offset($offset)->limit($limit)->get();
    
        // Chuyển đổi đường dẫn hình ảnh (nếu có) thành liên kết trực tiếp
        $categories->transform(function ($category) {
            if ($category->image) {
                $category->image = asset($category->image);
            }
            return $category;
        });
    
        // Tạo một mảng kết quả với danh sách danh mục và thông tin số trang
        $result = [
            'categories' => $categories,
            'totalPages' => $totalPages,
        ];
    
        return response()->json($result);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $data = $validator->validated();
    
        // Xử lý ảnh nếu được cung cấp
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/images');
            $data['image'] = str_replace('public/', 'storage/', $image);
        }
    
        $category = categories::create($data);
    
        return response()->json($category, 201);
    }
    

    public function show(categories $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, categories $category)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                // Các quy tắc kiểm tra dữ liệu khác dựa trên cấu trúc của danh mục
            ]);
    
            // Xác định xem trường "image" đã được cập nhật chưa
            $imageUpdated = $request->hasFile('image');
    
            // Lưu đường dẫn ảnh cũ
            $oldImagePath = $category->image;
    
            // Xử lý cập nhật dữ liệu
            $category->update($data);
    
            // Nếu trường "image" không được cập nhật, giữ nguyên đường dẫn ảnh cũ
            if (!$imageUpdated) {
                $category->image = $oldImagePath;
            } else {
                // Xóa ảnh cũ
                $oldImageFilePath = str_replace('storage/', 'public/', $oldImagePath);
                Storage::delete($oldImageFilePath);
    
                // Lưu ảnh mới
                $imagePath = $request->file('image')->store('public/images');
                $category->image = str_replace('public/', 'storage/', $imagePath);
            }
    
            $category->save();
    
            return response()->json([
                'message' => 'Update success',
                'data' => $data,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    

    public function destroy(categories $category)
    {
        // Lấy đường dẫn ảnh cũ
        $imagePath = str_replace('storage/', 'public/', $category->image);
    
        // Kiểm tra xem ảnh cũ có tồn tại không và sau đó xóa nó
        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);
        }
    
        // Xóa danh mục
        $category->delete();
    
        return response()->json(['message' => 'Delete successfully']);
    }
    
}
