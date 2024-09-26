<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarCliente;
use App\Models\Clientes;
use App\Services\CreditosSrv;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientesController extends Controller
{

    protected $CreditosSrv;

    public function __construct(CreditosSrv $CreditosSrv)
    {
        $this->CreditosSrv = $CreditosSrv;
    }

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
        $cliente->prenda = $request->prenda;
        $cliente->comercio_address = $request->comercio_address;
        $cliente->comercio_localidad = $request->comercio_localidad;
        $cliente->comercio_tipo = $request->comercio_tipo;
        $cliente->recorrido = $request->recorrido;
        $cliente->vendedor = $request->vendedor;
        $cliente->created_by = Auth::user()->id;
        $cliente->save();

        return response()->json(['message' => 'Cliente guardado correctamente']);
    }

    public function getDatosClientes()
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
        $cliente->prenda = $request->prenda;
    
        // Guardar los cambios
        $cliente->save();

        return response()->json(['message' => 'Cliente editado correctamente']);
    }

    public function ClienteDestroy(Request $request){
        return $this->CreditosSrv->deleteCliente($request->id);
    }
}
