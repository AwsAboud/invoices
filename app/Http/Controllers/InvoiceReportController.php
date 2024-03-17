<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceReportController extends Controller
{
    /**
     * Display the index page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('reports.invoices_report');
    }

    /**
     * Search for invoices based on the search type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function searchForInvoices(Request $request)
    {
        // Determine the search type and call the appropriate search method
        if ($request->search_type == 1) {
            return $this->searchByInvoiceStatus($request);
        } else {
            return $this->searchByInvoiceNumber($request);
        }
    }

    /**
     * Search for invoices by invoice status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    private function searchByInvoiceStatus(Request $request)
    {
        // Convert string dates to proper date objects
        $from = date($request->from);
        $to = date($request->to);
        $invoice_status = $request->invoice_status;
        // Retrieve invoices based on invoice status and date range, if provided
        if ($from && $to) {
            $invoices = Invoice::with('section')
                ->whereBetween('invoice_date', [$from, $to])
                ->where('status', $invoice_status)
                ->get();
        } else {
            $invoices = Invoice::with('section')
                ->where('status', $invoice_status)->get();
        }
        // Return the view with the invoices data
        return view('reports.index', compact(['invoices','from','to','invoice_status']));
    }

    /**
     * Search for invoices by invoice number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    private function searchByInvoiceNumber(Request $request)
    {
        // Retrieve invoices based on invoice number
        $invoices = Invoice::where('invoice_number', $request->invoice_number)->get();

        // Return the view with the invoices data
        return view('reports.index', compact('invoices'));
    }
}
