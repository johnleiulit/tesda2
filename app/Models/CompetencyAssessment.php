<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'title',
        'qualification_level',
        'industry_sector',
        'certificate_number',
        'date_of_issuance',
        'expiration_date',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
