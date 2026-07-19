<?php

namespace Modules\Glitter\CommunityIntelligence\Console\Commands;

use Illuminate\Console\Command;
use Modules\Glitter\CommunityIntelligence\Services\AnalysisService;

class AnalyzeCommand extends Command
{
    protected $signature = 'community-intelligence:analyze {--rule=} {--board=*} {--hours=} {--limit=500} {--dry-run} {--json}';
    protected $description = 'Run bounded community intelligence analysis';
    public function handle(AnalysisService $service): int
    {
        try {
            $result = $service->analyze($this->option('rule') ?: null, array_map('intval', $this->option('board')), min(5000, max(1, (int) $this->option('limit'))), (bool) $this->option('dry-run'), $this->option('hours') ? max(1, min(8760, (int) $this->option('hours'))) : null);
            $output = ['dry_run' => (bool) $this->option('dry-run'), 'created_or_candidate' => $result];
            $this->line($this->option('json') ? json_encode($output, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) : __('glitter-community-intelligence::messages.command.analyze_complete', ['count' => array_sum($result)]));
            return self::SUCCESS;
        } catch (\Throwable $e) { $this->error($e->getMessage()); return self::FAILURE; }
    }
}
