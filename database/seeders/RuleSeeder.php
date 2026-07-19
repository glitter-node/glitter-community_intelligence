<?php

namespace Modules\Glitter\CommunityIntelligence\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Glitter\CommunityIntelligence\Models\Rule;

class RuleSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['comment_surge', true, 60, ['minimum_comments' => 10]],
            ['report_concentration', true, 1440, ['minimum_reports' => 3, 'minimum_distinct_reporters' => 2]],
            ['stale_report', true, 4320, ['maximum_pending_hours' => 72]],
            ['repeated_conflict', false, 10080, ['minimum_shared_threads' => 3]],
        ];
        foreach ($defaults as [$identifier, $enabled, $window, $thresholds]) {
            Rule::query()->firstOrCreate(['identifier' => $identifier], [
                'version' => 1, 'enabled' => $enabled, 'board_scope' => [],
                'observation_window_minutes' => $window, 'thresholds' => $thresholds,
            ]);
        }
    }
}
