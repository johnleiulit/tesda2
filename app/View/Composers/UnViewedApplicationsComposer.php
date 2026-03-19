<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Application;

class UnviewedApplicationsComposer
{
    public function compose(View $view): void
    {
        // Count applications that have NEVER been viewed by ANY admin
        $unviewedCount = Application::where('status', Application::STATUS_PENDING)
            ->whereDoesntHave('views')
            ->count();
        
        $view->with('unviewedApplicationsCount', $unviewedCount);
    }
}
