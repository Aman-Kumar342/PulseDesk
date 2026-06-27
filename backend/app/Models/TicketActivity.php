<?php
namespace App\Models;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
class TicketActivity extends Model {
    use BelongsToOrganization;
    protected $fillable = ['organization_id','ticket_id','actor_id','event','meta'];
    protected $casts = ['meta' => 'array'];
    public function ticket() { return $this->belongsTo(Ticket::class); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
