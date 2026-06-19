<?php

namespace App\Filament\Pages;

use App\Models\Conversation;
use App\Models\Message;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MessagingPage extends Page
{
    protected string $view = 'filament.pages.messaging-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'المحادثات';

    public static function getNavigationGroup(): ?string
    {
        return 'التواصل';
    }

    public ?int $activeConversationId = null;

    public string $newMessage = '';

    public bool $showNewConversation = false;

    public array $availableContacts = [];

    public function mount(): void
    {
        $this->loadAvailableContacts();
    }

    private function loadAvailableContacts(): void
    {
        $teacherId = Auth::id();
        $students = \App\Models\Student::withoutGlobalScopes()->where('teacher_id', $teacherId)->get();
        
        $studentGuardians = \App\Models\StudentGuardian::whereIn('student_id', $students->pluck('id'))->get();
        $guardians = \App\Models\Guardian::whereIn('phone', $studentGuardians->pluck('phone'))->get();
        
        $contacts = [];
        $existingConvs = $this->getConversations();

        foreach ($studentGuardians as $sg) {
            $guardian = $guardians->firstWhere('phone', $sg->phone);
            $student = $students->firstWhere('id', $sg->student_id);

            if ($guardian && $student) {
                $alreadyExists = $existingConvs->contains(function ($conv) use ($student, $guardian) {
                    return $conv->student_id === $student->id && $conv->guardian_id === $guardian->id;
                });

                if (! $alreadyExists) {
                    $contacts[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'guardian_id' => $guardian->id,
                        'guardian_name' => $guardian->name,
                    ];
                }
            }
        }

        $this->availableContacts = $contacts;
    }

    public function startConversation(int $studentId, int $guardianId): void
    {
        $conversation = Conversation::firstOrCreateFor($studentId, Auth::id(), $guardianId);

        $this->showNewConversation = false;
        $this->activeConversationId = $conversation->id;
        $this->loadAvailableContacts();
    }

    public function getTitle(): string
    {
        return 'المحادثات';
    }

    public function getConversations(): Collection
    {
        return Conversation::with(['student', 'guardian', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->where('teacher_id', Auth::id())
            ->latest()
            ->get();
    }

    public function selectConversation(int $conversationId): void
    {
        // Mark all guardian messages in this conversation as read by the teacher
        Conversation::where('id', $conversationId)
            ->where('teacher_id', Auth::id())
            ->firstOrFail()
            ->messages()
            ->where('sender_type', 'guardian')
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

        return Conversation::with(['messages', 'student', 'guardian'])
            ->where('teacher_id', Auth::id())
            ->find($this->activeConversationId);
    }

    public function sendMessage(): void
    {
        $this->validate(['newMessage' => 'required|string|max:2000']);

        $conversation = Conversation::where('id', $this->activeConversationId)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();

        $conversation->messages()->create([
            'sender_type' => 'teacher',
            'sender_id' => Auth::id(),
            'body' => trim($this->newMessage),
        ]);

        $this->newMessage = '';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Message::whereHas(
            'conversation',
            fn ($q) => $q->where('teacher_id', Auth::id())
        )
            ->where('sender_type', 'guardian')
            ->whereNull('read_at')
            ->count();

        return $count > 0 ? (string) $count : null;
    }
}
