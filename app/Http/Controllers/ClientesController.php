<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarCliente;
use App\Models\Clientes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientesController extends Controller
{
    public function ClientesForm()
    {
        return Inertia::render('dashboard/clientes/FormCliente');
    }

    public function NewCliente(RegistrarCliente $request)
    {

        $cliente = new Clientes();
        $cliente->name = $request->name;
        $cliente->dni = $request->dni;
        $cliente->phone = $request->phone;
        $cliente->address = $request->address;
        $cliente->localidad = $request->localidad;
        $cliente->comercio_address = $request->comercio_address;
        $cliente->comercio_localidad = $request->comercio_localidad;
        $cliente->comercio_tipo = $request->comercio_tipo;
        $cliente->recorrido = $request->recorrido;
        $cliente->created_by = Auth::user()->id;
        $cliente->save();

        return response()->json(['message' => 'Cliente guardado correctamente']);
    }

    public function getDatosCliente()
    {
        $clientes = Clientes::all();

        return response()->json($clientes);
    }
}
