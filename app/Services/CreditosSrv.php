<?php

namespace App\Services;

use App\Models\AppSetup;
use App\Models\Creditos;
use App\Models\Ficheros;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CreditosSrv
{
    public function calcularValorCredito($interes, $credito)
    {
        $interesPorcentaje = ($interes / 100);
        $interesValor = number_format(((float) $credito * $interesPorcentaje), 2, '.', '');
        $totalCredito = number_format(((float) $credito + $interesValor), 2, '.', '');

        return $totalCredito;
    }

    public function calcularValorCuotas($total, $cuotas)
    {
        $cuotas_valor = number_format($total / (float)$cuotas, 2, '.', '');
        return $cuotas_valor;
    }

    public function Refinanciar($idCliente)
    {
        $credito = Creditos::where('cliente', $idCliente)->where('active', 1)->first();

        $credito->active = 0;
        $credito->status = 'Refinanciado';
        $credito->save();
    }

    public function Renovar($idCliente)
    {
        $credito = Creditos::where('cliente', $idCliente)->where('active', 1)->first();

        $credito->active = 0;
        $credito->status = 'Renovado';
        $credito->save();
    }

    public function DesactivarFichero($idCliente)
    {
        $creditoActivo = Ficheros::where('cliente', $idCliente)->where('active', 1)->first();
        $creditoActivo->active = 0;
        $creditoActivo->save();
    }

    public function CrearFichero($idCliente)
    {

        $creditoActivo = Creditos::where('cliente', $idCliente)->where('active', 1)->first();

        $fichero = new Ficheros();
        $fichero->cliente = $idCliente;
        $fichero->inicio = $creditoActivo->inicio;
        $fichero->cuotas = $creditoActivo->cuotas;
        $fichero->cuotas_valor = $creditoActivo->cuotas_valor;
        $fichero->save();
    }

    public function DatasFichero($fechaInicial, $cantidadDias)
    {
        // 0 - domingo, 1 - lunes, etc;
        $setup = AppSetup::where('active', 1)->first();

        // se guarda en una cadena separada por comas.
        $diasLibres = explode(',', $setup->diaslibres);
        $diasLibres = array_map('intval', $diasLibres);  // Convertir a enteros por si acaso

        // Configurar la fecha inicial
        $fecha = Carbon::createFromFormat('d/m/y', $fechaInicial);
        $fechas = [];

        // Generar las fechas
        while (count($fechas) < $cantidadDias) {
            $fecha->addDay();

            // Si el día actual no está en los días libres, añadirlo al array
            if (!in_array($fecha->dayOfWeek, $diasLibres)) {
                $fechas[] = $fecha->format('d/m');
            }
        }

        return $fechas;
    }

    public function generarQR(Request $request)
    {
        // Obtener la dirección desde la solicitud (puede venir de un formulario o una URL)
        $direccion = $request->input('direccion', '1600 Amphitheatre Parkway, Mountain View, CA');

        // Generar la URL de Google Maps con la dirección
        $direccionCodificada = urlencode($direccion);
        $urlGoogleMaps = "https://www.google.com/maps/search/?api=1&query={$direccionCodificada}";

        // Generar el código QR con la URL de Google Maps
        $qrCode = QrCode::size(300)->generate($urlGoogleMaps);

        // Retornar el código QR directamente en la vista
        return view('qrCodeView', compact('qrCode'));
    }
}
