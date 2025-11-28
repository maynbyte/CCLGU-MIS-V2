<?php

namespace App\Http\Controllers;

use App\Models\FinancialAssistance;
use Illuminate\Http\Request;

class PayoutVerificationController extends Controller
{
    /**
     * Verify and display Financial Assistance payout details via QR token
     * 
     * @param string $qr_token
     * @return \Illuminate\View\View
     */
    public function verify($qr_token)
    {
        // Find FA by QR token with related directory and barangay
        $financialAssistance = FinancialAssistance::with(['directory.barangay'])
            ->where('qr_token', $qr_token)
            ->firstOrFail();

        $directory = $financialAssistance->directory;

        // Prepare payout location (use from FA or fallback to default)
        $payoutLocation = $financialAssistance->payout_location 
            ?? 'City Social Welfare and Development Office, Cauayan City';

        // Format scheduled date
        $scheduledDate = $financialAssistance->scheduled_fa 
            ? \Carbon\Carbon::parse($financialAssistance->scheduled_fa)->format('F d, Y')
            : 'Not yet scheduled';

        return view('public.payout-verify', compact(
            'financialAssistance',
            'directory',
            'payoutLocation',
            'scheduledDate'
        ));
    }
}
