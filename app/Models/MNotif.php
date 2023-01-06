<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;

class MNotif extends Model
{
    use HasFactory;
    use CreatedUpdatedBy;

    protected $table = "notif";
    protected $primaryKey = 'id_notif';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
}
