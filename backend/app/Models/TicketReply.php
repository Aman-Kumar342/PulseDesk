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
}
