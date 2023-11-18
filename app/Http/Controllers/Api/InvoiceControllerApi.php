<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon; // Đảm bảo đã import Carbon

use App\Models\contracts;
use App\Models\invoice_logs;
use App\Models\invoices;
use App\Models\User;
use Illuminate\Http\Request;

class InvoiceControllerApi extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách hợp đồng cùng với thông tin sản phẩm và thông tin người dùng (sale, customer, manager)
        $contracts = contracts::with([
            'products' => function ($query) {
                $query->select('products.id', 'name', 'price');
            },
            'sale:id,name',
            'customer:id,name',
            'manager:id,name'
        ])->get();

        $currentDate = Carbon::now(); // Lấy thời gian hiện tại

        foreach ($contracts as $contract) {
            // Kiểm tra xem đã tồn tại hóa đơn nào với contract_id này chưa
            $existingInvoice = invoices::where('contract_id', $contract->id)->first();

            if (!$existingInvoice) {
                // Nếu chưa tồn tại hóa đơn với contract_id này, thì tạo một hóa đơn mới
                $invoice = invoices::create([
                    'contract_id' => $contract->id,
                    'invoice_number' => 1, // Giá trị mặc định
                    'invoice_date' => $currentDate, // Thời gian hiện tại
                ]);
            }
        }
        $invoices = invoices::all();

        return response()->json(['message' => 'Thông tin thanh toán của tất cả người dùng đã được lưu vào cơ sở dữ liệu.', 'invoices' => $invoices], 200);
    }




    public function show($id)
    {
        // Lấy thông tin của một bản ghi hợp đồng cụ thể bằng ID
        $contract = contracts::with([
            'products' => function ($query) {
                $query->select('products.id', 'name', 'price');
            },
            'sale:id,name',
            'customer:id,name',
            'manager:id,name',
            'deposit_amounts' => function ($query) {
                $query->with('paymentDetails');
            }
        ])->findOrFail($id);
    
        if (!$contract) {
            return response()->json(['message' => 'Contract not found'], 404);
        }
    
        // Kiểm tra xem đã tồn tại hóa đơn nào với contract_id này chưa
        $existingInvoice = invoices::where('contract_id', $contract->id)->first();
    
        if (!$existingInvoice) {
            // Nếu chưa tồn tại hóa đơn với contract_id này, thì tạo một hóa đơn mới
            $currentDate = Carbon::now();
            $invoice = invoices::create([
                'contract_id' => $contract->id,
                'invoice_number' => 1, // Giá trị mặc định
                'invoice_date' => $currentDate, // Thời gian hiện tại
            ]);
    
            // Tạo invoice_log sau khi tạo invoice
            invoice_logs::create([
                'invoice_id' => $invoice->id,
                'note' => 'Thông tin thanh toán được tạo vào ' . now(), // Ghi chú
            ]);
        }
    
        return response()->json(['contract' => $contract], 200);
    }
    
    
    
    
}