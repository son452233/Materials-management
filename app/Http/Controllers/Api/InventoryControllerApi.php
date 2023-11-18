<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\inventory_products;
use App\Models\inventories;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InventoryControllerApi extends Controller
{
    public function index()
    {
        $inventories = inventories::with(['products' => function ($query) {
            $query->select('products.id', 'name', 'price', 'description');
        }])->get();

        return response()->json(['inventories' => $inventories], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'manager_id' => 'required|exists:users,id',
        ]);

        $inventory = inventories::create($data);
        return response()->json(['message' => 'Inventory created successfully', 'inventory' => $inventory], 201);
    }
    public function attachProduct(Request $request, $inventoryId, $productId)
    {
        // Kiểm tra xem kho (inventory) và sản phẩm (product) tồn tại
        $inventory = inventories::find($inventoryId);
        $product = products::find($productId);

        if (!$inventory || !$product) {
            return response()->json(['message' => 'Inventory or product not found'], 404);
        }

        // Validate số lượng và kiểm tra ràng buộc (chẳng hạn, kiểm tra xem sản phẩm đã tồn tại trong kho chưa)
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // Đính kèm sản phẩm vào kho với số lượng xác định
        $inventory->products()->attach($productId, $data);

        return response()->json(['message' => 'Product attached to inventory successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'string',
            'address' => 'string',
            'phone_number' => 'string',
            'manager_id' => 'exists:users,id',
        ]);

        $inventory = inventories::find($id);

        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        $inventory->update($data);
        return response()->json(['message' => 'Inventory updated successfully', 'inventory' => $inventory], 200);
    }

    public function destroy($id)
    {
        $inventory = inventories::find($id);

        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        $inventory->delete();
        return response()->json(['message' => 'Inventory deleted successfully'], 200);
    }
}
