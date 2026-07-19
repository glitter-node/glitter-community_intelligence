<?php

namespace Modules\Glitter\CommunityIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $table = 'community_intelligence_rules';
    protected $fillable = ['identifier', 'version', 'enabled', 'board_scope', 'observation_window_minutes', 'thresholds', 'configured_by', 'configured_at'];
    protected function casts(): array { return ['enabled' => 'boolean', 'board_scope' => 'array', 'thresholds' => 'array', 'configured_at' => 'datetime']; }
}
