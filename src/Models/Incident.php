<?php

namespace Modules\Glitter\CommunityIntelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    protected $table = 'community_intelligence_incidents';
    protected $fillable = ['type', 'primary_subject_type', 'primary_subject_id', 'board_id', 'title_key', 'title_params', 'status', 'started_at', 'last_activity_at'];
    protected function casts(): array { return ['title_params' => 'array', 'started_at' => 'datetime', 'last_activity_at' => 'datetime']; }
    public function events(): HasMany { return $this->hasMany(IncidentEvent::class, 'incident_id')->orderBy('occurred_at')->orderBy('id'); }
}
