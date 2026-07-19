<?php

namespace Modules\Glitter\CommunityIntelligence\Services;

use Illuminate\Support\Facades\DB;
use Modules\Glitter\CommunityIntelligence\Models\Rule;

class RuleService
{
    public function __construct(private AccessAuditService $audit) {}

    public function update(Rule $rule, array $data, int $userId): Rule
    {
        return DB::transaction(function () use ($rule, $data, $userId): Rule {
            $comparable = array_intersect_key($data, array_flip(['enabled', 'board_scope', 'observation_window_minutes', 'thresholds']));
            foreach ($comparable as $key => $value) {
                if ($rule->{$key} !== $value) {
                    $data['version'] = $rule->version + 1;
                    break;
                }
            }
            $rule->update([...$data, 'configured_by' => $userId, 'configured_at' => now()]);
            $this->audit->record($userId, 'rule', (int) $rule->id, 'rule_updated');

            return $rule->refresh();
        });
    }
}
