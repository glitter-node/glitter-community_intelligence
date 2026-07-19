<?php

namespace Modules\Glitter\CommunityIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentEvent extends Model
{
    protected $table = 'community_intelligence_incident_events';
    protected $fillable = ['incident_id', 'event_type', 'source_type', 'source_id', 'actor_id', 'occurred_at', 'summary_key', 'metadata', 'idempotency_key'];
    protected function casts(): array { return ['metadata' => 'array', 'occurred_at' => 'datetime']; }
}
