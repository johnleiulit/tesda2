<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwspDocument extends Model
{
    protected $fillable = [
        'application_id',
        'psa_birth_certificate',
        'psa_marriage_contract',
        'high_school_document',
        'id_pictures_1x1',
        'id_pictures_passport',
        'government_school_id',
        'certificate_of_indigency',
    ];

    protected $casts = [
        'id_pictures_1x1' => 'array',
        'id_pictures_passport' => 'array',
        'government_school_id' => 'array',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
