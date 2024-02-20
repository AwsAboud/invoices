<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\InvoiceAttachmentController;
use App\Models\InvoicesDetails;
use Illuminate\Support\Facades\File;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('invoices.index', [
            'invoices' => Invoice::with('section')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('invoices.create', [
            'sections' => Section::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        //Refactor this function by using a service or repository class to handle the creation of InvoicesDetails & InvoiceAttachments

        // Begin a database transaction
        DB::beginTransaction();
        try {
            Invoice::create([
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'section_id' =>  $request->Section,
                'product' => $request->product,
                'collection_amount' =>  $request->collection_amount,
                'commission_amount' => $request->commission_amount,
                'discount' => $request->discount,
                'value_vat' => $request->value_vat,
                'rate_vat' => $request->rate_vat,
                'total' => $request->Total,
                'status' => Invoice::STATUS_NOT_PAID,
                'value_status' => Invoice::STATUS_NOT_PAID_VALUE,
                'note' => $request->note
            ]);
            //Retrieve the ID of the most recently added invoice
            $invoice_id = Invoice::latest('id')->value('id');
            $invoicesDetails = new InvoicesDetailsController();
            $invoicesDetails->store($request, $invoice_id);
            if ($request->hasFile('pic')) {
                $invoiceAttachment = new InvoiceAttachmentController();
                $invoiceAttachment->store($request, $invoice_id);
            }
            // Commit the transaction if everything is successful
            DB::commit();
            // Redirect with success message
            return redirect()->back()->with(['add' => 'تم اضافة الفاتورة بنجاح ']);
        } catch (QueryException $e) {
            // An exception occurred, rollback the transaction
            DB::rollBack();
            // Redirect with an error message
            return redirect()->back()->with(['error' => 'حدث خطأ أثناء إضافة الفاتورة']);
        }
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
    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', [
            'invoice' => $invoice,
            'sections' => Section::all()
        ]);
    }

    /**
     * Show the form for editing payment status of the specified invoice.
     */
    public function editStatus(Invoice $invoice)
    {
        return view('invoices.edit-status', [
            'invoice' => $invoice
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'collection_amount' => $request->collection_amount,
            'commission_amount' => $request->commission_amount,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);
        return redirect()->back()->with(['edit' => 'تم تعديل الفاتورة بنجاح ']);
    }
    /**
     * Update the specified invoice status in storage.
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        // Determine the value_status based on thestatus provided in the request
        $value_status = $this->determineValueStatus($request->status);
        $invoice->update([
            'status' => $request->status,
            'value_status' =>  $value_status,
            'payment_date' => $request->payment_date
        ]);
        // Call the method to create a new row in invoice_details
        $this->createInvoiceDetail($request);
        session()->flash('status_update');
        return redirect('/invoices');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //delete the Attachments from the storage
        $directory_path = public_path('Attachments'.'/'.$request->invoice_number);
        if (File::exists($directory_path)) {
            File::deleteDirectory($directory_path);
        }
        else {
            return redirect()->back()->with(['error' => 'المرفق غير موجود']);
        }

        Invoice::findOrFail($request->invoice_id)->forceDelete();
        session()->flash('delete');
        return redirect()->back();
    }

    /**
     * get product that associated with the given section_id to use it in invoices/create view .
     */
    public function getProducts($id)
    {
        $products = Product::where('section_id', $id)->pluck('product_name', 'id');
        return json_encode($products);
    }
    /*
     * This method should be called after updateStatus method to insert the updated invoice into
        the invoice details table.
    */
    private function createInvoiceDetail($request)
    {
        $value_status = $this->determineValueStatus($request->status);
        InvoicesDetails::create([
            "invoice_id" => $request->invoice_id,
            "invoice_number" => $request->invoice_number,
            "invoice_date" => $request->invoice_date,
            "section" => $request->section,
            "product" => $request->product,
            "status" => $request->status,
            "value_status" =>  $value_status,
            "note" => $request->note,
            "payment_date" => $request->payment_date,
            "created_by" => auth()->user()->name
        ]);
    }
    //Determine the value_status based on the status passed
    private function determineValueStatus($status)
    {
        return $status == Invoice::STATUS_PAID ? Invoice::STATUS_PAID_VALUE : Invoice::STATUS_PARTIAL_PAID_VALUE;
    }
}
