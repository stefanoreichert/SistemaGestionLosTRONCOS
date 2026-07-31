<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function edit(): View
    {
        return view('auth.change-password');
    }

    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['password' => Hash::make((string) $request->input('password'))]);
        $request->session()->put('password_hash_web', $user->getAuthPassword());

        return back()->with('status', 'Contraseña actualizada correctamente.');
    }
}
