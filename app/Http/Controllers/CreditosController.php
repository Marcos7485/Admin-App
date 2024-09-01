<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditoRequest;
use App\Models\Clientes;
use App\Models\Creditos;
use App\Services\CreditosSrv;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CreditosController extends Controller
{
    protected $CreditosSrv;

    public function __construct(CreditosSrv $CreditosSrv)
    {
        $this->CreditosSrv = $CreditosSrv;
    }

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
        $totalCredito = $this->CreditosSrv->calcularValorCredito($request->interes, $request->credito);

        $cuotas_pagas = 0; // crear servicio con DB de pagos para q reste los pagados.
        $pagado = 0; // crear servicio con DB de pagos para q sume el valor.

        $credito = Creditos::find($request->id);
        $credito->cliente = $request->cliente;
        $credito->credito = $request->credito;
        $credito->interes = $request->interes;
        $credito->total_credito = $totalCredito;
        $credito->cuotas = $request->cuotas;
        $credito->cuotas_restantes = ($request->cuotas - $cuotas_pagas);
        $credito->cuotas_valor = $this->CreditosSrv->calcularValorCuotas($totalCredito, $request->cuotas);
        $credito->modalidad = $request->modalidad;
        $credito->pagado = $pagado;

        $valor_restante = (int)$totalCredito - (int)$pagado;

        $credito->pago_restante = $valor_restante;
        $credito->inicio = $request->inicio;
        $credito->lugar_cobro = $request->lugar_cobro;
        // $credito->status = Verificador de Status por pago, si fue pagado, 
        // pendiente sino, refinanciado si fue refinanciado y renovado si fue renovado. en el SRV.

        // Guardar los cambios
        $credito->save();

        return response()->json(['message' => 'Credito editado correctamente']);
    }

    public function NewCredito(CreditoRequest $request)
    {

        // $verificador = Creditos::where('cliente', $request->cliente)->where('active', 1)->first();
        //  Crearlo en el request.

        $totalCredito = $this->CreditosSrv->calcularValorCredito($request->interes, $request->credito);

        $credito = new Creditos();
        $credito->cliente = $request->cliente;
        $credito->credito = $request->credito;
        $credito->interes = $request->interes;
        $credito->total_credito = $totalCredito;
        $credito->cuotas = $request->cuotas;
        $credito->cuotas_restantes = $request->cuotas;
        $credito->modalidad = $request->modalidad;
        $credito->cuotas_valor = $this->CreditosSrv->calcularValorCuotas($totalCredito, $request->cuotas);
        $credito->lugar_cobro = $request->lugar_cobro;
        $credito->pago_restante = $totalCredito;
        $credito->pagado = 0;
        $credito->inicio = $request->inicio;
        $credito->status = 'Pendiente';
        $credito->save();

        return response()->json(['message' => 'Credito creado correctamente']);
    }

    public function CuotasValue($modalidad)
    {
        $cuotas = [];

        if ($modalidad == 'Diaria') {
            $cuotas = [
                ['value' => 15],
                ['value' => 30],
                ['value' => 45],
                ['value' => 60],
                ['value' => 75],
                ['value' => 90],
            ];
        } elseif ($modalidad == 'Semanal') {
            $cuotas = [
                ['value' => 2],
                ['value' => 4],
                ['value' => 6],
                ['value' => 8]
            ];
        } elseif ($modalidad == 'Articulo') {
            $cuotas = [
                ['value' => 30],
                ['value' => 60],
                ['value' => 90],
                ['value' => 100],
                ['value' => 120],
                ['value' => 140],
                ['value' => 160],
                ['value' => 180],
                ['value' => 200],
                ['value' => 220]
            ];
        }

        return response()->json($cuotas);
    }
}
