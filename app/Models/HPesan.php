<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HPesan extends Model
{
    use HasFactory;    
    
    protected $table = "h_pesan";
    protected $primaryKey = 'id_pesan';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
}
