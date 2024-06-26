<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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
    return view('translate');
});


Route::get('/translate', [HomeController::class, 'translate'])->name('translate');
Route::post('/deepl', [HomeController::class, 'deepl'])->name('deepl');
Route::post('/gpt', [HomeController::class, 'gpt'])->name('gpt');
Route::post('/translateTotal', [HomeController::class, 'translateTotal'])->name('translateTotal');