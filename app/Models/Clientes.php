<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clientes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'name',
        'dni',
        'phone',
        'address',
        'localidad',
        'comercio_address',
        'comercio_localidad',
        'comercio_tipo',
        'status',
        'active',
        'created_by'
    ];
}
