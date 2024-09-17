<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditoRequest;
use App\Models\Clientes;
use App\Models\Creditos;
use App\Models\Ficheros;
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
        $creditos = Creditos::where('active', 1)->get();
        $clientes = Clientes::all();
        $this->CreditosSrv->TransformIdEnName($creditos, $clientes);
        
        return response()->json($creditos);
    }

    public function CreditoInfo($id)
    {
        $credito = Creditos::where('id', $id)->where('active', 1)->first();
        return response()->json($credito);
    }

    public function FicheroInfo($id){
        
        $fichero = Ficheros::where('cliente', $id)->where('active', 1)->first();
        if(empty($fichero)){
            $credito = Creditos::where('id', $id)->where('active', 1)->first();
            $fichero = Ficheros::where('cliente', $credito->cliente)->where('active', 1)->first();
        }
        return response()->json($fichero);
    }

    public function CreditoEdit(Request $request)
    {
        $totalCredito = $this->CreditosSrv->calcularValorCredito($request->interes, $request->credito);
        $this->CreditosSrv->DesactivarFichero($request->cliente);
        $cliente = $request->cliente;


        $cuotas_pagas = 0; // crear servicio con DB de pagos para q reste los pagados.
        $pagado = $this->CreditosSrv->verificarPagos($request->id);

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
        $credito->save();

        $this->CreditosSrv->CrearFichero($cliente);
        return response()->json(['message' => 'Credito editado correctamente']);
    }

    public function Refinanciar(Request $request)
    {
        $this->CreditosSrv->Refinanciar($request->cliente);
        $this->CreditosSrv->DesactivarFichero($request->cliente);
        $cliente = $request->cliente;

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
        $credito->status = 'Refinanciado';
        $credito->save();

        $this->CreditosSrv->CrearFichero($cliente);

        return response()->json(['message' => 'Credito refinanciado correctamente']);
    }

    public function Renovar(Request $request)
    {
        
        $this->CreditosSrv->DesactivarFichero($request->cliente);
        $cliente = $request->cliente;

        $totalCredito = $this->CreditosSrv->calcularValorCredito($request->interes, $request->credito);

        $dineroCancelado = $this->CreditosSrv->Renovar($request->cliente);
        $dineroARecibir = ($request->credito - $dineroCancelado);

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
        $credito->status = 'Renovado';
        $credito->dinero_cancelado = $dineroCancelado;
        $credito->dinero_arecibir = $dineroARecibir;
        $credito->save();

        $this->CreditosSrv->CrearFichero($cliente);

        return response()->json(['message' => 'Credito refinanciado correctamente']);
    }


    public function NewCredito(CreditoRequest $request)
    {

        $totalCredito = $this->CreditosSrv->calcularValorCredito($request->interes, $request->credito);
        $cliente = $request->cliente;

        $credito = new Creditos();
        $credito->cliente = $cliente;
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
        
        $this->CreditosSrv->CrearFichero($cliente);
        return response()->json(['message' => 'Credito creado correctamente']);
    }

    public function CuotasValue($modalidad)
    {
        $cuotas = [];

        if ($modalidad == 'Diaria') {
            $cuotas = [
                ['value' => 15],
                ['value' => 20],
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
        } elseif ($modalidad == 'Diaria-Articulo') {
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
                ['value' => 220],
                ['value' => 240]
            ];
        } elseif ($modalidad == 'Semanal-Articulo') {
            $cuotas = [
                ['value' => 4],
                ['value' => 8],
                ['value' => 12],
                ['value' => 16],
                ['value' => 20],
                ['value' => 24],
                ['value' => 28],
                ['value' => 32]
            ];
        }

        return response()->json($cuotas);
    }

    public function fechasCuotas($idCliente){
        $credito = Creditos::where('cliente', $idCliente)->where('active', 1)->first();
        $fechas = $this->CreditosSrv->DatasFichero($credito->inicio, $credito->cuotas, $credito->modalidad);
        return response()->json($fechas);
    }
}
