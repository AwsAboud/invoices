<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoicesDetails;
use App\Models\InvoiceAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class InvoiceService{

    public function store($invoiceData)
    {
        // Begin a database transaction
        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_number' => $invoiceData['invoice_number'],
                'invoice_date' => $invoiceData['invoice_date'],
                'due_date' => $invoiceData['due_date'],
                'section_id' =>  $invoiceData['Section'],
                'product' => $invoiceData['product'],
                'collection_amount' =>  $invoiceData['collection_amount'],
                'commission_amount' => $invoiceData['commission_amount'],
                'discount' => $invoiceData['discount'],
                'value_vat' => $invoiceData['value_vat'],
                'rate_vat' => $invoiceData['rate_vat'],
                'total' => $invoiceData['Total'],
                'status' => Invoice::STATUS_NOT_PAID,
                'value_status' => Invoice::STATUS_NOT_PAID_VALUE,
                'note' => $invoiceData['note']
            ]);

            $this->storeInvoiceDetails($invoiceData, $invoice->id);
            //check if there is a file uploaded
            if (isset($invoiceData['pic']) && $invoiceData['pic'] instanceof UploadedFile && $invoiceData['pic']->isValid()) {
               $this->storeInvoiceAttachments($invoiceData, $invoice->id);
            }
            // Commit the transaction if everything is successful
            DB::commit();
        } catch (\Exception $e) {
            // An exception occeurred, rollback the transaction
            DB::rollBack();
        }
    }
    public function storeInvoiceDetails($invoiceData, $invoice_id){
        $invoiceDetails = InvoicesDetails::create([
            'invoice_id' =>  $invoice_id,
            'product' => $invoiceData['product'],
            'section' =>  $invoiceData['Section'],
            'invoice_number' => $invoiceData['invoice_number'],
            'status' => Invoice::STATUS_NOT_PAID,
            'value_status' => Invoice::STATUS_NOT_PAID_VALUE,
            'note' => $invoiceData['note'],
            'created_by' => auth()->user()->name,

        ]);
        return $invoiceDetails;
    }
    public function storeInvoiceAttachments($invoiceData, $invoice_id){
        $file_name = $invoiceData['pic']->getClientOriginalName();
        $attachment = InvoiceAttachment::create([
            'file_name' =>  $file_name,
            'invoice_number' => $invoiceData['invoice_number'],
            'created_by' => auth()->user()->name,
            'invoice_id' => $invoice_id
        ]);
        // Store the attached file in the public/Attachments directory, organizing files by invoice number:
        // The 'Attachments' directory contains subdirectories named after the actual invoice number.
        $invoiceData['pic']->move(public_path('Attachments/' . $invoiceData['invoice_number']), $file_name);
        return  $attachment;
    }
    public function createInvoiceDetail($invoiceData)
    {
        $value_status = $this->determineValueStatus($invoiceData['status']);
        InvoicesDetails::create([
            "invoice_id" => $invoiceData['invoice_id'],
            "invoice_number" => $invoiceData['invoice_number'],
            "invoice_date" => $invoiceData['invoice_date'],
            "section" => $invoiceData['section'],
            "product" => $invoiceData['product'],
            "status" => $invoiceData['status'],
            "value_status" =>  $value_status,
            "note" => $invoiceData['note'],
            "payment_date" => $invoiceData['payment_date'],
            "created_by" => auth()->user()->name
        ]);
    }

     // Determine the value_status based on the status passed
     public function determineValueStatus($status)
     {
         return $status == Invoice::STATUS_PAID ? Invoice::STATUS_PAID_VALUE : Invoice::STATUS_PARTIAL_PAID_VALUE;
     }

     public function updateInvoiceStatus($data, $invoice)
     {
         // Determine the value_status based on thestatus provided in the request
         $value_status = $this->determineValueStatus($data['status']);
         $invoice->update([
             'status' => $data['status'],
             'value_status' =>  $value_status,
             'payment_date' => $data['payment_date']
         ]);
         // Call the method to create a new row in invoice_details
         $this->createInvoiceDetail($data);
         session()->flash('status_update');
         return redirect('/invoices');
     }

}
