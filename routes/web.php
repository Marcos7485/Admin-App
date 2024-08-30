<?php

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [MainController::class, 'loginview'])->name('login');

Route::post('/loginrequest', [MainController::class, 'login']);

Route::get('/logout', function () {
    return redirect()->route('dashboard');
});
Route::post('/logout', [MainController::class, 'logout'])->name('logout');





Route::middleware(['auth'])->group(function () {


    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');


    Route::get('/new/cliente', [ClientesController::class, 'ClientesForm'])->name('FormCliente');
    Route::post('/new/cliente', [ClientesController::class, 'NewCliente'])->name('cliente.new');

    Route::get('/datos-clientes', [ClientesController::class, 'getDatosCliente']);
    Route::get('/edit/cliente/{id}', [ClientesController::class, 'ClienteInfo']);
    Route::post('/modify/cliente', [ClientesController::class, 'ClienteEdit']);
});
