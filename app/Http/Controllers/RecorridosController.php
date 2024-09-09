<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Creditos;
use App\Models\Pagos;
use App\Models\Recorridos;
use App\Services\CreditosSrv;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecorridosController extends Controller
{
    protected $CreditosSrv;

    public function __construct(CreditosSrv $CreditosSrv)
    {
        $this->CreditosSrv = $CreditosSrv;
    }

    public function RecorridoHoy($recorrido)
    {

        $clientes = Clientes::where('recorrido', $recorrido)->where('active', 1)->orderBy('id', 'asc')->get();
        $creditos = [];
        $ids = [];
        $direcciones = [];
        $totales = [];
        $nombres = [];

        foreach ($clientes as $cliente) {
            $credito = Creditos::where('cliente', $cliente->id)->where('active', 1)->first();

            if (!empty($credito)) {
                if ($credito->status != 'Pagado') {

                    if ($credito->modalidad == 'Semanal' || $credito->modalidad == 'Semanal-Articulo') {

                        $inicio = Carbon::parse($credito->inicio);
                        $diaSemana = $inicio->dayOfWeek();
                        $hoy = Carbon::now()->dayOfWeek();

                        if ($diaSemana == $hoy) {
                            array_push($creditos, $credito);
                            array_push($ids, $credito->cliente);
                            array_push($totales, $credito->pago_restante);
                        }
                    } else {
                        array_push($creditos, $credito);
                        array_push($ids, $credito->cliente);
                        array_push($totales, $credito->pago_restante);
                    }
                }
            }
        }



        foreach ($creditos as $credito) {

            $nombre = $this->CreditosSrv->NombreCliente($credito->cliente);
            $lugar_cobro = $this->CreditosSrv->CobroAddress($credito->id);
            array_push($direcciones, $lugar_cobro);
            array_push($nombres, $nombre);
        }

        $elementos = count($ids);

        $resultados = [
            'elementos' => $elementos,
            'ids' => $ids,
            'nombres' => $nombres,
            'direcciones' => $direcciones,
            'totales_creditos' => $totales,
        ];

        $fechaHoy = Carbon::now()->format('Y-m-d');

        $this->CreditosSrv->GuardarRecorrido($resultados, $recorrido, $fechaHoy);

        return response()->json($resultados);
    }

    public function getDatosRecorridos()
    {
        $recorridos = Recorridos::all();

        $datosDecodificados = [];

        foreach ($recorridos as $recorrido) {
            $datosDecodificados[] = [
                'id' => $recorrido->id,
                'elementos' => json_decode($recorrido->elementos, true),
                'ids' => json_decode($recorrido->ids, true),
                'nombres' => json_decode($recorrido->nombres, true),
                'direcciones' => json_decode($recorrido->direcciones, true),
                'totales_creditos' => json_decode($recorrido->totales_creditos, true),
                'recorrido' => $recorrido->recorrido,
                'created_at' => $recorrido->created_at,
                'updated_at' => $recorrido->updated_at
            ];
        }

        return response()->json($datosDecodificados);
    }

    public function RecorridoInfo($id)
    {
        $recorrido = Recorridos::where('id', $id)->first();

        $datosDecodificados = [
            'id' => $recorrido->id,
            'elementos' => json_decode($recorrido->elementos, true),
            'ids' => json_decode($recorrido->ids, true),
            'nombres' => json_decode($recorrido->nombres, true),
            'direcciones' => json_decode($recorrido->direcciones, true),
            'totales_creditos' => json_decode($recorrido->totales_creditos, true),
            'pago' => json_decode($recorrido->pagos),
            'created_at' => $recorrido->created_at,
            'updated_at' => $recorrido->updated_at
        ];

        return response()->json($datosDecodificados);
    }

    public function getResumeCobrador($idRecorrido)
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $clientes = Clientes::where('recorrido', $idRecorrido)
            ->where('active', 1)
            ->get();

        $pagos_totales = [];

        foreach ($clientes as $cliente) {
            $pagos = Pagos::where('cliente', $cliente->id)
                ->whereBetween('pago_fecha', [$startOfMonth, $endOfMonth])
                ->get();

            if (!$pagos->isEmpty()) {
                foreach ($pagos as $pago) {
                    array_push($pagos_totales, $pago);
                }
            }
        }

        $this->CreditosSrv->TransformIdEnName($pagos_totales, $clientes);

        return response()->json($pagos_totales);
    }

}
