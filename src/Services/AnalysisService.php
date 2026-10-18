<?php

namespace Modules\Glitter\CommunityIntelligence\Services;

use Carbon\CarbonImmutable;
use Modules\Glitter\CommunityIntelligence\Contracts\IntelligenceSourceGatewayInterface;
use Modules\Glitter\CommunityIntelligence\Models\Rule;

class AnalysisService
{
    public function __construct(private IntelligenceSourceGatewayInterface $sources, private SignalWriter $writer) {}

    public function analyze(?string $onlyRule = null, array $boardIds = [], int $limit = 500, bool $dryRun = false, ?int $hours = null): array
    {
        $counts = [];
        $rules = Rule::query()->where('enabled', true)->when($onlyRule, fn ($q) => $q->where('identifier', $onlyRule))->get();
        foreach ($rules as $rule) {
            $effectiveBoards = $boardIds !== [] ? $boardIds : array_values(array_filter($rule->board_scope ?? [], 'is_int'));
            $until = CarbonImmutable::now();
            $from = $until->subMinutes($hours ? $hours * 60 : $rule->observation_window_minutes);
            $counts[$rule->identifier] = match ($rule->identifier) {
                'comment_surge' => $this->commentSurge($rule, $from, $until, $effectiveBoards, $limit, $dryRun),
                'report_concentration' => $this->reportConcentration($rule, $from, $until, $effectiveBoards, $limit, $dryRun),
                'stale_report' => $this->staleReport($rule, $until, $effectiveBoards, $limit, $dryRun),
                default => 0,
            };
        }
        return $counts;
    }

    private function commentSurge(Rule $rule, $from, $until, array $boards, int $limit, bool $dryRun): int
    {
        $threshold = max(1, (int) ($rule->thresholds['minimum_comments'] ?? 10)); $created = 0;
        foreach ($this->sources->commentSurges($from, $until, $threshold, $boards, $limit) as $row) {
            $created += (int) $this->writer->write($this->data($rule, 'comment_surge', 'post', (int) $row->post_id, (int) $row->board_id, $from, $until, (int) $row->observed_count, ['count' => (int) $row->observed_count, 'threshold' => $threshold]), ['observed_comment_count' => (int) $row->observed_count, 'threshold' => $threshold, 'comparison' => 'absolute_window_count'], $dryRun);
        } return $created;
    }

    private function reportConcentration(Rule $rule, $from, $until, array $boards, int $limit, bool $dryRun): int
    {
        $reports = max(1, (int) ($rule->thresholds['minimum_reports'] ?? 3)); $reporters = max(1, (int) ($rule->thresholds['minimum_distinct_reporters'] ?? 2)); $created = 0;
        foreach ($this->sources->reportConcentrations($from, $until, $reports, $reporters, $boards, $limit) as $row) {
            $facts = ['report_count' => (int) $row->report_count, 'distinct_reporter_count' => (int) $row->distinct_reporter_count, 'report_threshold' => $reports, 'reporter_threshold' => $reporters];
            $created += (int) $this->writer->write($this->data($rule, 'report_concentration', (string) $row->target_type, (int) $row->target_id, (int) $row->board_id, $from, $until, (int) $row->report_count, $facts), $facts, $dryRun);
        } return $created;
    }

    private function staleReport(Rule $rule, $until, array $boards, int $limit, bool $dryRun): int
    {
        $hours = max(1, (int) ($rule->thresholds['maximum_pending_hours'] ?? 72)); $cutoff = $until->subHours($hours); $created = 0;
        foreach ($this->sources->staleReports($cutoff, $boards, $limit) as $row) {
            $elapsed = (int) $row->created_at->diffInHours($until); $facts = ['report_id' => (int) $row->id, 'target_type' => (string) $row->target_type, 'target_id' => (int) $row->target_id, 'status' => (string) $row->status, 'created_at' => $row->created_at->toIso8601String(), 'elapsed_hours' => $elapsed, 'threshold_hours' => $hours];
            $created += (int) $this->writer->write($this->data($rule, 'stale_report', 'report', (int) $row->id, (int) $row->board_id, $row->created_at, $until, 1, $facts), $facts, $dryRun);
        } return $created;
    }

    private function data(Rule $rule, string $type, string $subjectType, int $subjectId, ?int $boardId, $from, $until, int $count, array $params): array
    {
        $window = $from->format('YmdHi').':'.$until->format('YmdHi');
        return ['type' => $type, 'subject_type' => $subjectType, 'subject_id' => $subjectId, 'board_id' => $boardId, 'status' => 'unreviewed', 'priority' => min(100, 40 + $count), 'observed_from' => $from, 'observed_until' => $until, 'rule_identifier' => $rule->identifier, 'rule_version' => $rule->version, 'summary_key' => 'glitter-community_intelligence::messages.signal.'.$type.'.summary', 'summary_params' => $params, 'explanation_key' => 'glitter-community_intelligence::messages.signal.'.$type.'.explanation', 'explanation_params' => $params, 'evidence_count' => $count, 'deduplication_key' => hash('sha256', implode('|', [$type, $subjectType, $subjectId, $rule->version, $window])), 'detected_at' => now(), 'last_evaluated_at' => now()];
    }
}
