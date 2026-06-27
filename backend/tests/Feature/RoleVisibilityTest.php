<?php
namespace Tests\Feature;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class RoleVisibilityTest extends TestCase {
    use RefreshDatabase;
    private function makeOrg() {
        $org = Organization::create(['name'=>'Acme']);
        $u = fn($e,$role) => User::create(['organization_id'=>$org->id,'name'=>$e,'email'=>$e,'password'=>Hash::make('password'),'role'=>$role]);
        return [$org, $u('admin@a.test','admin'), $u('agent@a.test','agent'), $u('c1@a.test','customer'), $u('c2@a.test','customer')];
    }
    public function test_customer_sees_only_own_tickets_agent_sees_all(): void {
        [$org,$admin,$agent,$c1,$c2] = $this->makeOrg();
        Ticket::create(['organization_id'=>$org->id,'subject'=>'C1 ticket','description'=>'x','status'=>'open','priority'=>'low','requester_id'=>$c1->id]);
        Ticket::create(['organization_id'=>$org->id,'subject'=>'C2 ticket','description'=>'x','status'=>'open','priority'=>'low','requester_id'=>$c2->id]);
        $this->actingAs($c1,'sanctum')->getJson('/api/tickets')->assertOk()->assertJsonCount(1,'data')->assertJsonPath('data.0.subject','C1 ticket');
        $this->actingAs($agent,'sanctum')->getJson('/api/tickets')->assertOk()->assertJsonCount(2,'data');
    }
    public function test_customer_cannot_see_internal_notes(): void {
        [$org,$admin,$agent,$c1] = $this->makeOrg();
        $t = Ticket::create(['organization_id'=>$org->id,'subject'=>'T','description'=>'x','status'=>'open','priority'=>'low','requester_id'=>$c1->id]);
        $t->replies()->create(['organization_id'=>$org->id,'user_id'=>$c1->id,'body'=>'public msg','is_internal'=>false]);
        $t->replies()->create(['organization_id'=>$org->id,'user_id'=>$agent->id,'body'=>'SECRET internal','is_internal'=>true]);
        $res = $this->actingAs($c1,'sanctum')->getJson("/api/tickets/{$t->id}")->assertOk();
        $res->assertJsonFragment(['body'=>'public msg'])->assertJsonMissing(['body'=>'SECRET internal']);
        // agent sees both
        $this->actingAs($agent,'sanctum')->getJson("/api/tickets/{$t->id}")->assertJsonFragment(['body'=>'SECRET internal']);
    }
}
