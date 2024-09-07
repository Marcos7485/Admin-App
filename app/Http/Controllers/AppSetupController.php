<?php

namespace App\Http\Controllers;

use App\Models\AppSetup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AppSetupController extends Controller
{
    public function Recorridos()
    {
        $setup = AppSetup::where('active', 1)->first();
        if (!$setup) {
            return response()->json(['error' => 'No se encontró configuración activa'], 404);
        }
        $recorridos = [];

        for ($i = 0; $i < $setup->recorridos; $i++) {
            $recorridos[] = [
                'id' => $i + 1,
                'name' => 'Recorrido ' . ($i + 1)
            ];
        }

        return response()->json($recorridos);
    }

    public function Vendedores()
    {
        $setup = AppSetup::where('active', 1)->first();
        if (!$setup) {
            return response()->json(['error' => 'No se encontró configuración activa'], 404);
        }
        $vendedores = [];
        for ($i = 0; $i < $setup->vendedores; $i++) {
            $vendedores[] = [
                'id' => $i + 1,
                'name' => 'Vendedor ' . ($i + 1)
            ];
        }
        return response()->json($vendedores);
    }

    public function Cobradores()
    {
        $setup = AppSetup::where('active', 1)->first();
        if (!$setup) {
            return response()->json(['error' => 'No se encontró configuración activa'], 404);
        }
        $cobradores = [];
        for ($i = 0; $i < $setup->cobradores; $i++) {
            $cobradores[] = [
                'id' => $i + 1,
                'name' => 'Cobrador ' . ($i + 1)
            ];
        }
        return response()->json($cobradores);
    }

    public function UpdatePassword(Request $request)
    {

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'oldPassword' => 'required',
            'newPassword' => 'required',
        ]);

        // Obtener el usuario especificado
        $user = User::find($request->user_id);

        // Verificar si el usuario existe
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Verificar si la contraseña actual es correcta
        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json(['error' => 'La contraseña actual es incorrecta'], 400);
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response()->json(['success' => 'Contraseña cambiada correctamente']);
    }

    public function UpdateConfig(Request $request)
    {
        // Validar los datos
        $validatedData = $request->validate([
            'daysOfWeek' => 'required|array|min:1',        
            'daysOfWeek.*' => 'integer|between:0,6',       
            'recorridos' => 'required|integer|min:0',    
            'vendedores' => 'required|integer|min:0',      
            'cobradores' => 'required|integer|min:0',     
        ]);

        $setup = AppSetup::where('active', 1)->first();

        if ($setup) {
            $setup->diaslibres = json_encode($validatedData['daysOfWeek']);
            $setup->recorridos = $validatedData['recorridos'];
            $setup->vendedores = $validatedData['vendedores'];
            $setup->cobradores = $validatedData['cobradores'];
            $setup->save();


            return response()->json(['message' => 'Configuración actualizada correctamente.']);
        }
        
    }
}
