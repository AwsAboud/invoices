<?php

namespace App\Http\Controllers;

use App\Models\InvoiceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class InvoiceAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request, $invoice_id)
    {
        $image_name =  $request->file('pic')->getClientOriginalName();
        $attachments = new InvoiceAttachment();
        $attachments->file_name = $request->file('pic')->getClientOriginalName();
        $attachments->invoice_number = $request->invoice_number;
        $attachments->created_by = auth()->user()->name;
        $attachments->invoice_id =  $invoice_id;
        $attachments->save();
        // Store the attached file in the public/Attachments directory, organizing files by invoice number:
        // The 'Attachments' directory contains subdirectories named after the actual invoice number.
        $request->pic->move(public_path('Attachments/' . $request->invoice_number), $image_name);
    }

    /**
     * Display the specified resource.
     */
    public function show(InvoiceAttachment $invoiceAttachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvoiceAttachment $invoiceAttachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoiceAttachment $invoiceAttachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //delete the Attachments from the storage
        $file_path  = public_path('Attachments'.'/'.$request->invoice_number . '/' . $request->file_name);
        if (File::exists($file_path)) {
            File::delete($file_path );
        }
        else {
            return redirect()->back()->with(['error' => 'المرفق غير موجود']);
        }
        //delete the Attachments from the database
        InvoiceAttachment::findOrFail($request->file_id)->delete();
        return redirect()->back()->with(['delete' => 'تم حذف المرفق بنجاح ']);
    }
}
