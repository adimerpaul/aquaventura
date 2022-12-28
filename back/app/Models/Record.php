<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;
    protected $fillable = [
        'ci',
        'code',
        'dateIni',
        'dateEnd',
        'codeTarget',
        'name',
        'birthday',
        'phone',
        'schedule',
        'amount',
        'type',
        'observation',
        'date',
        'time',
    ];
}
