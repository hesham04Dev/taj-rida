<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SqlConsolePage extends Page
{
    protected string $view = 'filament.pages.sql-console-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    /** Never show in navigation – accessible via direct URL only. */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        if (! config('sql-console.enabled', false)) {
            return false;
        }

        $allowedEmail = config('sql-console.allowed_email', '');

        if (empty($allowedEmail)) {
            return false;
        }

        return auth()->user()?->email === $allowedEmail;
    }

    /** ──────────────── State ──────────────── */
    public string $sql = '';

    public ?array $results = null;

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    public int $rowCount = 0;

    public float $executionTime = 0.0;

    public bool $confirmed = false;

    /** Whether write-mode is enabled (set from config on mount). */
    public bool $isWriteModeEnabled = false;

    /** ──────────────── Livewire lifecycle ──────────────── */
    public function mount(): void
    {
        $this->isWriteModeEnabled = (bool) config('sql-console.write_mode', false);
        $this->checkConfirmation();
    }

    /** ──────────────── Password Confirmation ──────────────── */
    private function checkConfirmation(): void
    {
        // Filament stores the password-confirmed timestamp in the session.
        $confirmedAt = session()->get('auth.password_confirmed_at', 0);
        $timeout = 10 * 60; // 10 minutes

        $this->confirmed = (time() - $confirmedAt) < $timeout;
    }

    public function confirmPassword(string $password): void
    {
        if (! Hash::check($password, auth()->user()->password)) {
            Notification::make()
                ->title('كلمة المرور غير صحيحة')
                ->danger()
                ->send();

            return;
        }

        session()->put('auth.password_confirmed_at', time());
        $this->confirmed = true;

        Notification::make()
            ->title('تم التحقق من كلمة المرور')
            ->success()
            ->send();
    }

    /** ──────────────── SQL Execution ──────────────── */
    public function executeQuery(): void
    {
        $this->results = null;
        $this->errorMessage = null;
        $this->successMessage = null;
        $this->rowCount = 0;

        // Re-check confirmation (10-min window)
        $this->checkConfirmation();

        if (! $this->confirmed) {
            Notification::make()
                ->title('يجب تأكيد كلمة المرور أولاً')
                ->warning()
                ->send();

            return;
        }

        // Rate limiting – max 30 executions per minute per user
        $key = 'sql-console:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, maxAttempts: 30)) {
            $seconds = RateLimiter::availableIn($key);
            Notification::make()
                ->title("لقد تجاوزت الحد المسموح به. حاول مجدداً بعد {$seconds} ثانية.")
                ->danger()
                ->send();

            return;
        }

        RateLimiter::hit($key, decaySeconds: 60);

        $query = trim($this->sql);

        if (empty($query)) {
            $this->errorMessage = 'يرجى إدخال استعلام SQL.';

            return;
        }

        if (! $this->isAllowedStatement($query)) {
            $this->errorMessage = $this->isWriteModeEnabled
                ? 'الاستعلام غير مدعوم. يُسمح فقط بـ: SELECT, SHOW, DESCRIBE, EXPLAIN, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER, TRUNCATE.'
                : 'وضع القراءة فقط مفعّل. يُسمح فقط بـ: SELECT, SHOW, DESCRIBE, EXPLAIN.';

            return;
        }

        // Audit log every execution attempt
        Log::channel('single')->info('[SQL Console] Query executed', [
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'ip' => request()->ip(),
            'query' => $query,
        ]);

        try {
            $start = microtime(true);

            if ($this->isSelectStatement($query)) {
                $rows = DB::select($query);
                $this->executionTime = round((microtime(true) - $start) * 1000, 2);
                $this->rowCount = count($rows);
                $this->results = array_map(fn ($row) => (array) $row, $rows);
            } else {
                // Write statement (only reachable when write mode enabled)
                $affected = DB::statement($query);
                $this->executionTime = round((microtime(true) - $start) * 1000, 2);
                $this->successMessage = $affected
                    ? "تم تنفيذ الاستعلام بنجاح. ({$this->executionTime}ms)"
                    : "تم تنفيذ الاستعلام. ({$this->executionTime}ms)";
            }
        } catch (\Throwable $e) {
            $this->errorMessage = 'خطأ: '.$e->getMessage();
        }
    }

    /** ──────────────── Helpers ──────────────── */
    private function isAllowedStatement(string $query): bool
    {
        $upper = strtoupper(ltrim($query));

        $readOnly = ['SELECT ', 'SHOW ', 'DESCRIBE ', 'DESC ', 'EXPLAIN ', 'SHOW;', 'DESCRIBE;'];

        if ($this->startsWithAny($upper, $readOnly)) {
            return true;
        }

        if ($this->isWriteModeEnabled) {
            $write = ['INSERT ', 'UPDATE ', 'DELETE ', 'CREATE ', 'DROP ', 'ALTER ', 'TRUNCATE '];

            return $this->startsWithAny($upper, $write);
        }

        return false;
    }

    private function isSelectStatement(string $query): bool
    {
        $upper = strtoupper(ltrim($query));

        return $this->startsWithAny($upper, ['SELECT ', 'SHOW ', 'DESCRIBE ', 'DESC ', 'EXPLAIN ', 'SHOW;', 'DESCRIBE;']);
    }

    /**
     * @param  string[]  $prefixes
     */
    private function startsWithAny(string $haystack, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if (str_starts_with($haystack, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function clearResults(): void
    {
        $this->results = null;
        $this->errorMessage = null;
        $this->successMessage = null;
        $this->sql = '';
    }
}
