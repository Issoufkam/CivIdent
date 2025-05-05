<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function showPhoneForm()
    {
        return view('auth.passwords.phone');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'telephone' => 'required|digits:10|exists:users,telephone',
        ]);

        $code = rand(100000, 999999);
        $telephone = $request->telephone;

        // Stocker le code temporairement (10 minutes)
        Cache::put('reset_code_' . $telephone, $code, now()->addMinutes(10));

        // Ici tu enverras le code par SMS
        Log::info("Code de réinitialisation pour $telephone : $code"); // remplace ceci par une API SMS comme Twilio

        return redirect()->route('password.phone.verify', ['telephone' => $telephone])
                         ->with('success', 'Code envoyé par SMS.');
    }

    public function showVerificationForm(Request $request)
    {
        return view('auth.passwords.verify_code', ['telephone' => $request->telephone]);
    }

    public function verifyAndReset(Request $request)
    {
        $request->validate([
            'telephone' => 'required|digits:10|exists:users,telephone',
            'code' => 'required|digits:6',
            'password' => 'required|confirmed|min:8',
        ]);

        $telephone = $request->telephone;
        $code = Cache::get('reset_code_' . $telephone);

        if (!$code || $code != $request->code) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        $user = Utilisateur::where('telephone', $telephone)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget('reset_code_' . $telephone);

        return redirect()->route('login')->with('success', 'Mot de passe réinitialisé avec succès.');
    }
}
