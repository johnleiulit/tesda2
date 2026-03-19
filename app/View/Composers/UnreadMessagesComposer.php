<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\ContactMessage;

class UnreadMessagesComposer
{
    public function compose(View $view): void
    {
        $unreadCount = ContactMessage::where('is_read', false)->count();
        
        $view->with('unreadMessagesCount', $unreadCount);
    }
}
