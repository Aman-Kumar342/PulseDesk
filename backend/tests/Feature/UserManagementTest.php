<?php
namespace Tests\Feature;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class UserManagementTest extends TestCase {
    use RefreshDatabase;
    private function mk($org,$e,$role){ return User::create(['organization_id'=>$org->id,'name'=>$e,'email'=>$e,'password'=>Hash::make('password'),'role'=>$role]); }
    public function test_admin_can_list_and_create_users_agent_cannot(): void {
        $org = Organization::create(['name'=>'Acme']);
        $admin = $this->mk($org,'admin@a.test','admin');
        $agent = $this->mk($org,'agent@a.test','agent');
        // admin lists org users
        $this->actingAs($admin,'sanctum')->getJson('/api/users')->assertOk()->assertJsonCount(2);
        // agent forbidden
        $this->actingAs($agent,'sanctum')->getJson('/api/users')->assertForbidden();
        // admin creates a new agent
        $this->actingAs($admin,'sanctum')->postJson('/api/users',['name'=>'New Agent','email'=>'na@a.test','password'=>'secret123','role'=>'agent'])
            ->assertCreated()->assertJsonPath('role','agent');
        $this->actingAs($agent,'sanctum')->postJson('/api/users',['name'=>'X','email'=>'x@a.test','password'=>'secret123','role'=>'agent'])->assertForbidden();
    }
}
