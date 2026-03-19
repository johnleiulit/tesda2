<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationView extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'viewed_at',
        'view_type',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];
    
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
