<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller {
    // Admin-only: manage users within the admin's organization
    public function index(Request $r) {
        abort_unless($r->user()->isAdmin(), 403, 'Admins only');
        return User::where('organization_id', $r->user()->organization_id)
            ->orderBy('role')->get(['id','name','email','role','organization_id']);
    }
    public function store(Request $r) {
        abort_unless($r->user()->isAdmin(), 403, 'Admins only');
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:agent,customer',
        ]);
        $data['organization_id'] = $r->user()->organization_id;
        $data['password'] = Hash::make($data['password']);
        return response()->json(User::create($data)->only(['id','name','email','role']), 201);
    }
}
