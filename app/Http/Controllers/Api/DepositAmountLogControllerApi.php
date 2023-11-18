<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\deposit_amount_logs;
use Illuminate\Http\Request;

class DepositAmountLogControllerApi extends Controller
{
    public function index()
    {
        $depositAmountLogs = deposit_amount_logs::all();
        return response()->json($depositAmountLogs);
    }

    public function show(deposit_amount_logs $depositAmountLog)
    {
        return response()->json($depositAmountLog);
    }
}
