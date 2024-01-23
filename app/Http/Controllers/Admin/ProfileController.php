<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Fortify\UpdateUserPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->only(["name", "email", "role"]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            "name" => ['required'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($request->user()->id)],
        ]);
        $request->user()->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        return response()->json(['success', true], 200);
    }
    public function changePassword(Request $request, UpdateUserPassword $updateUserPassword)
    {
        $updateUserPassword->update(

            auth()->user(),
            [
                'current_password' => $request->current_password,
                'password' => $request->new_password,
                'password_confirmation' => $request->confirm_new_password,
            ]
        );
        return response()->json(['message' => 'Password changed successfully']);
    }
}
