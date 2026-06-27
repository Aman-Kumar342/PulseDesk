<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class RegisterTest extends TestCase {
    use RefreshDatabase;
    public function test_register_creates_org_admin_and_token(): void {
        $this->postJson('/api/register', [
            'name' => 'New Admin', 'email' => 'new@acme.test', 'password' => 'secret123',
            'organization_name' => 'New Acme',
        ])->assertCreated()
          ->assertJsonStructure(['token','user'=>['id','organization','role']])
          ->assertJsonPath('user.role','admin')
          ->assertJsonPath('user.organization.name','New Acme');
        $this->assertDatabaseHas('users', ['email' => 'new@acme.test', 'role' => 'admin']);
    }
}
