<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'nc_program',
        'training_batch_id',
        'schedule_name',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'days',
        'max_students',
        'venue',
        'instructor',
        'description',
        'status',
        'schedule_notifications_sent_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'schedule_notifications_sent_at' => 'datetime',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function trainingBatch(): BelongsTo
    {
        return $this->belongsTo(TrainingBatch::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'training_schedule_id');
    }

    // Helper methods
    public function getEnrolledCountAttribute()
    {
        return $this->applications()
            ->where('application_type', 'TWSP')
            ->count();
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_students - $this->enrolled_count;
    }

    public function getIsFullAttribute()
    {
        return $this->enrolled_count >= $this->max_students;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
