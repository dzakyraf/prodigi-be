<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BotTelegramController;
use App\Http\Controllers\API\LakdanController;
use App\Http\Controllers\API\PenyelesaianController;
use App\Http\Controllers\API\ProcurementController;
use App\Http\Controllers\API\RendanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/forgot-password', 'forgotPassword');
});

Route::middleware('auth:sanctum')
    ->prefix('procurement')->controller(ProcurementController::class)->group(function () {
        Route::post('/create', 'newProposal');
        Route::post('/review', 'reviewProposal');
        Route::get('/document_list', 'getDocumentList');
        Route::get('/procurement_list', 'listProposal');
        Route::get('/procurement_details', 'detailsProposal');
        // Route::get('/download', 'download');
    });

Route::middleware('auth:sanctum')
    ->prefix('rendan')->controller(RendanController::class)->group(function () {
        Route::post('/draft', 'saveDraft');
        Route::post('/submit', 'submit');
    });

Route::middleware('auth:sanctum')
    ->prefix('lakdan')->controller(LakdanController::class)->group(function () {
        Route::post('/draft', 'saveDraft');
        Route::post('/submit', 'submit');
    });

Route::middleware('auth:sanctum')
    ->prefix('penyelesaian')->controller(PenyelesaianController::class)->group(function () {
        Route::post('/draft', 'saveDraft');
        Route::post('/submit', 'submit');
    });


Route::resource('position', App\Http\Controllers\API\PositionAPIController::class)
    ->except(['create', 'edit']);


Route::resource('division', App\Http\Controllers\API\DivisionAPIController::class)
    ->except(['create', 'edit']);

Route::get('setWebhook', [BotTelegramController::class, 'setWebhook']);
Route::post('prodigibot/webhook', [BotTelegramController::class, 'commandHandlerWebHook']);


Route::resource('unit-mpp', App\Http\Controllers\API\UnitMppAPIController::class)
    ->except(['create', 'edit']);
