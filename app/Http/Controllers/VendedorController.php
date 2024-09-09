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

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();


        $clientes = Clientes::where('vendedor', $idVendedor)->where('active', 1)->get();
        $creditos = [];

        foreach ($clientes as $cliente) {
            $credito = Creditos::where('cliente', $cliente->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('active', 1)
                ->first();
                
            if (!empty($credito)) {
                array_push($creditos, $credito);
            }
        }

        $this->CreditosSrv->TransformIdEnName($creditos, $clientes);

        return response()->json($creditos);
    }
}
