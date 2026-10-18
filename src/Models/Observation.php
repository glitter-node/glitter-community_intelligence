<?php

namespace Modules\Glitter\CommunityIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    protected $table = 'community_intelligence_observations';
    protected $fillable = ['signal_id', 'kind', 'source_type', 'source_id', 'actor_id', 'occurred_at', 'facts'];
    protected function casts(): array { return ['facts' => 'array', 'occurred_at' => 'datetime']; }
}
