<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
class TicketReplyController extends Controller {
    public function store(Request $r, Ticket $ticket) {
        $data = $r->validate(['body' => 'required|string', 'is_internal' => 'boolean']);
        // customers can never post internal notes
        $internal = $r->user()->isCustomer() ? false : (bool)($data['is_internal'] ?? false);
        $reply = $ticket->replies()->create([
            'organization_id' => $ticket->organization_id,
            'user_id' => $r->user()->id,
            'body' => $data['body'],
            'is_internal' => $internal,
        ]);
        return response()->json($reply->load('user'), 201);
    }
}
