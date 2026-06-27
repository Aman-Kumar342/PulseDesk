<?php
namespace App\Models;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
class TicketReply extends Model {
    use BelongsToOrganization;
    protected $fillable = ['organization_id','ticket_id','user_id','body','is_internal'];
    protected $casts = ['is_internal' => 'boolean'];
    public function ticket() { return $this->belongsTo(Ticket::class); }
    public function user() { return $this->belongsTo(User::class); }

    protected static function booted(): void
    {
        static::created(function ($r) {
            \App\Models\TicketActivity::create([
                'organization_id' => $r->organization_id,
                'ticket_id' => $r->ticket_id,
                'actor_id' => $r->user_id,
                'event' => 'replied',
                'meta' => ['internal' => (bool) $r->is_internal],
            ]);
        });
    }
}
