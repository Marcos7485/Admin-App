<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditoRequest;
use App\Models\Clientes;
use App\Models\Creditos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CreditosController extends Controller
{
    public function CreditoForm()
    {
        return Inertia::location(route('dashboard'));
    }

    public function getDatosCreditos()
    {
        $creditos = Creditos::all();

        return response()->json($creditos);
    }

    public function CreditoInfo($id)
    {

        $credito = Creditos::where('id', $id)->first();

        return response()->json($credito);
    }

    public function CreditoEdit(Request $request){

    }

    public function NewCredito(CreditoRequest $request)
    {
        $interes = 1.3;
        $interes_mora = 1.6;

        $valordelcredito = (float) $request->credito;
        $totalCredito = number_format($valordelcredito * $interes, 2, '.', '');
        $cuotas_valor = number_format($totalCredito / (float)$request->cuotas, 2, '.', '');

        $credito = new Creditos();
        $credito->cliente = $request->cliente;
        $credito->credito = $request->credito;
        $credito->interes = $interes;
        $credito->interes_mora = $interes_mora;
        $credito->total_credito = $totalCredito;
        $credito->cuotas = $request->cuotas;
        $credito->cuotas_restantes = $request->cuotas;
        $credito->modalidad = $request->modalidad;
        $credito->cuotas_valor = $cuotas_valor;
        $credito->lugar_cobro = $request->lugar_cobro;
        $credito->pagado = 0;
        $credito->inicio = $request->inicio;
        $credito->status = 'Pendiente';
        $credito->save();

        return response()->json(['message' => 'Credito creado correctamente']);
    }
}
