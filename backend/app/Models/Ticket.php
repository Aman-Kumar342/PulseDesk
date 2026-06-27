<?php
namespace App\Models;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
class Ticket extends Model {
    use BelongsToOrganization;
    protected $fillable = ['organization_id','subject','description','status','priority','requester_id','assignee_id'];
    public function requester() { return $this->belongsTo(User::class, 'requester_id'); }
    public function assignee() { return $this->belongsTo(User::class, 'assignee_id'); }
    public function replies() { return $this->hasMany(TicketReply::class); }
    public function tags() { return $this->belongsToMany(Tag::class, 'ticket_tag'); }

    protected static function booted(): void
    {
        static::created(fn($t) => $t->logActivity('created'));
        static::updated(function ($t) {
            if ($t->wasChanged('status'))   $t->logActivity('status_changed', ['to' => $t->status]);
            if ($t->wasChanged('assignee_id')) $t->logActivity('assigned', ['to' => $t->assignee_id]);
        });
    }

    public function activities() { return $this->hasMany(TicketActivity::class)->latest(); }

    public function logActivity(string $event, array $meta = []): void
    {
        TicketActivity::create([
            'organization_id' => $this->organization_id,
            'ticket_id' => $this->id,
            'actor_id' => auth()->id(),
            'event' => $event,
            'meta' => $meta ?: null,
        ]);
    }
}
