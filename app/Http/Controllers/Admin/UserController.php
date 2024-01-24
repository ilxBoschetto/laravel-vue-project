<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Carbon\Carbon;

class UserController extends Controller
{
    public function listUsers()
    {
        $searchQuery = request('query');
        $users = User::query()
            ->when($searchQuery, function ($query, $searchQuery) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%');
            })
            ->latest()->paginate(settings('pagination_limit'));
        $users->each(function ($user) {
            $user->formatted_created_at = Carbon::parse($user->created_at)->format(settings('date_format'));
        });

        // Include formatted users in the response
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
        $user->role = 2;
        //return the formatted created_at
        $user->formatted_created_at = Carbon::parse($user->created_at)->format(settings('date_format'));
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


    public function bulkDelete()
    {
        $users = User::whereIn('id', request('ids'))->delete();
        return response()->json(['message' => 'Users deleted successfully']);
    }
}
