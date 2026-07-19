<?php

namespace Modules\Glitter\CommunityIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $timestamps = false;
    protected $table = 'community_intelligence_reviews';
    protected $fillable = ['signal_id', 'reviewer_id', 'from_status', 'to_status', 'reason_code', 'note', 'created_at'];
    protected function casts(): array { return ['created_at' => 'datetime']; }
}
