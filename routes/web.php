<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MainController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [MainController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::post('/dashboard', [MainController::class, 'store'])->middleware(['auth'])->name('dashboard');
Route::get('/dashboard/delete/{id}', [MainController::class, 'destroy'])->middleware(['auth'])->name('dashboard.destroy');

require __DIR__.'/auth.php';
