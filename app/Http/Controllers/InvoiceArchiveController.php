<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class InvoiceArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Enhance performance by selecting only necessary fields from the eager-loaded relation.
        $invoices = Invoice::with(['section' => function ($query) {
            $query->select('id', 'section_name');
        }])->onlyTrashed()->get();

        return view('invoices.archive', [
            'invoices' => $invoices
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice, Request $request)
    {
        //delete the Attachments from the storage
        $directory_path = public_path('Attachments' . '/' . $request->invoice_number);
       // dd(File::exists($directory_path));
        if (File::exists($directory_path)) {
            File::deleteDirectory($directory_path);
        }
        $invoice->forceDelete();
        session()->flash('delete');
        return redirect()->back();
    }

    //restore the soft deleted invoice
    public function restoreArchivedInvoice(Invoice $invoice)
    {
        $invoice->restore();
        session()->flash('restore_invoice');
        return redirect()->back();
    }

    // Soft delete the invoice
    public function archiveInvoice(Invoice $invoice)
    {
        $invoice->delete();
        session()->flash('invoice_archived');
        return redirect()->back();
    }
}
