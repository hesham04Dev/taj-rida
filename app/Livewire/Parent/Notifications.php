<?php

namespace App\Livewire\Parent;

use App\Models\NotificationRead;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    /**
     * @var Collection<int, NotificationRead>
     */
    public Collection $reads;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    private function loadNotifications(): void
    {
        $guardian = Auth::guard('guardian')->user();

        $this->reads = NotificationRead::with('notification.teacher')
            ->where('guardian_id', $guardian->id)
            ->orderByRaw('read_at IS NOT NULL, created_at DESC')
            ->get();
    }

    /**
     * Mark a specific notification read and refresh.
     */
    public function markRead(int $notificationReadId): void
    {
        $guardian = Auth::guard('guardian')->user();

        NotificationRead::where('id', $notificationReadId)
            ->where('guardian_id', $guardian->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(): void
    {
        $guardian = Auth::guard('guardian')->user();

        NotificationRead::where('guardian_id', $guardian->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    public function getUnreadCountProperty(): int
    {
        return $this->reads->whereNull('read_at')->count();
    }

    public function render(): View
    {
        return view('livewire.parent.notifications')->layout('layouts.parent');
    }
}
