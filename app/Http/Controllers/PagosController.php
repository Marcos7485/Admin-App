<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditPagoRequest;
use App\Http\Requests\NewPagoRequest;
use App\Models\Clientes;
use App\Models\Creditos;
use App\Models\Pagos;
use App\Services\CreditosSrv;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PagosController extends Controller
{
    protected $CreditosSrv;

    public function __construct(CreditosSrv $CreditosSrv)
    {
        $this->CreditosSrv = $CreditosSrv;
    }

    public function PagoForm()
    {
        return Inertia::location(route('dashboard'));
    }

    public function NewPago(NewPagoRequest $request)
    {
        $credito = Creditos::where('cliente', $request->cliente)->where('active', 1)->first();

        $ultimoPago = Pagos::where('cliente', $request->cliente)
            ->orderBy('id', 'desc')
            ->first();

        $pago = new Pagos();

        if (empty($ultimoPago)) {
            $pago_numero = 1;
        } else {
            $pago_numero = $ultimoPago->pago_numero + 1;
        }

        $pago->idcredito = $credito->id;
        $pago->cliente = $request->cliente;
        $pago->valor = $request->valor;
        $pago->pago_numero = $pago_numero;
        $pago->pago_fecha = Carbon::parse($request->pago_fecha);
        $pago->active = 1;
        $pago->save();

        $this->CreditosSrv->PagoRecorridoHoy($request->cliente, $request->valor, $request->pago_fecha);
        $this->CreditosSrv->ActualizarPagos();
        return response()->json(['message' => 'Pago registrado correctamente']);
    }

    public function PagoEdit(EditPagoRequest $request)
    {
        $pago = Pagos::where('id', $request->id)->first();

        $pago->cliente = $request->cliente;
        $pago->valor = $request->valor;
        $pago->pago_fecha = Carbon::parse($request->pago_fecha);
        $pago->save();
        
        $this->CreditosSrv->PagoRecorridoHoy($request->cliente, $request->valor, $request->pago_fecha);
        $this->CreditosSrv->ActualizarPagos();
        return response()->json(['message' => 'Credito editado correctamente']);
    }

    public function getDatosPagos()
    {
        $pagos = Pagos::all();
        $clientes = Clientes::all();
        $this->CreditosSrv->TransformIdEnName($pagos, $clientes);
        
        return response()->json($pagos);
    }

    public function PagosInfo($id)
    {
        $pago = Pagos::where('id', $id)->first();
        return response()->json($pago);
    }
}
