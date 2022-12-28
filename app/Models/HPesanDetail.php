<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HPesanDetail extends Model
{    
    use HasFactory;    
    
    protected $table = "h_pesan_detail";
    protected $primaryKey = 'id_pesan_detail';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
}
