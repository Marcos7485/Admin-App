<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Creditos;
use App\Services\CreditosSrv;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VendedorController extends Controller
{
    protected $CreditosSrv;

    public function __construct(CreditosSrv $CreditosSrv)
    {
        $this->CreditosSrv = $CreditosSrv;
    }

    public function getResumeVendedor($idVendedor)
    {

        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();


        $clientes = Clientes::where('vendedor', $idVendedor)->where('active', 1)->get();
        $creditos = [];
        $idCreditos = [];
        $valor = 0;

        foreach ($clientes as $cliente) {
            $credito = Creditos::where('cliente', $cliente->id)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->where('active', 1)
                ->first();

            if (!empty($credito)) {
                array_push($creditos, $credito);
                array_push($idCreditos, $credito->id);
                $valor += $credito->credito;
            }
        }

        $this->CreditosSrv->TransformIdEnName($creditos, $clientes);
        $this->CreditosSrv->CreateResumenSemanalVendedor($idVendedor, $idCreditos, $valor);

        return response()->json($creditos);
    }
}
