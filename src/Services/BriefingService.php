<?php

namespace Modules\Glitter\CommunityIntelligence\Services;

use Carbon\CarbonImmutable;
use Modules\Glitter\CommunityIntelligence\Models\Incident;
use Modules\Glitter\CommunityIntelligence\Models\Review;
use Modules\Glitter\CommunityIntelligence\Models\Signal;

class BriefingService
{
    public function get(int $hours = 24): array
    {
        $from = CarbonImmutable::now()->subHours($hours);
        $recentSignals = Signal::query()->where('detected_at', '>=', $from);

        return [
            'period' => ['from' => $from->toIso8601String(), 'until' => now()->toIso8601String(), 'hours' => $hours],
            'new_signal_count' => (clone $recentSignals)->count(),
            'unreviewed_count' => Signal::query()->where('status', 'unreviewed')->count(),
            'open_incident_count' => Incident::query()->where('status', '!=', 'closed')->count(),
            'by_type' => (clone $recentSignals)->selectRaw('type, COUNT(*) AS aggregate')->groupBy('type')->pluck('aggregate', 'type'),
            'by_rule' => (clone $recentSignals)->selectRaw('rule_identifier, COUNT(*) AS aggregate')->groupBy('rule_identifier')->pluck('aggregate', 'rule_identifier'),
            'by_priority' => (clone $recentSignals)
                ->selectRaw("CASE WHEN priority >= 75 THEN 'high' WHEN priority >= 50 THEN 'medium' ELSE 'low' END AS priority_band, COUNT(*) AS aggregate")
                ->groupBy('priority_band')
                ->pluck('aggregate', 'priority_band'),
            'priority_signals' => Signal::query()->where('status', 'unreviewed')->where('detected_at', '>=', $from)->orderByDesc('priority')->limit(10)->get(),
            'recent_incidents' => Incident::query()->where('last_activity_at', '>=', $from)->latest('last_activity_at')->limit(10)->get(),
            'recent_reviews' => Review::query()->where('created_at', '>=', $from)->latest('created_at')->limit(10)->get(),
        ];
    }
}
