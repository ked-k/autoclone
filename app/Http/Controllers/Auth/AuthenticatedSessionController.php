<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LoginActivity;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        //return $request;

        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::user()->hasPermission(['manager-access'])) {
            return redirect()->intended(RouteServiceProvider::HOME);
        } elseif (Auth::user()->hasPermission(['master-access'])) {
            return redirect()->route('master-dashboard');
        } elseif (Auth::user()->hasPermission(['normal-access'])) {
            return redirect()->route('user-dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        LoginActivity::addToLog('logged Out', Auth::user()->email, $request->ip());

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
