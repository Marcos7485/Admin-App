<?php

namespace App\Http\Controllers;

use App\Models\Creditos;
use App\Models\Pagos;
use App\Models\ResumenMensual;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResumenMensualController extends Controller
{

    public function getResumeMensual($data)
    {
        // 09-2024
        $date = Carbon::createFromFormat('m-Y', $data);
        $yearMonth = $date->format('Y-m');

        $resumen = ResumenMensual::where('data', 'like', $yearMonth . '%')
            ->where('active', 1)
            ->first();

        return response()->json($resumen);
    }

    public function ResumenDatas()
    {
        $resumenes = ResumenMensual::all();
        $datas = [];

        foreach ($resumenes as $resumen) {
            $formattedDate = Carbon::parse($resumen->data)->format('m-Y');
            array_push($datas, $formattedDate);
        }
        return response()->json($datas);
    }
}
