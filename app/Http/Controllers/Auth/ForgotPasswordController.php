<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\Personnel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Notifications\ResetPassword;

class ForgotPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);


        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status),'token' => $request->input('token')], 200)
            : response()->json(['error' => __($status)], 500);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $status = Password::reset($request->only(
            'email', 'password', 'password_confirmation', 'token'
        ), function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password),
                'remember_token' => \Illuminate\Support\Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
        );
         if($status === Password::PASSWORD_RESET)
         {
          $user = User::where('email', $request->email)->first();
         if ($user->hasRole('client')) {
                             $client = Client::where('email', $user->email)->first();
                             $client->password = $request->password;
                             $client->save();
                         } elseif ($user->hasRole('personnel')) {
                             $personnel = Personnel::where('email', $user->email)->first();
                             $personnel->password = $request->password;
                             $personnel->save();
                         }
         return response()->json(['message' => __($status), 'token' => $request->input('token')], 200);
         }
        return response()->json(['error' => __($status)], 500);
    }



    public function broker()
    {
        return Password::broker();
    }
}
