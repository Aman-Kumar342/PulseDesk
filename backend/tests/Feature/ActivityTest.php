<?php
namespace Tests\Feature;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class ActivityTest extends TestCase {
    use RefreshDatabase;
    public function test_status_change_is_logged_and_listed(): void {
        $org = Organization::create(['name'=>'Acme']);
        $u = User::create(['organization_id'=>$org->id,'name'=>'Ag','email'=>'ag@a.test','password'=>Hash::make('password'),'role'=>'agent']);
        $this->actingAs($u,'sanctum');
        $t = Ticket::create(['organization_id'=>$org->id,'subject'=>'S','description'=>'d','status'=>'open','priority'=>'low','requester_id'=>$u->id]);
        $t->update(['status'=>'resolved']);
        $events = $this->getJson("/api/tickets/{$t->id}/activity")->assertOk()->json();
        $names = array_column($events, 'event');
        $this->assertContains('created', $names);
        $this->assertContains('status_changed', $names);
    }
    public function test_cross_tenant_activity_not_visible(): void {
        $orgA = Organization::create(['name'=>'A']); $orgB = Organization::create(['name'=>'B']);
        $a = User::create(['organization_id'=>$orgA->id,'name'=>'A','email'=>'a@a.test','password'=>Hash::make('password'),'role'=>'agent']);
        $b = User::create(['organization_id'=>$orgB->id,'name'=>'B','email'=>'b@b.test','password'=>Hash::make('password'),'role'=>'agent']);
        $tB = Ticket::create(['organization_id'=>$orgB->id,'subject'=>'B','description'=>'d','status'=>'open','priority'=>'low','requester_id'=>$b->id]);
        $this->actingAs($a,'sanctum')->getJson("/api/tickets/{$tB->id}/activity")->assertNotFound();
    }

    public function test_reply_and_assignment_are_logged(): void {
        $org = Organization::create(['name'=>'Acme']);
        $agent = User::create(['organization_id'=>$org->id,'name'=>'Ag','email'=>'ag2@a.test','password'=>Hash::make('password'),'role'=>'agent']);
        $cust = User::create(['organization_id'=>$org->id,'name'=>'C','email'=>'c2@a.test','password'=>Hash::make('password'),'role'=>'customer']);
        $this->actingAs($agent,'sanctum');
        $t = Ticket::create(['organization_id'=>$org->id,'subject'=>'S','description'=>'d','status'=>'open','priority'=>'low','requester_id'=>$cust->id]);
        $t->update(['assignee_id'=>$agent->id]);
        $t->replies()->create(['organization_id'=>$org->id,'user_id'=>$agent->id,'body'=>'hi','is_internal'=>false]);
        $names = array_column($this->getJson("/api/tickets/{$t->id}/activity")->json(), 'event');
        $this->assertContains('assigned', $names);
        $this->assertContains('replied', $names);
    }
}
