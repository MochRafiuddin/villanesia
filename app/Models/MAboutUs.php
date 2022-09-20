<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MAboutUs extends Model
{
    use HasFactory;
    use CreatedUpdatedBy;

    protected $table = "page_about_us";
    protected $primaryKey = 'id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';    
}
