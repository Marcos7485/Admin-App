<?php

use App\Http\Controllers\AppSetupController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CreditosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\RecorridosController;
use App\Http\Controllers\VendedorController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', [MainController::class, 'loginview'])->name('login');

Route::post('/loginrequest', [MainController::class, 'login']);

Route::get('/logout', function () {
    return redirect()->route('dashboard');
});
Route::post('/logout', [MainController::class, 'logout'])->name('logout');





Route::middleware(['auth'])->group(function () {


    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');


    // Clientes:
    Route::get('/new/cliente', [ClientesController::class, 'ClientesForm']);
    Route::post('/new/cliente', [ClientesController::class, 'NewCliente'])->name('cliente.new');

    Route::get('/datos-clientes', [ClientesController::class, 'getDatosClientes']);
    Route::get('/info/cliente/{id}', [ClientesController::class, 'ClienteInfo']);
    Route::post('/modify/cliente', [ClientesController::class, 'ClienteEdit']);

    // Creditos:
    Route::get('/cuotas/{modalidad}', [CreditosController::class, 'CuotasValue']);

    Route::get('/datos-creditos', [CreditosController::class, 'getDatosCreditos']);
    Route::post('/new/credito', [CreditosController::class, 'NewCredito'])->name('credito.new');
    Route::get('/new/credito', [CreditosController::class, 'CreditoForm']);

    Route::get('/info/credito/{id}', [CreditosController::class, 'CreditoInfo']);
    Route::get('/info/fichero/{id}', [CreditosController::class, 'FicheroInfo']);
    Route::post('/modify/credito', [CreditosController::class, 'CreditoEdit']);

    Route::post('/refinanciar/credito', [CreditosController::class, 'Refinanciar']);
    Route::post('/renovar/credito', [CreditosController::class, 'Renovar']);

    Route::get('/info/fechasCuotas/{idCliente}', [CreditosController::class, 'fechasCuotas']);

    // Pagos:
    Route::post('/new/pago', [PagosController::class, 'NewPago'])->name('pago.new');
    Route::get('/new/pago', [PagosController::class, 'PagoForm']);

    Route::get('/datos-pagos', [PagosController::class, 'getDatosPagos']);
    Route::get('/info/pago/{id}', [PagosController::class, 'PagosInfo']);
    Route::post('/modify/pago', [PagosController::class, 'PagoEdit']);


    // Recorridos:
    Route::get('/recorrido/{recorrido}', [RecorridosController::class, 'RecorridoHoy']);
    Route::get('/datos-recorridos', [RecorridosController::class, 'getDatosRecorridos']);
    Route::get('/info/recorrido/{id}', [RecorridosController::class, 'RecorridoInfo']);

    // App:
    Route::get('/setup/recorridos', [AppSetupController::class, 'Recorridos']);
    Route::get('/setup/vendedores', [AppSetupController::class, 'Vendedores']);
    Route::get('/setup/cobradores', [AppSetupController::class, 'Cobradores']);

    Route::middleware([AdminMiddleware::class])->group(function () {
        Route::post('/setup/password', [AppSetupController::class, 'UpdatePassword']);
        Route::post('/setup/update', [AppSetupController::class, 'UpdateConfig']);
    });


    // Vendedor
    Route::get('/vendedor/{id}', [VendedorController::class, 'getResumeVendedor']);
});
