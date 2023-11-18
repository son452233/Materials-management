<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\contract_logs;
use Illuminate\Http\Request;

class ContractLogControllerApi extends Controller
{
    public function index()
    {
        $contractLogs = contract_logs::all();
        return response()->json($contractLogs);
    }

    public function show(contract_logs $contractLog)
    {
        return response()->json($contractLog);
    }
}
