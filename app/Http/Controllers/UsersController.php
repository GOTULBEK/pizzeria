<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            // Create a new user
            $user = new User();
            $user->email = $request->email;
            $user->password = $request->password;

            $user->save();

            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            Log::error('Error creating user: '.$e->getMessage());
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Request Data:', $request->all());
        // Validate the request data
        $request->validate([
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'string|min:6',
        ]);

        // Find the user
        $user = User::findOrFail($id);

        // Update the user
        $user->fill($request->only(['email', 'password']));
        if ($request->has('password')) {
            $user->password = $request->password;
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function destroy($id)
    {
        // Find the user
        $user = User::findOrFail($id);

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}