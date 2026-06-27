<?php
namespace Database\Seeders;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        $org = Organization::create(['name' => 'Acme Support']);
        $mk = fn($name,$email,$role) => User::create([
            'organization_id' => $org->id, 'name' => $name, 'email' => $email,
            'password' => Hash::make('password'), 'role' => $role,
        ]);
        $admin = $mk('Admin User','admin@pulsedesk.test','admin');
        $agent1 = $mk('Agent One','agent1@pulsedesk.test','agent');
        $agent2 = $mk('Agent Two','agent2@pulsedesk.test','agent');
        $cust1 = $mk('Customer One','customer1@pulsedesk.test','customer');
        $cust2 = $mk('Customer Two','customer2@pulsedesk.test','customer');
        $agents = [$agent1->id, $agent2->id, null];
        $custs = [$cust1->id, $cust2->id];
        $statuses = ['open','pending','resolved','closed'];
        $prios = ['low','medium','high','urgent'];
        for ($i = 1; $i <= 12; $i++) {
            $t = Ticket::create([
                'organization_id' => $org->id,
                'subject' => "Sample ticket #$i",
                'description' => "Customer reported issue number $i. Needs attention.",
                'status' => $statuses[$i % 4],
                'priority' => $prios[$i % 4],
                'requester_id' => $custs[$i % 2],
                'assignee_id' => $agents[$i % 3],
            ]);
            TicketReply::create(['organization_id'=>$org->id,'ticket_id'=>$t->id,'user_id'=>$custs[$i%2],'body'=>'Initial customer message.','is_internal'=>false]);
            if ($i % 2 === 0) {
                TicketReply::create(['organization_id'=>$org->id,'ticket_id'=>$t->id,'user_id'=>$agent1->id,'body'=>'Internal: investigating.','is_internal'=>true]);
            }
        }
        // second org to prove isolation in manual testing
        $org2 = Organization::create(['name' => 'Globex Helpdesk']);
        $b = User::create(['organization_id'=>$org2->id,'name'=>'Beta Admin','email'=>'admin@globex.test','password'=>Hash::make('password'),'role'=>'admin']);
        Ticket::create(['organization_id'=>$org2->id,'subject'=>'Globex only ticket','description'=>'Should never be visible to Acme.','status'=>'open','priority'=>'high','requester_id'=>$b->id]);
    }
}
