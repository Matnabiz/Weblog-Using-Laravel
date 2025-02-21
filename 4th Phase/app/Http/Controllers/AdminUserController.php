<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index(){
        $users = User::all(); // Fetch all users
        return response()->json($users);
    }

    public function updateUser(Request $request, $id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update($request->only(['name', 'email', 'role']));
        return response()->json(['message' => 'User updated successfully']);
    }
    
    public function destroy($id){
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

}
