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
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    public InvoiceService $invoiceService;
    function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
        $this->middleware('permission:عرض-الفواتير', ['only' => ['index']]);
        $this->middleware('permission:اضافة-فاتورة', ['only' => ['create','store']]);
        $this->middleware('permission:تعديل-فاتورة', ['only' => ['edit','update']]);
        $this->middleware('permission:حذف-فاتورة', ['only' => ['destroy']]);
        $this->middleware('permission:تصدير-Excel', ['only' => ['export']]);
        $this->middleware('permission:طباعة-فاتورة', ['only' => ['print']]);
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
       try{
            $this->invoiceService->store($request->validated());
            return redirect()->back()->with(['add' => 'تم اضافة الفاتورة بنجاح ']);
        }catch(\Exception $e){
            return redirect()->back()->with(['error' => 'حدث خطأ أثناء إضافة الفاتورة' . $e->getMessage()]);
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
            'rate_vat' => $request->Rate_VAT,
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
        $this->invoiceService->updateInvoiceStatus($request, $invoice);
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


    // Display a listing of the paid Invoices.
    public function paidInvoices()
    {
        $invoices = Invoice::with('section')->where('value_status', Invoice::STATUS_PAID_VALUE)->get();
        return view('invoices.paid_invoices', [
            'invoices' => $invoices
        ]);
    }

    // Display a listing of the partially paid inoices.
    public function partialPaidInvoices()
    {
        $invoices = Invoice::with('section')->where('value_status', Invoice::STATUS_PARTIAL_PAID_VALUE)->get();
        return view('invoices.partial_paid_invoices', [
            'invoices' => $invoices
        ]);
    }

    // Display a listing of the not paid invoices.
    public function notPaidInvoices()
    {
        $invoices = Invoice::with('section')->where('value_status', Invoice::STATUS_NOT_PAID_VALUE)->get();
        return view('invoices.not_paid_invoices', [
            'invoices' => $invoices
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
}
