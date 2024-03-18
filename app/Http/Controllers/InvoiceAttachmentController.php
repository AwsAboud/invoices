<?php

namespace App\Http\Controllers;

use App\Models\InvoiceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class InvoiceAttachmentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store($request, $invoice_id)
    {
        $file_name =  $request->file('pic')->getClientOriginalName();
        InvoiceAttachment::create([
            'file_name' =>  $file_name,
            'invoice_number' => $request->invoice_number,
            'created_by' => auth()->user()->name,
            'invoice_id' => $invoice_id
        ]);
        // Store the attached file in the public/Attachments directory, organizing files by invoice number:
        // The 'Attachments' directory contains subdirectories named after the actual invoice number.
        $request->pic->move(public_path('Attachments/' . $request->invoice_number),  $file_name);
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
