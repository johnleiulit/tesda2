<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TwspAnnouncement;
use Illuminate\Http\Request;

class TwspAnnouncementController extends Controller
{
    // Show announcement management page
    public function index()
    {
        $announcement = TwspAnnouncement::where('is_active', true)->first();
        $history = TwspAnnouncement::where('is_active', false)
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        
        return view('admin.twsp.index', compact('announcement', 'history'));
    }

    // Create new announcement
    public function store(Request $request)
    {
        $request->validate([
            'total_slots' => 'required|integer|min:1|max:100'
        ]);

        // Close any existing active announcement
        TwspAnnouncement::where('is_active', true)->update(['is_active' => false]);

        TwspAnnouncement::create([
            'program_name' => 'Bookkeeping NC III',
            'total_slots' => $request->total_slots,
            'filled_slots' => 0,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'TWSP announcement created successfully!');
    }

    // Manually close announcement
    public function close($id)
    {
        $announcement = TwspAnnouncement::findOrFail($id);
        $announcement->is_active = false;
        $announcement->save();

        return redirect()->back()->with('success', 'TWSP announcement closed successfully!');
    }
}
