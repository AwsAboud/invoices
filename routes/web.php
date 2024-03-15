<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
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
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::delete('/invoices/archive/{invoice}',[InvoiceArchiveController::class, 'destroy'])
    ->withTrashed();
Route::patch('/invoices/archive/restore/{invoice}',[InvoiceArchiveController::class, 'restoreArchivedInvoice'])
    ->withTrashed();
Route::get('/invoices/archive',[InvoiceArchiveController::class, 'index'])->name('invoices-archive-list');

Route::get('/invoices/paid', [ InvoiceController::class,'paidInvoices']);
Route::get('/invoices/not-paid', [ InvoiceController::class,'notPaidInvoices']);
Route::get('/invoices/partial-paid', [ InvoiceController::class,'partialPaidInvoices']);
Route::get('/invoices/{invoice}/edit-status', [ InvoiceController::class,'editStatus'])->name('invoice.edit-status');
Route::post('/invoices/update-status/{invoice}', [ InvoiceController::class,'updateStatus'])->name('invoice.update-status');
Route::get('/invoices/{invoice}/print', [ InvoiceController::class,'print']);
Route::get('invoices/export/', [InvoiceController::class, 'export']);
Route::resource('/invoices', InvoiceController::class)->middleware('auth');

Route::resource('/products', ProductController::class)->middleware('auth');

Route::resource('/sections', SectionController::class)->middleware('auth');

Route::get('/section/{id}',[InvoiceController::class,'getProducts'] );
Route::get('/invoice/details/{invoice_id}',[InvoicesDetailsController::class, 'show']);
Route::get('/view-invoice-file/{invoice_number}/{file_name}',[InvoicesDetailsController::class, 'open_file']);
Route::get('/dawnload-invoice-file/{invoice_number}/{file_name}',[InvoicesDetailsController::class, 'dawnload_file']);

Route::post('/invoice/details/attachment/delete-file',[InvoiceAttachmentController::class, 'destroy'])->name('file.destroy');

Route::middleware('auth')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});

//this should be the last route if you but any route after it, it will not work
Route::get('/{page}', [AdminController::class,'index']);

//require __DIR__.'/auth.php';
