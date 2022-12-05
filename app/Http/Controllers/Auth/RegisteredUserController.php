<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendPasswordNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return view('super-admin.dashboard', compact('users'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'integer', 'unique:users'],
            'emp_id' => ['required', 'string', 'max:14', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'title' => ['required', 'string', 'max:6'],
            'contact' => ['string', 'max:20'],
            'is_active' => ['required', 'integer', 'max:3'],
            'avatar' => ['image', 'mimes:jpg,png', 'max:50'],
        ]);

        $greeting = 'Hello'.' '.$request->name;
        $body = 'Your password is'.' '.$request->password;
        $actiontext = 'Click to Login';
        $details = [
            'greeting' => $greeting,
            'body' => $body,
            'actiontext' => $actiontext,
            'actionurl' => config('app.url'),
        ];

        $input = $request->all();
        $avatarPath = '';
        if ($request->hasFile('avatar')) {
            $avatarName = date('YmdHis').'.'.$request->file('avatar')->extension();

            $avatarPath = $request->file('avatar')->storeAs('/images/profile', $avatarName, 'public');
        } else {
            $avatarPath = null;
        }

        $user = User::create([
            'employee_id' => $input['employee_id'],
            'emp_id' => $input['emp_id'],
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'title' => $input['title'],
            'contact' => $input['contact'],
            'avatar' => $avatarPath,
            'is_active' => $input['is_active'],

        ]);

        // event(new Registered($user));

        // Auth::login($user);

        $insertedUser = User::findOrFail($user->id);
        Notification::send($insertedUser, new SendPasswordNotification($details));

        return redirect()->back()->with('success', 'User Added Successfully and Password sent to '.$input['email']);
    }

    public function update(Request $request, User $user)
    {
        if ($request->filled(['password', 'password_confirmation', 'current_password'])) {
            if (Hash::check($request->current_password, auth()->user()->password)) {
                $request->validate([
                    'password' => ['required',
                        Password::min(8)
                                    ->mixedCase()
                                    ->numbers()
                                    ->symbols()
                                    ->uncompromised(),
                        'confirmed', ],
                ]);
            } else {
                return redirect()->back()->with('error', 'Either current password is incorrect or password confirmation failed');
            }
            $input = Hash::make($request->password);
            $user->update([
                'password' => $input,
            ]);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        } elseif ($request->filled(['declaration'])) {
            $user->update($request->all());

            return redirect()->back()->with('success', 'Declaration successfully submitted, Welcome to MERP');
        } else {
            $request->validate([
                'employee_id' => ['required', 'integer'],
                'emp_id' => ['required', 'string', 'max:14'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'title' => ['required', 'string', 'max:6'],
                'contact' => ['string', 'max:20'],
                'is_active' => ['required', 'integer', 'max:3'],
                'avatar' => ['image', 'mimes:jpg,png', 'max:50'],
            ]);

            $input = $request->all();
            $currentAvatar = $user->avatar;
            $avatarPath = '';
            if ($request->hasFile('avatar')) {
                $avatarName = date('YmdHis').'.'.$request->file('avatar')->extension();

                $avatarPath = $request->file('avatar')->storeAs('/images/profile', $avatarName, 'public');
                $userAvatar = storage_path('app/public/').$currentAvatar;
                if (file_exists($userAvatar)) {
                    @unlink($userAvatar);
                }
                $user->update([
                    'employee_id' => $input['employee_id'],
                    'emp_id' => $input['emp_id'],
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'title' => $input['title'],
                    'contact' => $input['contact'],
                    'avatar' => $avatarPath,
                    'is_active' => $input['is_active'],
                ]);
            } else {
                unset($input['avatar']);
                $user->update([
                    'employee_id' => $input['employee_id'],
                    'emp_id' => $input['emp_id'],
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'title' => $input['title'],
                    'contact' => $input['contact'],
                    'is_active' => $input['is_active'],
                ]);
            }

            return redirect()->back()->with('success', 'User Updated Successfully!');
        }
    }
}
