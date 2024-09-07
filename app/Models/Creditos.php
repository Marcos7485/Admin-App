<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Creditos extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'creditos';

    protected $fillable = [
        'cliente',
        'credito',
        'interes',
        'total_credito',
        'cuotas',
        'cuotas_restantes',
        'modalidad',
        'cuotas_valor',
        'lugar_cobro',
        'pagado',
        'pago_restante',
        'inicio',
        'finalizacion',
        'status',
        'dinero_cancelado',
        'dinero_arecibir',
        'status',
        'active'
    ];

}
