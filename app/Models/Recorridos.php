<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recorridos extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'recorridos';

    protected $casts = [
        'elementos' => 'array',
        'ids' => 'array',
        'nombres' => 'array',
        'direcciones' => 'array',
        'totales_creditos' => 'array',
    ];

    protected $fillable = [
        'elementos',
        'ids',
        'nombres',
        'direcciones',
        'totales_creditos',
        'active'
    ];

}
