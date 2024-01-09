<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function listUsers()
    {
        $users = User::latest()->get();
        return $users;
    }

    public function store()
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
        ]);
        return User::create([
            "name" => request("name"),
            "email" => request("email"),
            "password" => bcrypt(request("password")),
        ]);
    }
    public function update($id)
    {
        $user = User::find($id);
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'sometimes|min:8',
        ]);
        $user->update([
            "name" => request("name"),
            "email" => request("email"),
            "password" => request('password') ? bcrypt(request("password")) : $user->password,
        ]);
        return $user;
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return 1;
    }
}
