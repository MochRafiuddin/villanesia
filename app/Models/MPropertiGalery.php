<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MPropertiGalery extends Model
{
    use HasFactory;
    use CreatedUpdatedBy;

    protected $table = "m_properti_galery";
    protected $primaryKey = 'id_properti_galery';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';    
}
