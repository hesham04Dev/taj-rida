<?php

namespace App\Http\Controllers;

use App\Filament\Pages\NeedsMemorizationPage;
use Request;

class SuraReportController extends Controller
{
    public function print(Request $request)
    {
        // dd(NeedsMemorizationPage::groupedNeeds());

        return view('exports.sura-report', [
            'groups' => NeedsMemorizationPage::groupedNeeds(),
            'date' => now()->format('Y-m-d'),
        ]);
    }
}
