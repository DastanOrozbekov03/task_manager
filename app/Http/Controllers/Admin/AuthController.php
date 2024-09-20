<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Auth::class);
        $users = Auth::paginate(10);
        return response()->json($users, 200);
    }

    public function store(UserRequet $request)
    {
        $this->authorize('create', Auth::class);
        $validated = request()->validated();
        $validated['password'] = Hash::make($validated['password']);
        $user = Auth::create($validated);
        return response()->json($user, 200);
    }

    public function show(Auth $user)
    {
        $this->authorize('view', $user);
        return response()->json($user, 200);
    }

    public function update(UserRequest $request, Auth $user)
    {
        $this->authorize('update', $user);
        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        $user->update($validated);
        return response()->json($user, 200);
    }

    public function destroy(Auth $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.'], 200);
    }
}
