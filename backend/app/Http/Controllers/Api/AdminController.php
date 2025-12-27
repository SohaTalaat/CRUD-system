<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admin = Admin::select('id', 'name', 'email', 'phone', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($admin);
    }

    public function store(Request $request)

    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:11|unique:admins',
            'status' => 'required|in:active,inactive',
        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);


        $admin = Admin::create([
            $validatedData
        ]);

        return response()->json($admin, 201);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|max:255|unique:admins,email,' . $admin->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'sometimes|string|max:11|unique:admins,phone,' . $admin->id,
            'status' => 'sometimes|in:active,inactive',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }


        $admin->update($validatedData);

        return response()->json($admin);
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully'], 200);
    }
}
