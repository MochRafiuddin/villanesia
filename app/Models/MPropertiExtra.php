<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MPropertiExtra extends Model
{
    use HasFactory;
    use CreatedUpdatedBy;

    protected $table = "m_properti_extra";
    protected $primaryKey = 'id_properti_extra';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';    
}
