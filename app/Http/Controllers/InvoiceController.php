<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Exports\InvoicesExport;
use App\Models\InvoicesDetails;
use App\Models\InvoiceAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use App\Http\Requests\StoreInvoiceRequest;

class InvoiceController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:', ['only' => ['index']]);
        $this->middleware('permission:اضافة-فاتورة', ['only' => ['create','store']]);
        $this->middleware('permission:تعديل-فاتورة', ['only' => ['edit','update']]);
        $this->middleware('permission:حذف-فاتورة', ['only' => ['destroy']]);
        $this->middleware('permission:تصدير-Excel', ['only' => ['export']]);
        $this->middleware('permission:طباعة-فاتور', ['only' => ['print']]);
        $this->middleware('permission:تغير-حالة-الدفع', ['only' => ['updateStatus']]);
    }

    public function index()
    {
        // Enhance performance by selecting only necessary fields from the eager-loaded relation.
        $invoices = Invoice::with(['section' => function ($query) {
            $query->select('id', 'section_name');
        }])->get();

        return view('invoices.index', [
            'invoices' => $invoices
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
            $invoice = Invoice::create([
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

            $this->storeInvoiceDetails($request, $invoice->id);
            if ($request->hasFile('pic')) {
                $this->storeInvoiceAttachments($request, $invoice->id);
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
        $directory_path = public_path('Attachments' . '/' . $request->invoice_number);
        if (File::exists($directory_path)) {
            File::deleteDirectory($directory_path);
        }
        Invoice::findOrFail($request->invoice_id)->forceDelete();
        session()->flash('delete');
        return redirect()->back();
    }

    // Get the product that associated with the given section_id to use it in invoices/create view .
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

    // Determine the value_status based on the status passed
    private function determineValueStatus($status)
    {
        return $status == Invoice::STATUS_PAID ? Invoice::STATUS_PAID_VALUE : Invoice::STATUS_PARTIAL_PAID_VALUE;
    }

    // Display a listing of the paid Invoices.
    public function paidInvoices()
    {
        return view('invoices.paid_invoices', [
            'invoices' => Invoice::with('section')->where('value_status', Invoice::STATUS_PAID_VALUE)->get()
        ]);
    }

    // Display a listing of the partially paid inoices.
    public function partialPaidInvoices()
    {
        return view('invoices.partial_paid_invoices', [
            'invoices' => Invoice::with('section')->where('value_status', Invoice::STATUS_PARTIAL_PAID_VALUE)->get()
        ]);
    }

    // Display a listing of the not paid invoices.
    public function notPaidInvoices()
    {
        return view('invoices.not_paid_invoices', [
            'invoices' => Invoice::with('section')->where('value_status', Invoice::STATUS_NOT_PAID_VALUE)->get()
        ]);
    }

    public function print(Invoice $invoice)
    {
        return view('invoices.print', [
            'invoice' => $invoice
        ]);
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }

    private function storeInvoiceDetails($request, $invoice_id){
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
    private function storeInvoiceAttachments($request, $invoice_id){
        $file_name =  $request->file('pic')->getClientOriginalName();
        InvoiceAttachment::create([
            'file_name' =>  $file_name,
            'invoice_number' => $request->invoice_number,
            'created_by' => auth()->user()->name,
            'invoice_id' => $invoice_id
        ]);
        // Store the attached file in the public/Attachments directory, organizing files by invoice number:
        // The 'Attachments' directory contains subdirectories named after the actual invoice number.
        $request->pic->move(public_path('Attachments/' . $request->invoice_number), $file_name);
    }
}
