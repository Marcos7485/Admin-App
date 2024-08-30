<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventPostAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica si el método de la solicitud es POST
        if ($request->isMethod('post')) {

            return redirect()->route('login'); // Cambia 'index' por el nombre de tu ruta de índice
        }

        return $next($request);
    }
}
