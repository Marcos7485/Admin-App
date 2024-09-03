<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ficheros extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'ficheros';

    protected $fillable = [
        'cliente',
        'inicio',
        'cuotas',
        'cuotas_valor',
        'active'
    ];

}
