<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\contract_details;
use App\Models\contracts;
use App\Models\products;
use App\Models\contract_logs;
use App\Models\deposit_amounts;
use App\Models\User;
use App\Models\bills; 
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contracts::with([
            'products' => function ($query) {
                $query->select('products.id', 'name', 'price');
            },
            'sale:id,name',
            'customer:id,name',
            'manager:id,name',
            'deposit_amounts' => function ($query) {
                $query->with('paymentDetails');
            }
        ])->get();
        
    
        return response()->json(['contracts' => $contracts], 200);
    }
    
    


    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'manager_id' => 'required|integer',
            'name' => 'required|string',
            'note' => 'nullable|string',
            'status' => 'required|string',
            'datetime_start' => ['required', 'date', 'after_or_equal:now'],
            'datetime_end' => ['required', 'date', 'after:datetime_start'],
            'manager_electronic_signature' => 'required|string',
            'customer_electronic_signature' => 'required|string',
            'sale_eletronic_signature' => 'required|string',
            'deposit_amount_ids' => 'required|array',
            'deposit_amount_ids.*' => 'required|integer',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.amount' => 'required|integer',
        ]);
    
        // Tạo hợp đồng (contracts)
        $contract = contracts::create($data);
    
        $contractProducts = [];
        $contractTotalPrice = 0;
    
        // Lấy danh sách ID từ trường "deposit_amount_ids"
        $depositAmountIds = $data['deposit_amount_ids'];
    
        // Kết nối các khoản đặt cọc (deposit_amounts) với hợp đồng
        $contract->deposit_amounts()->attach($depositAmountIds);
        deposit_amounts::whereIn('id', $depositAmountIds)->update(['status' => 1]);
        $contractTotalPrice = deposit_amounts::whereIn('id', $depositAmountIds)->sum('total_price');
        // Kết nối các sản phẩm (products) với hợp đồng và gán amount cho mỗi sản phẩm
        foreach ($data['products'] as $productData) {
            $productId = $productData['id'];
            $amount = $productData['amount'];
    
            $contract->products()->attach($productId, ['amount' => $amount]);
            
            // Lấy thông tin của sản phẩm để hiển thị trong kết quả
            $product = products::find($productId);
            $productInfo = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
            ];
    
            // Thêm thông tin sản phẩm vào danh sách sản phẩm cho hợp đồng
            $contractProducts[] = $productInfo;
            }
            
        // Tạo bills tự động sau khi tạo hợp đồng
        $totalPercentAmount = $contract->deposit_amounts->sum('percent_amount');
        $billNote = 'Tổng percent_amount của deposit_amounts: ' . $totalPercentAmount;
        bills::create([
            'user_id' => $contract->customer_id,
            'contract_id' => $contract->id,
            'total_price' => $totalPercentAmount, // Gán tổng percent_amount vào total_price
            'note' => 'Tổng percent_amount của deposit_amounts: ' . $totalPercentAmount,
        ]);
        contract_logs::create([
            'contract_id' => $contract->id,
            'amount' => $contractTotalPrice, // Sử dụng contractTotalPrice làm giá trị amount
            'description' => 'Hợp đồng ' .  $contract->name  . ' được tạo vào ' . now(),
        ]);
    
        return response()->json([
            'contracts' => $contract,
            'contract_products' => $contractProducts,
            'contract_total_price' => $contractTotalPrice,
        ], 201);
    }
    


    public function show($contractId)
    {
        $contract = contracts::find($contractId); // Lấy hợp đồng theo ID

        if (!$contract) {
            return response()->json(['message' => 'Hợp đồng không tồn tại'], 404);
        }

        // Lấy danh sách sản phẩm trong hợp đồng
        $contractProducts = $contract->products;

        // Duyệt qua danh sách sản phẩm và lấy thông tin
        $productInfo = [];
        foreach ($contractProducts as $product) {
            $productInfo[] = [
                'name' => $product->name,
                'price' => $product->price,
            ];
        }

        return response()->json([
            'contracts' => $contract,
            'contract_details' => $productInfo,
        ]);
    }

    public function destroy(contracts $contract)
    {
        $contract->delete();

        return response()->json(['message' => 'Delete successfully']);
    }
}
