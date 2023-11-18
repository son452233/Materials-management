<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\deposit_amounts;
use App\Models\products;
use App\Models\PaymentDetail;
use App\Models\deposit_amount_logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DepositAmountControllerApi extends Controller
{
    public function index()
    {
        $depositAmounts = deposit_amounts::with('paymentDetails')->get();
        
        $formattedDepositAmounts = $depositAmounts->map(function ($depositAmount) {
            return [
                'id' => $depositAmount->id,
                'amount' => $depositAmount->amount,
                'price' => $depositAmount->price,
                'total_price' => $depositAmount->total_price,
                'percent' => $depositAmount->percent,
                'percent_amount' => $depositAmount->percent_amount,
                'remaining_amount' => $depositAmount->remaining_amount,
                'number_of_payments' => $depositAmount->number_of_payments,
                'payment_details' => $depositAmount->paymentDetails, // Sử dụng tên mối quan hệ đúng
                'status' => $depositAmount->status,
                'start_date' => $depositAmount->start_date,
                'end_date' => $depositAmount->end_date,
                'updated_at' => $depositAmount->updated_at,
                'created_at' => $depositAmount->created_at,
            ];
        });
        
        return response()->json($formattedDepositAmounts);
    }
    
    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'percent' => 'required|numeric|min:0|max:100',
            'number_of_payments' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $data = $validator->validated();
    
        $percent = $data['percent'] / 100;
    
        // Tính số ngày giữa start_date và end_date
        $start_date = Carbon::parse($data['start_date']);
        $end_date = Carbon::parse($data['end_date']);
        $days_diff = $end_date->diffInDays($start_date);
    
        // Lấy giá (price) từ product_id
        $product = products::findOrFail($data['product_id']);
        $price = $product->price;
    
        // Tính tổng tiền (total_price) dựa trên amount, price và số ngày
        $total_price = $data['amount'] * $price * $days_diff;
    
        // Tính toán số tiền phần trăm
        $percent_amount = $total_price * $percent;
    
        // Tính số tiền còn lại sau phần trăm
        $remaining_amount = $total_price - $percent_amount;
    
        // Tạo mảng chứa thông tin số tiền trả sau mỗi lần trả
        $payment_details = [];
        $remaining_amount_per_payment = $remaining_amount / $data['number_of_payments'];
    
        for ($i = 1; $i <= $data['number_of_payments']; $i++) {
            $payment_details[] = [
                'payment_number' => $i,
                'payment_amount' => $remaining_amount_per_payment,
                'remaining_amount' => $remaining_amount - $i * $remaining_amount_per_payment,
                'status' => 0, // Trạng thái mặc định là 0
            ];
        }
    
        // Chuyển mảng thành JSON
        $payment_details_json = json_encode($payment_details);
    
        // Lưu các giá trị vào database
        $depositAmount = deposit_amounts::create([
            'amount' => $data['amount'],
            'product_id' => $data['product_id'],
            'price' => $price,
            'total_price' => $total_price,
            'percent' => $data['percent'],
            'percent_amount' => $percent_amount, // Số tiền phần trăm
            'remaining_amount' => $remaining_amount, // Số tiền còn lại
            'number_of_payments' => $data['number_of_payments'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'payment_details' => $payment_details_json, // Chuyển mảng thành JSON
            'status' => 0, // Thêm cột status với giá trị mặc định là 0
        ]);
    
        foreach ($payment_details as $payment) {
            PaymentDetail::create([
                'deposit_amount_id' => $depositAmount->id,
                'payment_number' => $payment['payment_number'],
                'payment_amount' => $payment['payment_amount'],
                'remaining_amount' => $payment['remaining_amount'],
                'status' => $payment['status'],
            ]);
        }
    
        deposit_amount_logs::create([
            'deposit_amount_id' => $depositAmount->id,
            'amount' => $data['amount'], // Số tiền đã đặt cọc
            'status' => 0, // Trạng thái mặc định
            'note' => 'Thông tin đặt cọc được tạo vào ' . now(), // Ghi chú với thời gian tạo
        ]);
    
        return response()->json([
            'deposit_amount' => $depositAmount,
            'payment_details' => $payment_details,
        ], 201);
    }
    
    public function show($id)
{
    $depositAmount = deposit_amounts::find($id); // Lấy bản ghi theo ID
    if ($depositAmount) {
        return response()->json($depositAmount);
    } else {
        return response()->json(['error' => 'Không tìm thấy bản ghi.'], 404);
    }
}
public function updatePaymentStatus(Request $request, $deposit_amount_id, $payment_detail_id)
{
    // Tìm đối tượng deposit_amounts theo ID bằng Eloquent
    $depositAmount = deposit_amounts::find($deposit_amount_id);

    if (!$depositAmount) {
        return response()->json(['error' => 'Không tìm thấy giao dịch đặt cọc.'], 404);
    }

    // Trích xuất 'new_status' từ yêu cầu
    $newStatus = $request->input('status');

    if (!in_array($newStatus, [0, 1, 2])) {
        return response()->json(['error' => 'Trạng thái không hợp lệ.'], 400);
    }

    // Tìm và cập nhật trạng thái trong PaymentDetail
    $paymentDetail = PaymentDetail::where('deposit_amount_id', $deposit_amount_id)
        ->where('id', $payment_detail_id)
        ->first();

    if (!$paymentDetail) {
        return response()->json(['error' => 'Không tìm thấy đợt trả cụ thể.'], 404);
    }

    $paymentDetail->status = $newStatus; // Cập nhật trạng thái
    $paymentDetail->save();

    return response()->json(['message' => 'Cập nhật trạng thái thành công.'], 200);
}







    
}
