<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'لینک بازنشانی رمز عبور به ایمیل شما ارسال شد.'], 200);
        } else {
            return response()->json(['message' => 'خطایی در ارسال لینک بازنشانی رمز عبور رخ داد.'], 400);
        }
    }



    public function resetPassword(Request $request)
    {  
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'رمز عبور با موفقیت بازنشانی شد.'], 200);
        } else {
            return response()->json(['message' => 'خطا در بازنشانی رمز عبور.'], 400);
        }
    }
}
