<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;    
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'لینک بازنشانی رمز عبور به ایمیل شما ارسال شد.'], 200);
        } else {
            $errorMessage = $this->getErrorMessage($status);
            return response()->json(['message' => $errorMessage], 400);   
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



    protected function getErrorMessage($status)
    {
        $messages = [
            Password::INVALID_USER => 'کاربری با این ایمیل یافت نشد.',
            Password::INVALID_TOKEN => 'لینک بازنشانی رمز عبور نادرست است.',
            Password::RESET_LINK_SENT => 'لینک بازنشانی رمز عبور ارسال شد.',
        ];

        return $messages[$status] ?? 'خطای ناشناخته.';
    }

}
