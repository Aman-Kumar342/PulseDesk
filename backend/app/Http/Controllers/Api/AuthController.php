<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
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
}
