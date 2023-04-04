<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MSplashSlide extends Model
{
    use HasFactory;
    use CreatedUpdatedBy;

    protected $table = "m_splash_slide";
    protected $primaryKey = 'id_ss';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';  
    
    protected $fillable = [
        'deleted',
    ];
    public static function withDeleted()
    {
        return self::where('deleted',1);
    }
    public static function updateDeleted($id)
    {
        return self::find($id)->update(['deleted'=>0]);
    }
}
