<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum', 'stat.req')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::post('/category',[CategoryController::class, 'store']);
    Route::get('/filter', [ProductController::class, 'filter']); 
    Route::get('/sort', [ProductController::class, 'sort']); 
    Route::get('/search', [ProductController::class, 'search']); 
    Route::post('/logout', [AuthController::class, 'logout']);
});