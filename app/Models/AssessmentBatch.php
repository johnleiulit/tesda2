<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'nc_program',
        'batch_name',
        'assessment_date',
        'start_time',
        'end_time',
        'venue',
        'assessor_name',
        'status',
        'schedule_notifications_sent_at',
        'max_applicants',
        'intensive_review_day1',
        'intensive_review_day1_start',
        'intensive_review_day1_end',
        'intensive_review_day2',
        'intensive_review_day2_start',
        'intensive_review_day2_end',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'intensive_review_day1' => 'date',
        'intensive_review_day1_start' => 'datetime:H:i',
        'intensive_review_day1_end' => 'datetime:H:i',
        'intensive_review_day2' => 'date',
        'intensive_review_day2_start' => 'datetime:H:i',
        'intensive_review_day2_end' => 'datetime:H:i',
        'schedule_notifications_sent_at' => 'datetime',
    ];

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    // Helper methods
    public function getAssignedCountAttribute()
    {
        return $this->applications()->count();
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_applicants - $this->assigned_count;
    }

    public function getIsFullAttribute()
    {
        return $this->assigned_count >= $this->max_applicants;
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
    public function hasScheduleNotificationsSent()
    {
        return !is_null($this->schedule_notifications_sent_at);
    }
}