<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'date_employed',
        'occupation',
        'employer_name',
        'employer_address',
        'employer_classification',
        'monthly_income',
        'viewed_at',
    ];

    protected $casts = [
        'date_employed' => 'date',
        'monthly_income' => 'decimal:2',
        'viewed_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
 
    public function isNew()
    {
        return is_null($this->viewed_at);
    }

    public function markAsViewed()
    {
        $this->update(['viewed_at' => now()]);
    }

}
