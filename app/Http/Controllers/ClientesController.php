<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarCliente;
use App\Models\Clientes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientesController extends Controller
{
    public function ClientesForm()
    {
        return Inertia::location(route('dashboard'));
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

    public function ClienteInfo($id)
    {
        $cliente = Clientes::where('id', $id)->first();

        return response()->json($cliente);
    }

    public function ClienteEdit(Request $request)
    {
        $cliente = Clientes::find($request->id);
        $cliente->name = $request->name;
        $cliente->dni = $request->dni;
        $cliente->phone = $request->phone;
        $cliente->address = $request->address;
        $cliente->localidad = $request->localidad;
        $cliente->comercio_address = $request->comercio_address;
        $cliente->comercio_localidad = $request->comercio_localidad;
        $cliente->comercio_tipo = $request->comercio_tipo;
        $cliente->recorrido = $request->recorrido;
    
        // Guardar los cambios
        $cliente->save();

        return response()->json(['message' => 'Cliente editado correctamente']);
    }
}
