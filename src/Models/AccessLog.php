<?php

namespace Modules\Glitter\CommunityIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $table = 'community_intelligence_access_logs';
    protected $fillable = ['user_id', 'target_type', 'target_id', 'feature_identifier', 'viewed_at'];
    protected function casts(): array { return ['viewed_at' => 'datetime']; }
}
