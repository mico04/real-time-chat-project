<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|confirmed|min:8',
        // ]);

        // Auth::login($user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]));

            $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'min:3',
            'max:50',
            'alpha_dash',
        ],
    ]);

    $username = strtolower($validated['name']);

    $users = Cache::get('registered_users', []);

    if (in_array($username, $users, true)) {
        return back()->withErrors([
            'username' => 'Username is already registered.',
        ]);
    }

    $users[] = $username;

    Cache::put(
        'registered_users',
        $users,
        now()->addDays(30)
    );

    Auth::login(new \App\Models\SimpleUser($username));

    $request->session()->regenerate();

        event(new Registered($username));

        return redirect('dashboard');
    }
}
