<?php
namespace Tests\Feature;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class ExportTest extends TestCase {
    use RefreshDatabase;
    public function test_tickets_can_be_exported_as_csv(): void {
        $org = Organization::create(['name'=>'Acme']);
        $u = User::create(['organization_id'=>$org->id,'name'=>'Ag','email'=>'ag@a.test','password'=>Hash::make('password'),'role'=>'agent']);
        Ticket::create(['organization_id'=>$org->id,'subject'=>'Export me','description'=>'x','status'=>'open','priority'=>'high','requester_id'=>$u->id]);
        $res = $this->actingAs($u,'sanctum')->get('/api/tickets/export');
        $res->assertOk();
        $this->assertStringContainsString('text/csv', $res->headers->get('content-type'));
        $this->assertStringContainsString('Export me', $res->streamedContent());
    }
}
