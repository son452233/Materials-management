<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\invoice_logs;
use App\Models\invoices;
use Illuminate\Http\Request;

class InvoiceLogControllerApi extends Controller
{
    public function index()
    {
        $invoiceLogs = invoice_logs::all();
        return response()->json($invoiceLogs);
    }

    public function show(invoice_logs $invoiceLog)
    {
        return response()->json($invoiceLog);
    }
}
