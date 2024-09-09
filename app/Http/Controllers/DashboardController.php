<?php

namespace App\Http\Controllers;

use App\Services\CreditosSrv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $CreditosSrv;

    public function __construct(CreditosSrv $CreditosSrv)
    {
        $this->CreditosSrv = $CreditosSrv;
    }


    public function dashboard(){

        $this->CreditosSrv->NewResumenMensual();
        $user = Auth::user();
        return Inertia::render('dashboard/Main', [
            'user' => $user,
        ]);
    }
}
