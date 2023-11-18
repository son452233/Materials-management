<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\bills;
use App\Models\User;
use App\Models\contracts;
use Illuminate\Http\Request;

class BillsControllerApi extends Controller
{
    public function index()
    {
        // Lấy danh sách bills
        $bills = bills::all();
        
        return response()->json($bills, 200);
    }
    
    

    public function show($id)
    {
        // Lấy thông tin của một bản ghi bill cụ thể bằng ID
        $bill = bills::find($id);
    
        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }
    
        // Sử dụng các mối quan hệ để lấy contract của bill
        $contract = $bill->contract;
    
        // Lấy danh sách deposit_amounts của contract cùng với paymentDetails
        $depositAmounts = $contract->deposit_amounts()->with('paymentDetails')->get();
    
        // Tính tổng total_price từ danh sách deposit_amounts
        $totalPrice = $depositAmounts->sum('total_price');
    
        // Tạo một mảng dữ liệu chi tiết
        $billDetails = [
            'user_name' => $bill->user->name,
            'contract' => $contract,
            'total_price' => $totalPrice,
        ];
    
        return response()->json($billDetails, 200);
    }
    
}
