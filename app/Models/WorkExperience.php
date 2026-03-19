<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'company_name',
        'position',
        'date_from',
        'date_to',
        'monthly_salary',
        'appointment_status',
        'years_experience',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
