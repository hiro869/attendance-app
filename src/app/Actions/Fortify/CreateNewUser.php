<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        // ★ Fortify の $input は使わない！
        // ★ request()->all() を使うことで password_confirmation が確実に取れる
        $request = new RegisterRequest();

        $validated = validator(
            request()->all(),   // ← ここが最重要
            $request->rules(),
            $request->messages(),
            $request->attributes()
        )->validate();

        return User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
    }
}
