<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserStorageProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function Register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:40',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        // Create user folder + default directories on FTP
        UserStorageProvisioner::provision($user);

        Auth::login($user);
        return redirect(route('home'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect(route('home'));
        }

        return redirect(route('auth.login.index'))->with('error', 'Wrong credentials');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }
}
