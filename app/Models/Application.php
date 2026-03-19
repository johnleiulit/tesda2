<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;
use App\Models\WorkExperience;
use App\Models\Training;
use App\Models\LicensureExam;
use App\Models\CompetencyAssessment;
use App\Models\AssessmentBatch;
use App\Models\TwspDocument;
use App\Models\ApplicationChange;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'application_type',
        'title_of_assessment_applied_for',
        'photo',
        'surname',
        'firstname',
        'middlename',
        'middleinitial',
        'name_extension',
        'region_code',
        'region_name',
        'province_code',
        'province_name',
        'city_code',
        'city_name',
        'barangay_code',
        'barangay_name',
        'district',
        'street_address',
        'zip_code',
        'mothers_name',
        'fathers_name',
        'sex',
        'civil_status',
        'mobile',
        'email',
        'highest_educational_attainment',
        'employment_status',
        'birthdate',
        'birthplace',
        'age',
        'status',
        'training_batch_id',
        'training_schedule_id',
        'training_status',
        'training_completed_at',
        'training_remarks',
        'reviewed_by',
        'reviewed_at',
        'review_remarks',
        'assessment_batch_id',
        'assessment_status',
        'assessment_date',
         // New fields for pages 3-4
        'nationality',
        'employment_before_training_status',
        'employment_before_training_type',
        'birthplace_city',
        'birthplace_province',
        'birthplace_region',
        'educational_attainment_before_training',
        'parent_guardian_name',
        'parent_guardian_street',
        'parent_guardian_barangay',
        'parent_guardian_district',
        'parent_guardian_city',
        'parent_guardian_province',
        'parent_guardian_region',
        'learner_classification',
        'scholarship_type',
        'privacy_consent',
        'payment_proof',
        'payment_status',
        'payment_submitted_at',
        'payment_remarks',
        'correction_requested',
        'correction_message',
        'correction_requested_at',
        'was_corrected',    
        'resubmitted_at',
        'is_reassessment',
        'reassessment_fee',
        'reassessment_payment_proof',
        'reassessment_payment_date',
        'reassessment_payment_status',
        'reassessment_attempt',
        'second_reassessment_payment_proof',
        'second_reassessment_payment_date',
        'second_reassessment_payment_status',
    ];
    protected $casts = [
        'reviewed_at' => 'datetime',
        'training_completed_at' => 'date',
        'assessment_date' => 'datetime',
        'learner_classification' => 'array',
        'payment_submitted_at' => 'datetime',
        'correction_requested' => 'boolean',
        'correction_requested_at' => 'datetime',
        'was_corrected' => 'boolean', 
        'resubmitted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function twspDocument(): HasOne
    {
        return $this->hasOne(TwspDocument::class);
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }

    public function licensureExams(): HasMany
    {
        return $this->hasMany(LicensureExam::class);
    }

    public function competencyAssessments(): HasMany
    {
        return $this->hasMany(CompetencyAssessment::class);
    }

    public function trainingBatch(): BelongsTo
    {
        return $this->belongsTo(TrainingBatch::class);
    }

    public function trainingSchedule(): BelongsTo
    {
        return $this->belongsTo(TrainingSchedule::class);
    }

    public function trainingResult(): HasOne
    {
        return $this->hasOne(TrainingResult::class);
    }

    public function assessmentBatch(): BelongsTo
    {
        return $this->belongsTo(AssessmentBatch::class);
    }
    public function assessmentResult(): HasOne
    {
        return $this->hasOne(AssessmentResult::class)->latestOfMany('assessed_at');
    }
    public function employmentRecord()
    {
        return $this->hasOne(EmploymentRecord::class);
    }

    public function changes(): HasMany
    {
        return $this->hasMany(ApplicationChange::class)->orderBy('changed_at', 'desc');
    }

    public function latestChanges(): HasMany
    {
        return $this->hasMany(ApplicationChange::class)
            ->where('changed_at', $this->resubmitted_at)
            ->orderBy('field_name');
    }

    // Add a new method to get ALL assessment results
    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    // Add a method to get the latest assessment result
    public function latestAssessmentResult(): HasOne
    {
        return $this->assessmentResult();
    }
        // Reassessment methods
    public function needsReassessment(): bool
    {
        return $this->assessmentResult && 
            $this->assessmentResult->result === AssessmentResult::RESULT_FAIL;
    }

    public function canPayForReassessment(): bool
    {
        return $this->needsReassessment() && 
            ($this->reassessment_payment_status === null || 
                $this->reassessment_payment_status === 'rejected');
    }

    public function hasVerifiedReassessmentPayment(): bool
    {
        return $this->reassessment_payment_status === 'verified';
    }

    public function getReassessmentFee(): float
    {
        $fees = [
            'VISUAL GRAPHIC DESIGN NC III' => 1500.00,
            'EVENTS MANAGEMENT SERVICES NC III' => 1500.00,
            'TOURISM PROMOTION SERVICES NC II' => 1200.00,
            'BOOKKEEPING NC III' => 1500.00,
            'PHARMACY SERVICES NC III' => 1500.00,
        ];
        
        return $fees[strtoupper($this->title_of_assessment_applied_for)] ?? 1500.00;
    }

    public function getLastAttemptCocResults()
    {
        if (!$this->assessmentResult) {
            return collect([]);
        }
        
        return $this->assessmentResult->cocResults;
    }

    public function getNycCocs()
    {
        if (!$this->assessmentResult) {
            return collect([]);
        }
        
        return $this->assessmentResult->cocResults()
            ->where('result', 'not_yet_competent')
            ->get();
    }

    public function getCompetentCocs()
    {
        if (!$this->assessmentResult) {
            return collect([]);
        }
        
        return $this->assessmentResult->cocResults()
            ->where('result', 'competent')
            ->get();
    }

    public function getLastAssessmentDate()
    {
        return $this->assessmentResult?->assessed_at;
    }

    public function getAllAssessmentAttempts()
    {
        return AssessmentResult::where('application_id', $this->id)
            ->with('cocResults')
            ->orderBy('assessed_at', 'desc')
            ->get();
    }
    public function canReassess(): bool
    {
        return $this->assessment_attempt_count < 2;
    }

    public function hasFailedTwice(): bool
    {
        return $this->reassessment_attempt >= 3 && $this->assessmentResults()
        ->where('result', 'Not Yet Competent')
        ->count() >= 3;
    }
    public function canPayForSecondReassessment(): bool
    {
        return $this->needsReassessment() && 
            $this->hasVerifiedReassessmentPayment() && 
            ($this->second_reassessment_payment_status === null || $this->second_reassessment_payment_status === 'rejected');
    }

    public function hasVerifiedSecondReassessmentPayment(): bool
    {
        return $this->second_reassessment_payment_status === 'verified';
    }

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_SUBMITTED = 'submitted';
    public const PAYMENT_STATUS_VERIFIED = 'verified';
    public const PAYMENT_STATUS_REJECTED = 'rejected';

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

     // training status constants
    public const TRAINING_STATUS_ENROLLED = 'enrolled';
    public const TRAINING_STATUS_ONGOING = 'ongoing';
    public const TRAINING_STATUS_COMPLETED = 'completed';
    public const TRAINING_STATUS_FAILED = 'failed';

    // Assessment status constants
    public const ASSESSMENT_STATUS_PENDING = 'pending';
    public const ASSESSMENT_STATUS_ASSIGNED = 'assigned';
    public const ASSESSMENT_STATUS_COMPLETED = 'completed';
    public const ASSESSMENT_STATUS_FAILED = 'failed';

    public function needsCorrection(): bool
    {
        return $this->correction_requested === true;
    }

    public function requiresTraining(): bool
    {
        return $this->title_of_assessment_applied_for === 'BOOKKEEPING NC III' 
            && $this->application_type === 'TWSP';
    }
    // Check if training is completed
    public function isTrainingCompleted()
    {
        return $this->training_status === self::TRAINING_STATUS_COMPLETED;
    }

    public function isEligibleForAssessment()
    {
        // For BOOKKEEPING with TWSP: must complete training
        if ($this->title_of_assessment_applied_for === 'BOOKKEEPING NC III' 
            && $this->application_type === 'TWSP') {
            return $this->status === self::STATUS_APPROVED && 
                $this->training_status === self::TRAINING_STATUS_COMPLETED &&
                $this->assessment_status !== self::ASSESSMENT_STATUS_ASSIGNED &&
                $this->assessment_status !== self::ASSESSMENT_STATUS_COMPLETED;
        }
        
        // For BOOKKEEPING with Assessment Only OR Other NCs: skip training, only need approval
        return $this->status === self::STATUS_APPROVED &&
            $this->assessment_status !== self::ASSESSMENT_STATUS_ASSIGNED &&
            $this->assessment_status !== self::ASSESSMENT_STATUS_COMPLETED;
    }

    public function views(): HasMany
    {
        return $this->hasMany(ApplicationView::class);
    }

    // Check if ANY admin has viewed this
    public function hasBeenViewed(): bool
    {
        return $this->views()->exists();
    }

    // Check if CURRENT admin has viewed this
    public function hasBeenViewedBy($userId): bool
    {
        return $this->views()->where('user_id', $userId)->exists();
    }
    
    // Get last view time
    public function lastViewedAt()
    {
        return $this->views()->latest('viewed_at')->first()?->viewed_at;
    }

    // Check if unviewed by anyone
    public function isUnviewed(): bool
    {
        return !$this->hasBeenViewed();
    }
    public function needsPaymentProof(): bool
    {
        return $this->application_type === 'Assessment Only' 
            && $this->status === self::STATUS_PENDING
            && $this->payment_status === self::PAYMENT_STATUS_PENDING;
    }

    public function hasSubmittedPayment(): bool
    {
        return in_array($this->payment_status, [
            self::PAYMENT_STATUS_SUBMITTED,
            self::PAYMENT_STATUS_VERIFIED
        ]);
    }

}
