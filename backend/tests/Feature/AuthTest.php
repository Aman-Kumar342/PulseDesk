<?php
namespace Tests\Feature;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class AuthTest extends TestCase {
    use RefreshDatabase;
    public function test_user_can_login_and_get_token(): void {
        $org = Organization::create(['name' => 'A']);
        User::create(['organization_id'=>$org->id,'name'=>'Ag','email'=>'a@a.test','password'=>Hash::make('password'),'role'=>'agent']);
        $this->postJson('/api/login', ['email'=>'a@a.test','password'=>'password'])
            ->assertOk()->assertJsonStructure(['token','user']);
    }
    public function test_invalid_login_rejected(): void {
        $this->postJson('/api/login', ['email'=>'x@x.test','password'=>'nope'])->assertStatus(422);
    }
}
