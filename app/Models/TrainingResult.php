<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'training_batch_id',
        'result',
        'attendance_percentage',
        'remarks',
        'evaluated_by',
        'completed_at',
    ];

    protected $casts = [
        'attendance_percentage' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // Result constants
    const RESULT_ONGOING = 'ongoing';
    const RESULT_COMPLETED = 'completed';
    const RESULT_FAILED = 'failed';

    // Relationships
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function trainingBatch(): BelongsTo
    {
        return $this->belongsTo(TrainingBatch::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->result === self::RESULT_COMPLETED;
    }

    public function isFailed()
    {
        return $this->result === self::RESULT_FAILED;
    }

    public function isOngoing()
    {
        return $this->result === self::RESULT_ONGOING;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('result', self::RESULT_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('result', self::RESULT_FAILED);
    }

    public function scopeOngoing($query)
    {
        return $query->where('result', self::RESULT_ONGOING);
    }
}
