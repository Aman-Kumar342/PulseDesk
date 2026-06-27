<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
class TicketController extends Controller {
    public function index(Request $r) {
        $q = Ticket::query()->with(['requester','assignee','tags'])->withCount('replies');
        if ($r->user()->isCustomer()) $q->where('requester_id', $r->user()->id);
        if ($r->filled('status'))   $q->where('status', $r->status);
        if ($r->filled('priority')) $q->where('priority', $r->priority);
        if ($r->filled('assignee_id')) $q->where('assignee_id', $r->assignee_id);
        if ($r->filled('q')) {
            $term = $r->q;
            $q->where(fn($w) => $w->where('subject','like',"%$term%")->orWhere('description','like',"%$term%"));
        }
        return $q->latest()->paginate(20);
    }
    public function store(Request $r) {
        $data = $r->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'in:low,medium,high,urgent',
            'assignee_id' => 'nullable|exists:users,id',
        ]);
        $data['requester_id'] = $r->input('requester_id', $r->user()->id);
        $ticket = Ticket::create($data);
        return response()->json($ticket->load(['requester','assignee']), 201);
    }
    public function show(\Illuminate\Http\Request $r, Ticket $ticket) {
        if ($r->user()->isCustomer() && $ticket->requester_id !== $r->user()->id) abort(404);
        $ticket->load(['requester','assignee','tags']);
        $replies = $ticket->replies()->with('user')
            ->when($r->user()->isCustomer(), fn($q) => $q->where('is_internal', false))->get();
        $ticket->setRelation('replies', $replies);
        return $ticket;
    }
    public function update(Request $r, Ticket $ticket) {
        $data = $r->validate([
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'in:open,pending,resolved,closed',
            'priority' => 'in:low,medium,high,urgent',
            'assignee_id' => 'nullable|exists:users,id',
        ]);
        $ticket->update($data);
        return $ticket->fresh()->load(['requester','assignee']);
    }
    public function assign(Request $r, Ticket $ticket) {
        $data = $r->validate(['assignee_id' => 'required|exists:users,id']);
        $ticket->update($data);
        return $ticket->fresh()->load('assignee');
    }

    public function export(\Illuminate\Http\Request $r) {
        $tickets = Ticket::with(['requester','assignee'])->latest()->get();
        $cols = ['id','subject','status','priority','requester','assignee','created_at'];
        $cb = function () use ($tickets, $cols) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $cols);
            foreach ($tickets as $t) {
                fputcsv($out, [$t->id, $t->subject, $t->status, $t->priority,
                    optional($t->requester)->name, optional($t->assignee)->name, $t->created_at]);
            }
            fclose($out);
        };
        return response()->streamDownload($cb, 'tickets.csv', ['Content-Type' => 'text/csv']);
    }

    public function activity(Ticket $ticket) {
        abort_unless($ticket->organization_id === auth()->user()->organization_id, 404);
        return $ticket->activities()->with('actor')->get();
    }
}
