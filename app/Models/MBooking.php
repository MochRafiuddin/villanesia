<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBooking extends Model
{
    use HasFactory;
    use CreatedUpdatedBy;

    protected $table = "t_booking";
    protected $primaryKey = 'id_booking';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    public static function withDeleted()
    {
        return self::where('deleted',1);
    }
    
}
