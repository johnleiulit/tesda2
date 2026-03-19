<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrainingBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'nc_program',
        'batch_number',
        'max_students',
        'status',
        'remarks',
    ];

    // Status constants
    const STATUS_ENROLLING = 'enrolling';
    const STATUS_FULL = 'full';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'training_batch_id');
    }

    public function trainingSchedule(): HasOne
    {
        return $this->hasOne(TrainingSchedule::class, 'training_batch_id');
    }

    public function trainingResults(): HasMany
    {
        return $this->hasMany(TrainingResult::class, 'training_batch_id');
    }

    // Helper methods
    public function getEnrolledCountAttribute()
    {
        return $this->applications()->count();
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_students - $this->enrolled_count;
    }

    public function getIsFullAttribute()
    {
        return $this->enrolled_count >= $this->max_students;
    }

    public function getBatchNameAttribute()
    {
        return "{$this->nc_program} - Batch {$this->batch_number}";
    }

    public function hasSchedule()
    {
        return $this->trainingSchedule()->exists();
    }

    // Scopes
    public function scopeEnrolling($query)
    {
        return $query->where('status', self::STATUS_ENROLLING);
    }

    public function scopeFull($query)
    {
        return $query->where('status', self::STATUS_FULL);
    }

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

    public function scopeForProgram($query, $ncProgram)
    {
        return $query->where('nc_program', $ncProgram);
    }
    public function hasAllReferenceNumbers()
    {
        return $this->applications()->whereNull('reference_number')->count() === 0;
    }

    public function getMissingReferenceCount()
    {
        return $this->applications()->whereNull('reference_number')->count();
    }

    public function getReferenceNumberStatus()
    {
        $total = $this->applications()->count();
        $missing = $this->getMissingReferenceCount();
        
        if ($missing === 0) {
            return ['status' => 'complete', 'badge' => 'bg-success', 'text' => 'Complete'];
        }
        
        return [
            'status' => 'missing', 
            'badge' => 'bg-warning', 
            'text' => "Missing ({$missing}/{$total})"
        ];
    }
}
