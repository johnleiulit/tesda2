<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicensureExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'title',
        'year_taken',
        'exam_venue',
        'rating',
        'remarks',
        'expiry_date',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
