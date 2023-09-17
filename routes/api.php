<?php

use App\Http\Controllers\Api\MainController;
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

Route::group(['prefix' => 'v1'], function () {
    Route::get('/pesan', [MainController::class, 'getAllPesan']);
    Route::post('/pesan/create', [MainController::class, 'createPesan']);
    Route::post('/pesan/update', [MainController::class, 'updatePesan']);
    Route::delete('/pesan/delete/{id}', [MainController::class, 'deletePesan']);
});
