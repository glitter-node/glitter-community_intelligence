<?php

namespace Modules\Glitter\CommunityIntelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Signal extends Model
{
    protected $table = 'community_intelligence_signals';
    protected $fillable = ['type', 'subject_type', 'subject_id', 'board_id', 'status', 'priority', 'observed_from', 'observed_until', 'rule_identifier', 'rule_version', 'summary_key', 'summary_params', 'explanation_key', 'explanation_params', 'evidence_count', 'deduplication_key', 'detected_at', 'last_evaluated_at'];
    protected function casts(): array { return ['summary_params' => 'array', 'explanation_params' => 'array', 'observed_from' => 'datetime', 'observed_until' => 'datetime', 'detected_at' => 'datetime', 'last_evaluated_at' => 'datetime']; }
    public function observations(): HasMany { return $this->hasMany(Observation::class, 'signal_id'); }
    public function reviews(): HasMany { return $this->hasMany(Review::class, 'signal_id')->orderBy('created_at'); }
}
