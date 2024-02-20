<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

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
    return view('email.notification',[
        'status' => 'info',
        'procurement_title' => "HELLO",
        'nodin' => "NODIN",
        'title' => 'Pengajuan Berhasil Dibuat',
        'messages' => 'Pengajuan Anda Sudah diteruskan untuk di approval',
    ]);
});

Route::get('/d', function () {
    $pdf = Pdf::loadView('pdf.persetujuan',[
        'status' => 'info',
        'procurement_title' => "HELLO",
        'nodin' => "NODIN",
        'title' => 'Pengajuan Berhasil Dibuat',
        'messages' => 'Pengajuan Anda Sudah diteruskan untuk di approval',
    ]);
    return $pdf->download('invoice.pdf');
});

// Route::get('/', function () {
//     return inertia('Welcome');
// });
