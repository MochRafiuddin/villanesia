<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MPropertiKamarTidur extends Model
{ 
    use HasFactory;
    use CreatedUpdatedBy;

    protected $table = "m_properti_kamar_tidur";
    protected $primaryKey = 'id_properti_kamar_tidur';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';   
}
