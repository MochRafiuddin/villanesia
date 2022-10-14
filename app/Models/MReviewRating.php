<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MReviewRating extends Model
{
    use HasFactory;
    use CreatedUpdatedBy;

    protected $table = "h_review_rating";
    protected $primaryKey = 'id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
}
