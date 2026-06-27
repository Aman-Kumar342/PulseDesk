<?php
namespace Tests\Feature;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class TenancyTest extends TestCase {
    use RefreshDatabase;
    public function test_org_a_cannot_see_or_modify_org_b_data(): void {
        $orgA = Organization::create(['name'=>'A']);
        $orgB = Organization::create(['name'=>'B']);
        $userA = User::create(['organization_id'=>$orgA->id,'name'=>'A','email'=>'a@a.test','password'=>Hash::make('password'),'role'=>'agent']);
        $userB = User::create(['organization_id'=>$orgB->id,'name'=>'B','email'=>'b@b.test','password'=>Hash::make('password'),'role'=>'agent']);
        $ticketB = Ticket::create(['organization_id'=>$orgB->id,'subject'=>'B SECRET','description'=>'secret','status'=>'open','priority'=>'high','requester_id'=>$userB->id]);

        // list: A must not see B's ticket
        $this->actingAs($userA,'sanctum')->getJson('/api/tickets')->assertOk()->assertJsonMissing(['subject'=>'B SECRET']);
        // show: cross-tenant fetch -> 404 (scoped out)
        $this->actingAs($userA,'sanctum')->getJson("/api/tickets/{$ticketB->id}")->assertNotFound();
        // update: cross-tenant modify -> 404
        $this->actingAs($userA,'sanctum')->patchJson("/api/tickets/{$ticketB->id}", ['status'=>'closed'])->assertNotFound();
        // and B's data is unchanged
        $this->assertSame('open', $ticketB->fresh()->status);
    }
}
