<?php

namespace App\Services;

use App\Models\AppSetup;
use App\Models\Creditos;
use App\Models\Ficheros;
use App\Models\Pagos;
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
        $fichero->valor_otorgado = $creditoActivo->credito;
        $fichero->valor_final = $creditoActivo->total_credito;
        $fichero->modalidad = $creditoActivo->modalidad;
        $fichero->lugar_cobro = $creditoActivo->lugar_cobro;
        $fichero->save();
    }

    public function DatasFichero($fechaInicial, $cantidadDias, $modalidad)
    {
        if ($modalidad == 'Diaria' || $modalidad == 'Diaria-Articulo') {
            // 0 - domingo, 1 - lunes, etc;
            $setup = AppSetup::where('active', 1)->first();


            // se guarda en una cadena separada por comas.
            $diasLibres = explode(',', $setup->diaslibres);
            $diasLibres = array_map('intval', $diasLibres);  // Convertir a enteros por si acaso


            // Configurar la fecha inicial
            $fecha = Carbon::createFromFormat('Y-m-d', $fechaInicial);

            $fechas = [];

            $fechas[] = $fecha->format('d/m');
            // Generar las fechas
            while (count($fechas) != $cantidadDias) {
                $fecha->addDay();

                // Si el día actual no está en los días libres, añadirlo al array
                if (!in_array($fecha->dayOfWeek, $diasLibres)) {
                    $fechas[] = $fecha->format('d/m');
                }
            }

            return $fechas;
        } elseif ($modalidad == 'Semanal' || $modalidad == 'Semanal-Articulo') {
            $fecha = Carbon::createFromFormat('Y-m-d', $fechaInicial);
            $diaSemana = $fecha->dayOfWeek; // Obtener el día de la semana de la fecha inicial (0 - domingo, 1 - lunes, etc.)

            $fechas = [];

            // Agregar la fecha inicial
            $fechas[] = $fecha->format('d/m');

            // Generar las fechas para los siguientes mismos días de la semana
            for ($i = 1; $i < $cantidadDias; $i++) {
                // Añadir 1 semana (7 días) para obtener el mismo día de la semana en la siguiente semana
                $fecha->addWeek();

                // Agregar la nueva fecha al array
                $fechas[] = $fecha->format('d/m');
            }
            
            return $fechas;
        }
    }

    public function verificarPagos($idCredito){
        $pagos = Pagos::where('idcredito', $idCredito)->get();
        $valor = 0;
        foreach ($pagos as $pago) {
            $valor += $pago->valor;
        }
        return $valor;
    }

    // public function ActualizarPagos(){
    //     $pagos = Pagos::where('active', 1)->get();
    //     $creditos = Creditos::where('active', 1)->get();

    //     foreach($pagos as $pago){
    //         foreach($creditos as $credito){
    //             if($credito->id == $pago->idcredito){
    //                 $valor = $credito->pagado + $pago->valor;
    //                 $credito->pagado = $valor;
    //                 $credito->pago_restante = ($credito->total_credito - $valor);
    //                 $credito->save();

    //                 $pago->active = 0;
    //                 $pago->save();
    //             }
    //         }
    //     }
    // }

    public function ActualizarPagos(){
        $pagos = Pagos::all();
        $creditos = Creditos::where('active', 1)->get();

        foreach($creditos as $credito){
            $valor = 0;

            foreach($pagos as $pago){
                if($credito->id == $pago->idcredito){
                    $valor += $pago->valor;
                    $credito->pagado = $valor;
                    $credito->pago_restante = ($credito->total_credito - $valor);
                    $credito->save();

                    $pago->active = 0;
                    $pago->save();

                    if($credito->pago_restante <= 0){
                        $credito->status = 'Pagado';
                        $credito->save();
                    }
                }
            }
        }
    }

    public function TransformIdEnName($array, $clientes){
        $clienteMap = [];
        foreach ($clientes as $cliente) {
            $clienteMap[$cliente->id] = $cliente->name; // Ajusta 'id' y 'nombre' según las columnas de tu tabla Clientes
        }

        // Iterar sobre los pagos para asignar el nombre del cliente en lugar del idcliente
        foreach ($array as $arr) {
            $arr->nombre_cliente = $clienteMap[$arr->cliente] ?? 'Cliente desconocido';
        }
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
