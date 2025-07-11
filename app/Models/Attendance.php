<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'npk',
        'tanggal',
        'subdivisi',
        'jam_pagi',
        'jam_siang',
        'jam_malam',
        'status'
    ]; 
}
