<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
class DashboardController extends Controller {
    public function metrics(\Illuminate\Http\Request $r) {
        $own = fn($q) => $r->user()->isCustomer() ? $q->where('requester_id',$r->user()->id) : $q;
        $byStatus = $own(Ticket::query())->selectRaw('status, count(*) c')->groupBy('status')->pluck('c','status');
        $byPriority = $own(Ticket::query())->selectRaw('priority, count(*) c')->groupBy('priority')->pluck('c','priority');
        return [
            'total' => $own(Ticket::query())->count(),
            'open' => (int) ($byStatus['open'] ?? 0),
            'by_status' => $byStatus,
            'by_priority' => $byPriority,
            'unassigned' => $own(Ticket::query())->whereNull('assignee_id')->count(),
        ];
    }
}
