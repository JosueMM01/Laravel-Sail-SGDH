<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isFirstPassword = is_null($user->password);

        $rules = [
            'password' => ['required', Password::defaults(), 'confirmed'],
        ];

        if (! $isFirstPassword) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validateWithBag('updatePassword', $rules);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return back()->with('status', $isFirstPassword ? 'password-set' : 'password-updated');
    }
}
