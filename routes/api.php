<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\BannerControllerApi;
use App\Http\Controllers\Api\BillsControllerApi;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\ContractLogControllerApi;
use App\Http\Controllers\Api\DepositAmountControllerApi;
use App\Http\Controllers\Api\DepositAmountLogControllerApi;
use App\Http\Controllers\Api\InventoryControllerApi;
use App\Http\Controllers\Api\InvoiceControllerApi;
use App\Http\Controllers\Api\InvoiceLogControllerApi;
use App\Http\Controllers\Api\ProductControllerApi;
use App\Http\Controllers\api\ProductImageApi;
use App\Http\Controllers\Api\RequestControllerApi;
use App\Http\Controllers\api\RequestLogControllerApi;
use App\Http\Controllers\api\RoleControllerApi;
use App\Http\Controllers\Api\UserControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Tuyến đường dưới đây yêu cầu xác thực bằng Sanctum
    Route::get('users', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [AuthController::class, 'logout']);
});

// Route::group(['prefix' => 'auth'], function () {
// Trong tệp routes/web.php hoặc routes/api.php

Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register']);
// });

Route::resources([
    'categories' => CategoryController::class,
    'products' => ProductControllerApi::class,
    // 'users' => UserControllerApi::class,
    'requests' => RequestControllerApi::class,
    'request-logs' => RequestLogControllerApi::class,
    'contracts' => ContractController::class,
    'contract-logs' => ContractLogControllerApi::class,
    'deposit-amounts' => DepositAmountControllerApi::class,
    'deposit-amount-logs' => DepositAmountLogControllerApi::class,
    'inventories' => InventoryControllerApi::class,
    // 'inventory-logs' => Inventory
    'invoices' => InvoiceControllerApi::class,
    'product-images' => ProductImageApi::class,
    'invoice-logs' => InvoiceLogControllerApi::class,
    'bills' => BillsControllerApi::class,
    'roles' => RoleControllerApi::class,
    'banners' => BannerControllerApi::class,


]);
// Route::group(["prefix" => "user"], function () {
//     Route::get("/get/{id}", [CategoryController::class, "get"]);
//     Route::get("/gets", [CategoryController::class, "index"]);
//     Route::post("/store", [CategoryController::class, "store"]);
//     Route::put("/update/{id}", [CategoryController::class, "update"]);
//     Route::delete("/delete/{id}", [CategoryController::class, "delete"]);
// });
Route::group(['prefix' => 'users'], function () {
    Route::get('/gets', [UserControllerApi::class, 'index'])->middleware('auth:sanctum');
    Route::post('/store', [UserControllerApi::class, 'store']);
    Route::post('/register', [UserControllerApi::class, 'register']);
    Route::post('/login', [UserControllerApi::class, 'login']);
    Route::get('/get/{user}', [UserControllerApi::class, 'show']);
    Route::put('/update/{user}', [UserControllerApi::class, 'update']);
    Route::delete('/delete/{user}', [UserControllerApi::class, 'destroy']);
});

// Route::group(['middleware' => 'auth:api'], function () {
// Route để tạo vai trò
Route::post('/roles/create', [RoleControllerApi::class, 'createRole']);

// Route để tạo quyền
Route::post('/permissions/create', [RoleControllerApi::class, 'createPermission']);
// });
Route::put('deposit_amounts/{deposit_amount_id}/update-payment-status/{payment_detail_id}', [DepositAmountControllerApi::class, 'updatePaymentStatus']);
