<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceAttachmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;

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
Route::resource('/invoices', InvoiceController::class)->middleware('auth');
Route::resource('/sections', SectionController::class)->middleware('auth');
Route::resource('/products', ProductController::class)->middleware('auth');
Route::get('/section/{id}',[InvoiceController::class,'getProducts'] );
Route::get('/invoice/details/{invoice_id}',[InvoicesDetailsController::class, 'show']);
Route::get('/view-invoice-file/{invoice_number}/{file_name}',[InvoicesDetailsController::class, 'open_file']);
Route::get('/dawnload-invoice-file/{invoice_number}/{file_name}',[InvoicesDetailsController::class, 'dawnload_file']);
Route::post('/invoice/details/attachment/delete-file',[InvoiceAttachmentController::class, 'destroy'])->name('file.destroy');
//this should be the last route if you but any route after it, it will not work
Route::get('/{page}', [AdminController::class,'index']);

