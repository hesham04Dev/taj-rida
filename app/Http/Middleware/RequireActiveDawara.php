<?php

namespace App\Http\Middleware;

use App\Models\Dawara;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveDawara
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $exemptPatterns = [
            '*dawaras*',
            '*settings*',
            '*users*',
            '*creditors*',
            '*budget-transactions*',
            '*curriculum*',
            '*guardians*',
            '*notifications*',
            '*livewire*',
        ];

        $isExempt = false;
        foreach ($exemptPatterns as $pattern) {
            if ($request->routeIs($pattern)) {
                $isExempt = true;
                break;
            }
        }

        if (! $isExempt && ! Dawara::current()) {
            Notification::make()
                ->title('الدورة غير مفعلة')
                ->body('يرجى تفعيل دورة حالية للمتابعة.')
                ->danger()
                ->send();

            return redirect()->route('filament.admin.resources.dawaras.index');
        }

        return $next($request);
    }
}
