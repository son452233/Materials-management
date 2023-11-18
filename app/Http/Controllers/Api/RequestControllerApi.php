<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\requests;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Validator;

class RequestControllerApi extends Controller
{
    public function index()
    {
        $requests = requests::with(['customer:id,name', 'sale:id,name', 'products:id'])->get();

        return response()->json($requests);
    }
    public function store(Request $request)
{
    $data = $request->validate([
        'sale_id' => 'required|integer|exists:users,id',
        'customer_id' => 'required|integer|exists:users,id',
        'name' => 'required|string',
        'note' => 'nullable|string',
        'datetime' => 'required|date',
        'products' => 'required|array',
        'products.*.id' => 'required|exists:products,id', // ID của sản phẩm
        'products.*.amount' => 'required|integer', // Số lượng
    ]);

    // Lấy tên của khách hàng (customer) và người bán (sale)
    $customerName = User::find($data['customer_id'])->name;
    $saleName = User::find($data['sale_id'])->name;

    $requestData = array_merge($data, [
        'customer_name' => $customerName,
        'sale_name' => $saleName,
    ]);

    $request = requests::create($requestData);

    // Lưu trữ danh sách sản phẩm và số lượng trong bảng request_details
    $productData = collect($data['products'])->map(function ($product) {
        return [
            'product_id' => $product['id'],
            'amount' => $product['amount'],
        ];
    });

    $request->products()->attach($productData);

    return response()->json($request, 201);
}

}
