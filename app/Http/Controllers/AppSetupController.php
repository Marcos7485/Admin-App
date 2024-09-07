<?php

namespace App\Http\Controllers;

use App\Models\AppSetup;
use Illuminate\Http\Request;

class AppSetupController extends Controller
{
    public function Recorridos(){
        $setup = AppSetup::where('active', 1)->first();
        $recorridos = [];
    
        for ($i = 0; $i < $setup->recorridos; $i++) {
            $recorridos[] = [
                'id' => $i + 1,
                'name' => 'Recorrido ' . ($i + 1)
            ];
        }
    
        return response()->json($recorridos);
    }

    public function Vendedores(){
        $setup = AppSetup::where('active', 1)->first();
        $vendedores = [];
        for ($i = 0; $i < $setup->vendedores; $i++) {
            $vendedores[] = [
                'id' => $i + 1,
                'name' => 'Vendedor ' . ($i + 1)
            ];
        }
        return response()->json($vendedores);
    }

    public function Cobradores(){
        $setup = AppSetup::where('active', 1)->first();
        $cobradores = [];
        for ($i = 0; $i < $setup->cobradores; $i++) {
            $cobradores[] = [
                'id' => $i + 1,
                'name' => 'Cobrador ' . ($i + 1)
            ];
        }
        return response()->json($cobradores);
    }
}
