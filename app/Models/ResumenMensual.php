<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResumenMensual extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'resumenmensual';

    protected $fillable = [
        'creditos_otorgados',
        'creditos_renovados',
        'creditos_refinanciados',
        'dinero_renovaciones',
        'pagos_totales',
        'month',
        'active'
    ];

}
