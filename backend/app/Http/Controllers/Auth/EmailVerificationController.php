<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return makeApiResponse(null, 'Email already verified.');
        }
        $request->user()->sendEmailVerificationNotification();
        return makeApiResponse(null, 'Verification email sent.');
    }

    public function verify(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return makeApiResponse(null, 'Email already verified.');
        }
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return makeApiResponse(null, 'Email verified successfully.');
    }
}
