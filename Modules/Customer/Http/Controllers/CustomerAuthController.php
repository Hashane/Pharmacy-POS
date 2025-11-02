<?php

namespace Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Modules\People\Entities\Customer;

class CustomerAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('customer::auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $customer = Customer::firstOrCreate(
            ['email' => $request->email],
            [
                'customer_name'   => $request->name,
                'email'  => $request->email,
                'password'        => Hash::make($request->password),
                'customer_phone'  => $request->phone,
                'city'            => $request->city,
                'country'         => $request->country,
                'address'         => $request->address,
            ]
        );

//        event(new Registered($customer));

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.verification.notice');
    }

    public function showLoginForm()
    {
        return view('customer::auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}
