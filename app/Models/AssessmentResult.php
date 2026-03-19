<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'assessment_batch_id',
        'result',
        'score',
        'remarks',
        'assessed_by',
        'assessed_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'assessed_at' => 'datetime',
    ];

    // Result constants
    const RESULT_PASS = 'Competent';
    const RESULT_FAIL = 'Not Yet Competent';
    const RESULT_INCOMPLETE = 'incomplete';

    // Relationships
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function assessmentBatch(): BelongsTo
    {
        return $this->belongsTo(AssessmentBatch::class);
    }

    // Helper methods
    public function isPassed()
    {
        return $this->result === self::RESULT_PASS;
    }

    public function isFailed()
    {
        return $this->result === self::RESULT_FAIL;
    }
    public function cocResults()
    {
        return $this->hasMany(AssessmentCocResult::class);
    }

    public function allCocsCompetent(): bool
    {
        return $this->cocResults()->where('result', 'not_yet_competent')->count() === 0;
    }

    public function getNycCocs()
    {
        return $this->cocResults()->where('result', 'not_yet_competent')->get();
    }

    public function getCompetentCocs()
    {
        return $this->cocResults()->where('result', 'competent')->get();
    }
}