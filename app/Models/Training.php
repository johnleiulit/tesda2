<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'title',
        'venue',
        'date_from',
        'date_to',
        'hours',
        'conducted_by',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
