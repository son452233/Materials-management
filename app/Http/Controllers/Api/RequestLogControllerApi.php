<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\request_logs;
use Illuminate\Http\Request;

class RequestLogControllerApi extends Controller
{
    public function index()
    {
        $request_log = request_logs::all();
        return response()->json($request_log);
    }

    public function show(request_logs $request_log)
    {
        return response()->json($request_log);
    }
}
