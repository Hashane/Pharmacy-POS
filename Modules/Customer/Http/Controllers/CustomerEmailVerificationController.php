<?php

namespace Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class CustomerEmailVerificationController extends Controller
{
    public function notice()
    {
        return view('customer::auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user('customer')->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        if ($request->user('customer')->markEmailAsVerified()) {
            event(new Verified($request->user('customer')));
        }

        return redirect()->route('customer.dashboard')->with('success', 'Email verified successfully!');
    }

    public function resend(Request $request)
    {
        $user = $request->user('customer');

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        $user->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    }
}