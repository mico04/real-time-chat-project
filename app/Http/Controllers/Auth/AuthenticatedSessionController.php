<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cache;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // $request->authenticate();

        // $request->session()->regenerate();

        // return redirect()->intended(route('dashboard', absolute: false));

         $validated = $request->validate([
        'name' => ['required', 'string'],
    ]);

    $username = strtolower($validated['name']);

    if (Auth::attempt([
        'username' => $username,
    ])) {
        $request->session()->regenerate();

        return redirect('/chat');
    } else {
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
        now()->endOfDay()
    );

    Auth::login(new \App\Models\SimpleUser($username));

    $request->session()->regenerate();

        event(new Registered($username));

        return redirect('chat');
    }

    // return back()->withErrors([
    //     'username' => 'Username is not registered.',
    // ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Auth::guard('web')->logout();

        // $request->session()->invalidate();

        // $request->session()->regenerateToken();

        // return redirect('/');
        Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
    }
}
