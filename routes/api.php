<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
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

Route::get('index',[AuthController::class,'index']);
Route::post('register',[AuthController::class,'register']);
Route::post('log',[AuthController::class,'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('store', [ArticleController::class, 'store']);
    Route::get('show', [ArticleController::class, 'show']);
    Route::post('update', [ArticleController::class, 'update']);
    Route::post('destroy', [ArticleController::class, 'destroy']);
    Route::get('getDataById/{id}', [ArticleController::class, 'getDataById']);
    
    Route::post('encrypt', [ArticleController::class, 'encrypt']);
    Route::post('decrypt', [ArticleController::class, 'decrypt']);
});
