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

    public function CreditoEdit(Request $request)
    {
        $interes = 1.3;
        $interes_mora = 1.6;

        $valordelcredito = (float) $request->credito;
        $totalCredito = number_format($valordelcredito * $interes, 2, '.', '');
        $cuotas_valor = number_format($totalCredito / (float)$request->cuotas, 2, '.', '');

        $cuotas_pagas = 5; // crear servicio con DB de pagos para q reste los pagados.

        $credito = Creditos::find($request->id);
        $credito->cliente = $request->cliente;
        $credito->credito = $request->credito;
        $credito->interes = $interes;
        $credito->interes_mora = $interes_mora;
        $credito->total_credito = $totalCredito;
        $credito->cuotas = $request->cuotas;
        $credito->cuotas_restantes = ($request->cuotas - $cuotas_pagas);
        $credito->cuotas_valor = $cuotas_valor;
        $credito->modalidad = $request->modalidad;
        $credito->inicio = $request->inicio;
        $credito->lugar_cobro = $request->lugar_cobro;
        $credito->status = $request->status;
    
        // Guardar los cambios
        $credito->save();

        return response()->json(['message' => 'Credito editado correctamente']);
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
