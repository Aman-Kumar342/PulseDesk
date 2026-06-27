<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller {
    public function login(Request $r) {
        $data = $r->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials']);
        }
        return ['token' => $user->createToken('api')->plainTextToken, 'user' => $user->load('organization')];
    }
    public function me(Request $r) { return $r->user()->load('organization'); }
    public function logout(Request $r) { $r->user()->currentAccessToken()->delete(); return ['ok' => true]; }

    public function register(Request $r) {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'organization_name' => 'required|string|max:255',
        ]);
        $org = Organization::create(['name' => $data['organization_name']]);
        $user = User::create([
            'organization_id' => $org->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);
        return response()->json(['token' => $user->createToken('api')->plainTextToken, 'user' => $user->load('organization')], 201);
    }
}
