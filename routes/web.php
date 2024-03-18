<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\InvoiceReportController;
use App\Http\Controllers\CustomerReportController;
use App\Http\Controllers\InvoiceArchiveController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\InvoiceAttachmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

//disable registration
Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {

    //==============================Home====================================================
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //==============================Invoices====================================================
    Route::delete('/invoices/archive/{invoice}', [InvoiceArchiveController::class, 'destroy'])
        ->withTrashed();
    Route::patch('/invoices/archive/restore/{invoice}', [InvoiceArchiveController::class, 'restoreArchivedInvoice'])
        ->withTrashed();
    Route::get('/invoices/archive', [InvoiceArchiveController::class, 'index'])->name('invoices-archive-list');
    Route::get('/invoices/paid', [InvoiceController::class, 'paidInvoices']);
    Route::get('/invoices/not-paid', [InvoiceController::class, 'notPaidInvoices']);
    Route::get('/invoices/partial-paid', [InvoiceController::class, 'partialPaidInvoices']);
    Route::get('/invoices/{invoice}/edit-status', [InvoiceController::class, 'editStatus'])->name('invoice.edit-status');
    Route::post('/invoices/update-status/{invoice}', [InvoiceController::class, 'updateStatus'])->name('invoice.update-status');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print']);
    Route::get('invoices/export/', [InvoiceController::class, 'export']);
    Route::resource('/invoices', InvoiceController::class);
    Route::get('invoice/section/{id}', [InvoiceController::class, 'getProducts']);

    //==============================Products====================================================
    Route::resource('/products', ProductController::class);

    //==============================Invoices Reports====================================================
    Route::get('reports/invoices', [InvoiceReportController::class, 'index']);
    Route::post('reports/invoices/search', [InvoiceReportController::class, 'searchForInvoices']);

    //==============================Customers Reports====================================================
    Route::get('reports/customers', [CustomerReportController::class, 'index']);
    Route::post('reports/customers/search', [CustomerReportController::class, 'SearchForCustomers']);

    //==============================Sections====================================================
    Route::resource('/sections', SectionController::class);


    //==============================Invoices Details====================================================
    Route::get('/invoice/details/{invoice_id}', [InvoicesDetailsController::class, 'show']);
    Route::get('/view-invoice-file/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'open_file']);
    Route::get('/dawnload-invoice-file/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'dawnload_file']);

    //==============================Sections====================================================
    Route::post('/invoice/details/attachment/delete-file', [InvoiceAttachmentController::class, 'destroy'])->name('file.destroy');

    //==============================Roles====================================================
    Route::resource('roles', RoleController::class);

    //==============================Users====================================================
    Route::resource('users', UserController::class);

    //this should be the last route if you but any route after it, it will not work
    Route::get('/{page}', [AdminController::class, 'index']);
});
