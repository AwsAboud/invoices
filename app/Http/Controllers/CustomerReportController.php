<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\section;
use Illuminate\Http\Request;

class CustomerReportController extends Controller
{
    public function index()
    {
        $sections = section::all();
        return view('reports.customers_report', compact('sections'));
    }
    public function SearchForCustomers(Request $request)
    {
        $from = date($request->from);
        $to = date($request->to);
        $sections = section::all();
        $selectedSection = $request->section;

        if ($request->section && $request->product && $request->from && $request->to) {
            $invoices = Invoice::with('section')
                ->whereBetween('invoice_date', [$from, $to])
                ->where('section_id', $request->section)
                ->where('product', $request->product)
                ->get();
        }
        //search without determinate the date
        else {
            $invoices = Invoice::with('section')
                ->where('section_id', '=', $request->section)
                ->where('product', '=', $request->product)->get();
        }
        return view('reports.customers_report', compact('sections','invoices','from', 'to','selectedSection'));
    }
}
