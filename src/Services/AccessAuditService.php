<?php

namespace Modules\Glitter\CommunityIntelligence\Services;

use Modules\Glitter\CommunityIntelligence\Models\AccessLog;

class AccessAuditService
{
    public function record(int $userId, string $type, int $id, string $feature): void
    {
        AccessLog::query()->create(['user_id' => $userId, 'target_type' => $type, 'target_id' => $id, 'feature_identifier' => $feature, 'viewed_at' => now()]);
    }
}
