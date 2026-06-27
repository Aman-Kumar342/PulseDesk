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
}
