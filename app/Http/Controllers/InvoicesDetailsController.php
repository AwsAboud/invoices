<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\InvoicesDetails;
use App\Models\InvoiceAttachment;
class InvoicesDetailsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store($request, $invoice_id)
    {
        InvoicesDetails::create([
            'invoice_id' =>  $invoice_id,
            'product' => $request->product,
            'section' =>  $request->Section,
            'invoice_number' => $request->invoice_number,
            'status' => Invoice::STATUS_NOT_PAID,
            'value_status' => Invoice::STATUS_NOT_PAID_VALUE,
            'note' => $request->note,
            'created_by' => auth()->user()->name,

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($invoice_id)
    {
        // Retrieve the invoice, including soft-deleted ones,
        // to allow viewing details from the invoices.archive view also .
        $invoice = Invoice::withTrashed()->where('id', $invoice_id)->first();
        return view('invoices.show', [
            'invoice' => $invoice,
            'invoice_details' => InvoicesDetails::where('invoice_id', $invoice_id)->get(),
            'attachments' => InvoiceAttachment::where('invoice_id', $invoice_id)->get()

        ]);
    }

    /**
     *  Display the attachment file from storage
     */
    public function open_file($invoice_number, $file_name){
        //Get the full path to the attachment file
        $file_path  = public_path('Attachments'.'/'.$invoice_number.'/'.$file_name);
        return response()->file($file_path);
    }
     /**
     * dawnload the attachment file in storage
     */
    public function dawnload_file($invoice_number, $file_name){
        //Get the full path to the attachment file
        $file_path  = public_path('Attachments'.'/'.$invoice_number.'/'.$file_name);
        return response()->download($file_path);
    }

}
