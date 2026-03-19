<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwspAnnouncement extends Model
{
    protected $fillable = [
        'program_name',
        'total_slots',
        'filled_slots',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Get remaining slots
    public function getRemainingSlots()
    {
        return $this->total_slots - $this->filled_slots;
    }

    // Check if slots are available
    public function hasAvailableSlots()
    {
        return $this->getRemainingSlots() > 0;
    }

    // Get active announcement (only one at a time)
    public static function getActive()
    {
        $announcement = self::where('is_active', true)->first();
        
        // Return only if it has available slots
        if ($announcement && $announcement->hasAvailableSlots()) {
            return $announcement;
        }
        
        return null;
    }

    // Increment filled slots when admin approves
    public function incrementFilledSlots()
    {
        $this->filled_slots++;
        
        // Auto-close if slots are full
        if ($this->filled_slots >= $this->total_slots) {
            $this->is_active = false;
        }
        
        $this->save();
    }
}
