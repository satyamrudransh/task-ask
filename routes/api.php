<?php

use App\Http\Controllers\API\AuthService\AuthController;
use App\Http\Controllers\API\Product\ProductController;
use App\Http\Controllers\API\SubCategory\SubCategoryController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// task url
// Route::middleware(['service-auth'])->group(function () {

// Route::get('/task', 'API\Task\TaskController@index');

Route::middleware('auth:api')->get('/user', [AuthController::class, 'getUser']);


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/check-session', [AuthController::class, 'checkSession']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');


Route::post('categories', [CategoryController::class, 'store']);
Route::get('categories', [CategoryController::class, 'index']);
Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
Route::put('categories/{id}', [CategoryController::class, 'update']);

Route::get('active-categories', [CategoryController::class, 'activeIndex']);

Route::post('subcategories', [SubCategoryController::class, 'store']);
Route::get('subcategories', [SubCategoryController::class, 'indexSubCategory']);
Route::get('cat-subcategories', [SubCategoryController::class, 'getSubcategories']);

Route::put('subcategories/{id}', [SubCategoryController::class, 'update']);

Route::get('subcategories/{id}', [SubCategoryController::class, 'show']);
Route::delete('subcategories/{id}', [SubCategoryController::class, 'destroy']);

Route::get('active-subcategories/{id}', [SubCategoryController::class, 'getActiveSubcategoriesByCategory']);


Route::post('product', [ProductController::class, 'store']);

Route::get('/product', [ProductController::class, 'index']);

// });