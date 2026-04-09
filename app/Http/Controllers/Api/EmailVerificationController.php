<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email уже подтверждён.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Письмо для подтверждения отправлено.']);
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return response()->json(['message' => 'Email успешно подтверждён.']);
    }
}
