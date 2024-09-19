<?php

namespace App\Services;

use App\Models\AppSetup;
use App\Models\Clientes;
use App\Models\Creditos;
use App\Models\Ficheros;
use App\Models\Pagos;
use App\Models\Recorridos;
use App\Models\Resumecobrador;
use App\Models\ResumenMensual;
use App\Models\Resumevendedor;
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
        $dineroCancelado = $credito->pago_restante;

        $credito->dinero_cancelado = $dineroCancelado;
        $credito->active = 0;
        $credito->status = 'Renovado';
        $credito->save();

        return $dineroCancelado;
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

        if ($creditoActivo->status == 'Renovado') {
            $fichero = new Ficheros();
            $fichero->cliente = $idCliente;
            $fichero->inicio = $creditoActivo->inicio;
            $fichero->cuotas = $creditoActivo->cuotas;
            $fichero->cuotas_valor = $creditoActivo->cuotas_valor;
            $fichero->valor_otorgado = $creditoActivo->credito;
            $fichero->valor_final = $creditoActivo->total_credito;
            $fichero->modalidad = $creditoActivo->modalidad;
            $fichero->lugar_cobro = $creditoActivo->lugar_cobro;
            $fichero->status = $creditoActivo->status;
            $fichero->dinero_cancelado = $creditoActivo->dinero_cancelado;
            $fichero->dinero_arecibir = $creditoActivo->dinero_arecibir;
            $fichero->save();
        } else {
            $fichero = new Ficheros();
            $fichero->cliente = $idCliente;
            $fichero->inicio = $creditoActivo->inicio;
            $fichero->cuotas = $creditoActivo->cuotas;
            $fichero->cuotas_valor = $creditoActivo->cuotas_valor;
            $fichero->valor_otorgado = $creditoActivo->credito;
            $fichero->valor_final = $creditoActivo->total_credito;
            $fichero->modalidad = $creditoActivo->modalidad;
            $fichero->lugar_cobro = $creditoActivo->lugar_cobro;
            $fichero->status = $creditoActivo->status;
            $fichero->save();
        }
    }

    public function DatasFichero($fechaInicial, $cantidadDias, $modalidad)
    {
        if ($modalidad == 'Diaria' || $modalidad == 'Diaria-Articulo') {
            $setup = AppSetup::where('active', 1)->first();

            if (!$setup) {

                return [];
            }


            $diasLibres = json_decode($setup->diaslibres, true);

            if (!is_array($diasLibres)) {

                $diasLibres = [];
            }

            $fecha = Carbon::createFromFormat('Y-m-d', $fechaInicial);

            $fechas = [];
            $count = 0;

            while ($count < $cantidadDias) {
                if (!in_array($fecha->dayOfWeek, $diasLibres)) {
                    $fechas[] = $fecha->format('d/m');
                    $count++;
                }
                $fecha->addDay();
            }

            return $fechas;
        } elseif ($modalidad == 'Semanal' || $modalidad == 'Semanal-Articulo') {
            $fecha = Carbon::createFromFormat('Y-m-d', $fechaInicial);
            $diaSemana = $fecha->dayOfWeek;

            $fechas = [];

            $fechas[] = $fecha->format('d/m');

            for ($i = 1; $i < $cantidadDias; $i++) {

                $fecha->addWeek();

                // Agregar la nueva fecha al arra
                $fechas[] = $fecha->format('d/m');
            }

            return $fechas;
        }
    }

    public function verificarPagos($idCredito)
    {
        $pagos = Pagos::where('idcredito', $idCredito)->get();
        $valor = 0;
        foreach ($pagos as $pago) {
            $valor += $pago->valor;
        }
        return $valor;
    }

    public function ActualizarPagos()
    {
        $pagos = Pagos::all();
        $creditos = Creditos::where('active', 1)->get();

        foreach ($creditos as $credito) {
            $valor = 0;

            foreach ($pagos as $pago) {
                if ($credito->id == $pago->idcredito) {
                    $valor += $pago->valor;
                    $pago_restante = ($credito->total_credito - $valor);
                    $cuotas_restantes = floor($pago_restante / $credito->cuotas_valor);

                    $credito->cuotas_restantes = $cuotas_restantes;
                    $credito->pagado = $valor;
                    $credito->pago_restante = $pago_restante;
                    $credito->save();

                    $pago->active = 0;
                    $pago->save();

                    if ($credito->pago_restante <= 0) {
                        $credito->status = 'Pagado';
                        $credito->save();
                    }
                }
            }
        }
    }

    public function TransformIdEnName($array, $clientes)
    {
        $clienteMap = [];
        foreach ($clientes as $cliente) {
            $clienteMap[$cliente->id] = $cliente->name; // Ajusta 'id' y 'nombre' según las columnas de tu tabla Clientes
        }

        // Iterar sobre los pagos para asignar el nombre del cliente en lugar del idcliente
        foreach ($array as $arr) {
            $arr->nombre_cliente = $clienteMap[$arr->cliente] ?? 'Cliente desconocido';
        }
    }

    public function NombreCliente($idCliente)
    {
        $cliente = Clientes::where('id', $idCliente)->first();
        $nombre = $cliente->name;
        return $nombre;
    }


    public function CobroAddress($idCredito)
    {
        $credito = Creditos::where('id', $idCredito)->first();
        $cliente = Clientes::where('id', $credito->cliente)->first();

        if ($credito->lugar_cobro == 'Domicilio') {
            $direccion = $cliente->address;
        } else {
            $direccion = $cliente->comercio_address;
        }

        return $direccion;
    }


    public function GuardarRecorrido($data, $recorridoId, $fechaHoy)
    {
        if (!empty($data['ids'])) {
            $pagos = [];
            foreach ($data['ids'] as $elemento) {
                array_push($pagos, 'No pago');
            }

            $verificador = Recorridos::whereDate('created_at', $fechaHoy)->where('recorrido', $recorridoId)->first();

            if (empty($verificador)) {
                $recorrido = new Recorridos();
                $recorrido->elementos = json_encode($data['elementos']);
                $recorrido->ids = json_encode($data['ids']);
                $recorrido->nombres = json_encode($data['nombres']);
                $recorrido->direcciones = json_encode($data['direcciones']);
                $recorrido->totales_creditos = json_encode($data['totales_creditos']);
                $recorrido->recorrido = $recorridoId;
                $recorrido->pagos = json_encode($pagos);
                $recorrido->save();
            }
        }
    }

    public function PagoRecorridoHoy($idCliente, $valor, $fecha)
    {
        $cliente = Clientes::where('id', $idCliente)->first();

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $recorrido = $cliente->recorrido;

        $fecha = Carbon::parse($fecha);


        $FichaRecorrido = Recorridos::where('recorrido', $recorrido)
            ->whereDate('created_at', $fecha)
            ->first();

        if (!$FichaRecorrido) {
            return;
        }

        $idsRecorrido = json_decode($FichaRecorrido->ids);
        $pagos = json_decode($FichaRecorrido->pagos);
        $indexCliente = array_search($idCliente, $idsRecorrido);

        if ($indexCliente === false) {
            return;
        }

        $pagos[$indexCliente] = $valor;

        $FichaRecorrido->pagos = json_encode($pagos);
        $FichaRecorrido->save();
    }

    public function NewResumenMensual()
    {
        // creditos Otorgados
        // creditos Renovados
        // dinero cancelado por renovaciones
        // creditos Refinanciados
        // pagos totales
        // data mes correspondiente al resumen

        $now = Carbon::now();

        // Definir el inicio y el fin del mes actual
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();


        // Obtener los créditos activos creados en el mes actual
        $creditos = Creditos::where('active', 1)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();


        $pendiente = [];
        $renovados = [];
        $refinanciados = [];

        foreach ($creditos as $credito) {
            if ($credito->status == 'Renovado') {
                array_push($renovados, $credito);
            } elseif ($credito->status == 'Refinanciado') {
                array_push($refinanciados, $credito);
            } else {
                array_push($pendiente, $credito);
            }
        }

        $creditos_otorgados = 0;
        $creditos_renovados = 0;
        $dinero_cancelado = 0;
        $creditos_refinanciados = 0;

        foreach ($pendiente as $cr) {
            $creditos_otorgados += $cr->credito;
        }

        foreach ($refinanciados as $cr) {
            $creditos_refinanciados += $cr->credito;
        }

        foreach ($renovados as $cr) {
            $creditos_renovados += $cr->credito;
            $dinero_cancelado += $cr->dinero_cancelado;
        }

        $pagos_totales = Pagos::whereBetween('pago_fecha', [$startOfMonth, $endOfMonth])->sum('valor');

        $verificador = ResumenMensual::whereMonth('data', $now->month)
            ->whereYear('data', $now->year)
            ->where('active', 1)
            ->first();


        if ($verificador) {
            // Actualizar el resumen existente
            $verificador->creditos_otorgados = $creditos_otorgados;
            $verificador->creditos_renovados = $creditos_renovados;
            $verificador->creditos_refinanciados = $creditos_refinanciados;
            $verificador->dinero_renovaciones = $dinero_cancelado;
            $verificador->pagos_totales = $pagos_totales;
            $verificador->save();
        } else {
            // Crear un nuevo resumen mensual
            $ResumenMensual = new ResumenMensual();
            $ResumenMensual->creditos_otorgados = $creditos_otorgados;
            $ResumenMensual->creditos_renovados = $creditos_renovados;
            $ResumenMensual->creditos_refinanciados = $creditos_refinanciados;
            $ResumenMensual->dinero_renovaciones = $dinero_cancelado;
            $ResumenMensual->pagos_totales = $pagos_totales;
            $ResumenMensual->data = $now->format('Y-m-d'); // Aquí puedes guardar la fecha actual en formato yyyy-mm-dd
            $ResumenMensual->save();
        }
    }

    public function CreateResumenSemanalVendedor($idVendedor, $idCreditos, $valor)
    {
        $verificador = Resumevendedor::where('vendedor', $idVendedor)->where('active', 1)->first();
        $fecha = Carbon::now();

        if (empty($verificador)) {
            $resume = new Resumevendedor();
            $resume->vendedor = $idVendedor;
            $resume->creditos = json_encode($idCreditos);
            $resume->valor = $valor;
            $resume->data = $fecha;
            $resume->active = 1;
            $resume->save();
        } else {
            if ($fecha->isFriday()) {
                $verificador->creditos = json_encode($idCreditos);
                $verificador->valor = $valor;
                $verificador->data = $fecha;
                $verificador->active = 1;
                $verificador->save();
            } elseif ($fecha->isSaturday()) {
                $verificador->creditos = json_encode($idCreditos);
                $verificador->valor = $valor;
                $verificador->data = $fecha;
                $verificador->active = 0;
                $verificador->save();
            }
        }
    }

    public function CreateResumenSemanalCobrador($idRecorrido, $idPagos, $valor)
    {

        $verificador = Resumecobrador::where('cobrador', $idRecorrido)->where('active', 1)->first();
        $fecha = Carbon::now();

        if (empty($verificador)) {
            $resume = new Resumecobrador();
            $resume->cobrador = $idRecorrido;
            $resume->pagos = json_encode($idPagos);
            $resume->valor = $valor;
            $resume->data = $fecha;
            $resume->active = 1;
            $resume->save();
        } else {
            if ($fecha->isFriday()) {
                $verificador->pagos = json_encode($idPagos);
                $verificador->valor = $valor;
                $verificador->data = $fecha;
                $verificador->active = 1;
                $verificador->save();
            } elseif ($fecha->isSaturday()) {
                $verificador->pagos = json_encode($idPagos);
                $verificador->valor = $valor;
                $verificador->data = $fecha;
                $verificador->active = 0;
                $verificador->save();
            }
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
