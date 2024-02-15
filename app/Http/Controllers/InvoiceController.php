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
        try{
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
            $invoicesDetails->store($request,$invoice_id);
            if ($request->hasFile('pic')) {
                $invoiceAttachment = new InvoiceAttachmentController();
                $invoiceAttachment->store($request, $invoice_id);
            }
            // Commit the transaction if everything is successful
            DB::commit();
            // Redirect with success message
            return redirect()->back()->with(['add' => 'تم اضافة الفاتورة بنجاح ']);

        }catch(QueryException $e){
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    /**
     * get product that associated with the given section_id to use it in invoices/create view .
     */
    public function getProducts($id)
    {
        $products = Product::where('section_id', $id)->pluck('product_name', 'id');
        return json_encode($products);
    }
}
