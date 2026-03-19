<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentCocResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_result_id',
        'application_id',
        'coc_code',
        'coc_title',
        'result',
        'remarks',
    ];

    public function assessmentResult(): BelongsTo
    {
        return $this->belongsTo(AssessmentResult::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function isCompetent(): bool
    {
        return $this->result === 'competent';
    }

    public function isNotYetCompetent(): bool
    {
        return $this->result === 'not_yet_competent';
    }
}
