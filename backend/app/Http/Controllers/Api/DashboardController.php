<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
class DashboardController extends Controller {
    public function metrics() {
        $byStatus = Ticket::selectRaw('status, count(*) c')->groupBy('status')->pluck('c','status');
        $byPriority = Ticket::selectRaw('priority, count(*) c')->groupBy('priority')->pluck('c','priority');
        return [
            'total' => Ticket::count(),
            'open' => (int) ($byStatus['open'] ?? 0),
            'by_status' => $byStatus,
            'by_priority' => $byPriority,
            'unassigned' => Ticket::whereNull('assignee_id')->count(),
        ];
    }
}
