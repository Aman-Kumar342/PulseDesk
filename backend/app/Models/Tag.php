<?php
namespace App\Models;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
class Tag extends Model {
    use BelongsToOrganization;
    protected $fillable = ['organization_id','name'];
    public function tickets() { return $this->belongsToMany(Ticket::class, 'ticket_tag'); }
}
