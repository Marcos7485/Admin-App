<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resumevendedor extends Model
{
    use HasFactory;
    
    use SoftDeletes;

    protected $table = 'resumevendedor';

    protected $fillable = [
        'vendedor',
        'creditos',
        'valor',
        'data',
        'active'
    ];

}
