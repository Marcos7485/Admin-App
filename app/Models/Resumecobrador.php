<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resumecobrador extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'resumecobrador';

    protected $fillable = [
        'cobrador',
        'pagos',
        'valor',
        'data',
        'active'
    ];
}
