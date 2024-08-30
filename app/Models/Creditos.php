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
        'interes_mora',
        'total_credito',
        'cuotas',
        'modalidad',
        'cuotas_valor',
        'lugar_cobro',
        'pagado',
        'inicio',
        'finalizacion',
        'mora',
        'status',
        'active'
    ];

}
