<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function listUsers()
    {
        $users = User::latest()->paginate(2);
        return $users;
    }

    public function store()
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
        ]);
        $user = User::create([
            "name" => request("name"),
            "email" => request("email"),
            "password" => bcrypt(request("password")),
        ]);
        return $user;
    }
    public function update($id)
    {
        $user = User::find($id);
        if ($user->email != request("email")) {
            request()->validate([
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'sometimes|min:8',
            ]);
        } else {
            request()->validate([
                'name' => 'required',
                'email' => 'required',
                'password' => 'sometimes|min:8',
            ]);
        }

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

    public function changeRole($id)
    {
        $user = User::find($id);
        $user->role = request("role");
        $user->save();
        return response()->json(['success' => true]);
    }

    public function search()
    {
        $searchQuery = request('query');
        $users = User::where('name', 'LIKE', '%' . $searchQuery . '%')->latest()->paginate(2);
        return $users;
    }
}
