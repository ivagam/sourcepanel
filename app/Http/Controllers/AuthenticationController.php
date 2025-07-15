<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthenticationController extends Controller
{
    public function forgotPassword(Request $request)
    {

        if ($request->isMethod('post')) {
            $request->validate([
                'email' => 'required|email'
            ]);

            $admin = DB::table('admins')->where('email', $request->email)->first();

            if (!$admin) {
                return back()->with('error', 'Email not found in our records.');
            }

            $newPassword = Str::random(8);

            DB::table('admins')->where('id', $admin->id)->update([
                'password' => Hash::make($newPassword)
            ]);

            Mail::raw("Your new password is: $newPassword", function ($message) use ($admin) {
                $message->to($admin->email)
                        ->subject('Password Reset');
            });

            return back()->with('success', 'A new password has been sent to your email.'.$newPassword);
        }

        return view('authentication/forgotPassword');
    }

    public function signin()
    {
        return view('authentication/signin');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
        
        $remember = $request->has('remember');

        Auth::login($user, $remember);

        session([
            'user_id' => $user->id,
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'role_id' => $user->role_id,
            'profile' => $user->profile,
        ]);

            return redirect()->route('index');
        }

        return back()->withErrors([
            'username' => $user ? null : 'The username you entered does not exist.',
            'password' => $user ? 'The password you entered is incorrect.' : null,
        ])->withInput();
    }

    public function signUp()
    {
        return view('authentication/signUp');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin');
    }
    
}
