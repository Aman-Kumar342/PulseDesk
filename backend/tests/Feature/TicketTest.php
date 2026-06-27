<?php
namespace Tests\Feature;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class TicketTest extends TestCase {
    use RefreshDatabase;
    private function agent(): User {
        $org = Organization::create(['name' => 'Acme']);
        return User::create(['organization_id'=>$org->id,'name'=>'Ag','email'=>'ag@acme.test','password'=>Hash::make('password'),'role'=>'agent']);
    }
    public function test_agent_can_create_and_list_tickets(): void {
        $u = $this->agent();
        $this->actingAs($u, 'sanctum')->postJson('/api/tickets', [
            'subject'=>'Cannot login','description'=>'help','priority'=>'high','requester_id'=>$u->id,
        ])->assertCreated()->assertJsonPath('subject','Cannot login');
        $this->actingAs($u, 'sanctum')->getJson('/api/tickets')->assertOk()->assertJsonPath('data.0.subject','Cannot login');
    }
    public function test_ticket_filter_and_search(): void {
        $u = $this->agent();
        Ticket::create(['organization_id'=>$u->organization_id,'subject'=>'Billing problem','description'=>'x','status'=>'open','priority'=>'urgent','requester_id'=>$u->id]);
        Ticket::create(['organization_id'=>$u->organization_id,'subject'=>'Other','description'=>'y','status'=>'closed','priority'=>'low','requester_id'=>$u->id]);
        $this->actingAs($u,'sanctum')->getJson('/api/tickets?status=open')->assertOk()->assertJsonCount(1,'data');
        $this->actingAs($u,'sanctum')->getJson('/api/tickets?q=Billing')->assertOk()->assertJsonPath('data.0.subject','Billing problem');
    }
    public function test_customer_reply_cannot_be_internal(): void {
        $org = Organization::create(['name'=>'Acme2']);
        $cust = User::create(['organization_id'=>$org->id,'name'=>'C','email'=>'c@a.test','password'=>Hash::make('password'),'role'=>'customer']);
        $t = Ticket::create(['organization_id'=>$org->id,'subject'=>'S','description'=>'d','status'=>'open','priority'=>'low','requester_id'=>$cust->id]);
        $this->actingAs($cust,'sanctum')->postJson("/api/tickets/{$t->id}/replies", ['body'=>'hi','is_internal'=>true])
            ->assertCreated()->assertJsonPath('is_internal', false);
    }
}
