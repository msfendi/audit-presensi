<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'AUDIT';
    protected $fillable = [
        'NPK',
        'TANGGAL',
        'SUBDIVISI',
        'JAM_PAGI',
        'JAM_SIANG',
        'JAM_MALAM',
        'STATUS'
    ];
}
