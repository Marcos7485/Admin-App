<?php

namespace App\Services;

use App\Models\Creditos;
use App\Models\Ficheros;

class CreditosSrv
{
    public function calcularValorCredito($interes, $credito){
        $interesPorcentaje = ($interes / 100);
        $interesValor = number_format(((float) $credito * $interesPorcentaje), 2, '.', '');
        $totalCredito = number_format(((float) $credito + $interesValor), 2, '.', '');

        return $totalCredito;
    }

    public function calcularValorCuotas($total, $cuotas){
        $cuotas_valor = number_format($total / (float)$cuotas, 2, '.', '');
        return $cuotas_valor;
    }

    public function Refinanciar($idCliente){
        $credito = Creditos::where('cliente', $idCliente)->where('active', 1)->first();

        $credito->active = 0;
        $credito->status = 'Refinanciado';
        $credito->save();
    }

    public function DesactivarFichero($idCliente){
        $creditoActivo = Ficheros::where('cliente', $idCliente)->where('active', 1)->first();
        $creditoActivo->active = 0;
        $creditoActivo->save();
    }

    public function CrearFichero($idCliente){

        $creditoActivo = Creditos::where('cliente', $idCliente)->where('active', 1)->first();

        $fichero = new Ficheros();
        $fichero->cliente = $idCliente;
        $fichero->inicio = $creditoActivo->inicio;
        $fichero->cuotas = $creditoActivo->cuotas;
        $fichero->cuotas_valor = $creditoActivo->cuotas_valor;
        $fichero->save();

    }
}
