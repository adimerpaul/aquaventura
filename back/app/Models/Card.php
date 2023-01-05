<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
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
        'days',
        'number',
        'photo',
    ];
}
