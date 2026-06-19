<?php

namespace App\Livewire\Parent;

use App\Models\Conversation;
use App\Models\Guardian;
use App\Models\Message;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Messages extends Component
{
    /**
     * @var Collection<int, Conversation>
     */
    public Collection $conversations;

    public ?int $activeConversationId = null;

    public string $newMessage = '';

    /** Controls whether the "new conversation" picker is open */
    public bool $showNewConversation = false;

    /** @var array<int, array{id: int, name: string, teacher_id: int, teacher_name: string}> */
    public array $availableContacts = [];

    public function mount(): void
    {
        $this->loadConversations();
        $this->loadAvailableContacts();
    }

    private function loadConversations(): void
    {
        /** @var Guardian $guardian */
        $guardian = Auth::guard('guardian')->user();

        $this->conversations = Conversation::with([
            'student',
            'teacher',
            'messages' => fn ($q) => $q->latest()->limit(1),
        ])
            ->where('guardian_id', $guardian->id)
            ->latest()
            ->get();
    }

    /**
     * Build the list of contacts (teacher + admin) the parent can start a conversation with,
     * for each of their children.
     */
    private function loadAvailableContacts(): void
    {
        /** @var Guardian $guardian */
        $guardian = Auth::guard('guardian')->user();

        $studentIds = StudentGuardian::where('phone', $guardian->phone)->pluck('student_id');
        $students = Student::withoutGlobalScopes()->whereIn('id', $studentIds)->with('teacher')->get();

        $contacts = [];

        foreach ($students as $student) {
            // Skip if a conversation already exists with this teacher for this student
            $alreadyExists = $this->conversations->contains(function ($conv) use ($student, $guardian) {
                return $conv->student_id === $student->id
                    && $conv->teacher_id === $student->teacher_id
                    && $conv->guardian_id === $guardian->id;
            });

            if (! $alreadyExists && $student->teacher) {
                $contacts[] = [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'teacher_id' => $student->teacher_id,
                    'teacher_name' => $student->teacher->name,
                ];
            }
        }

        $this->availableContacts = $contacts;
    }

    public function startConversation(int $studentId, int $teacherId): void
    {
        /** @var Guardian $guardian */
        $guardian = Auth::guard('guardian')->user();

        // Verify the guardian is linked to this student
        $linked = StudentGuardian::where('phone', $guardian->phone)
            ->where('student_id', $studentId)
            ->exists();

        if (! $linked) {
            return;
        }

        $conversation = Conversation::firstOrCreateFor($studentId, $teacherId, $guardian->id);

        $this->loadConversations();
        $this->loadAvailableContacts();
        $this->showNewConversation = false;
        $this->activeConversationId = $conversation->id;
    }

    public function selectConversation(int $conversationId): void
    {
        /** @var Guardian $guardian */
        $guardian = Auth::guard('guardian')->user();

        // Verify this conversation belongs to this guardian
        $conversation = Conversation::where('id', $conversationId)
            ->where('guardian_id', $guardian->id)
            ->firstOrFail();

        // Mark teacher messages as read
        $conversation->messages()
            ->where('sender_type', 'teacher')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->activeConversationId = $conversationId;
        $this->newMessage = '';
        $this->showNewConversation = false;
    }

    public function getActiveConversation(): ?Conversation
    {
        if (! $this->activeConversationId) {
            return null;
        }

        /** @var Guardian $guardian */
        $guardian = Auth::guard('guardian')->user();

        return Conversation::with(['messages', 'student', 'teacher'])
            ->where('guardian_id', $guardian->id)
            ->find($this->activeConversationId);
    }

    public function sendMessage(): void
    {
        $this->validate(['newMessage' => 'required|string|max:2000']);

        /** @var Guardian $guardian */
        $guardian = Auth::guard('guardian')->user();

        $conversation = Conversation::where('id', $this->activeConversationId)
            ->where('guardian_id', $guardian->id)
            ->firstOrFail();

        $conversation->messages()->create([
            'sender_type' => 'guardian',
            'sender_id' => $guardian->id,
            'body' => trim($this->newMessage),
        ]);

        $this->newMessage = '';
        $this->loadConversations();
    }

    public function getTotalUnreadCountProperty(): int
    {
        /** @var Guardian $guardian */
        $guardian = Auth::guard('guardian')->user();

        return Message::whereHas(
            'conversation',
            fn ($q) => $q->where('guardian_id', $guardian->id)
        )
            ->where('sender_type', 'teacher')
            ->whereNull('read_at')
            ->count();
    }

    public function render(): View
    {
        return view('livewire.parent.messages')->layout('layouts.parent');
    }
}
