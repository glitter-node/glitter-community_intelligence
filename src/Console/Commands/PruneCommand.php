<?php

namespace Modules\Glitter\CommunityIntelligence\Console\Commands;

use Illuminate\Console\Command;
use Modules\Glitter\CommunityIntelligence\Models\AccessLog;
use Modules\Glitter\CommunityIntelligence\Models\Observation;

class PruneCommand extends Command
{
    protected $signature = 'community-intelligence:prune {--observation-days=90} {--access-log-days=180} {--limit=1000} {--dry-run} {--json}';
    protected $description = 'Prune short-lived evidence and access audit records in bounded batches';
    public function handle(): int
    {
        $limit = min(5000, max(1, (int) $this->option('limit')));
        $targets = [
            'observations' => Observation::query()->where('created_at', '<', now()->subDays(max(1, (int) $this->option('observation-days'))))->limit($limit),
            'access_logs' => AccessLog::query()->where('viewed_at', '<', now()->subDays(max(1, (int) $this->option('access-log-days'))))->limit($limit),
        ];
        $result = []; foreach ($targets as $name => $query) $result[$name] = $this->option('dry-run') ? $query->count() : $query->delete();
        $this->line($this->option('json') ? json_encode(['dry_run' => (bool) $this->option('dry-run'), 'affected' => $result], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) : __('glitter-community-intelligence::messages.command.prune_complete', ['count' => array_sum($result)]));
        return self::SUCCESS;
    }
}
