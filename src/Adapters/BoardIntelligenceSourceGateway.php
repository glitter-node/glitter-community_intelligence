<?php

namespace Modules\Glitter\CommunityIntelligence\Adapters;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Modules\Glitter\CommunityIntelligence\Contracts\IntelligenceSourceGatewayInterface;
use Modules\Sirsoft\Board\Models\Comment;
use Modules\Sirsoft\Board\Models\Report;
use Modules\Sirsoft\Board\Models\ReportLog;

/** Aggregate adapter isolated from the domain because board repositories expose no bounded aggregate contract. */
class BoardIntelligenceSourceGateway implements IntelligenceSourceGatewayInterface
{
    public function commentSurges(CarbonInterface $from, CarbonInterface $until, int $threshold, array $boardIds, int $limit): iterable
    {
        return Comment::query()->select(['board_id', 'post_id'])
            ->selectRaw('COUNT(*) AS observed_count')
            ->whereBetween('created_at', [$from, $until])
            ->when($boardIds !== [], fn ($query) => $query->whereIn('board_id', $boardIds))
            ->groupBy('board_id', 'post_id')->havingRaw('COUNT(*) >= ?', [$threshold])
            ->orderByDesc('observed_count')->limit($limit)->get();
    }

    public function reportConcentrations(CarbonInterface $from, CarbonInterface $until, int $reportThreshold, int $reporterThreshold, array $boardIds, int $limit): iterable
    {
        return ReportLog::query()
            ->join('boards_reports', 'boards_reports.id', '=', 'boards_report_logs.report_id')
            ->select(['boards_reports.id AS report_id', 'boards_reports.board_id', 'boards_reports.target_type', 'boards_reports.target_id'])
            ->selectRaw('COUNT(*) AS report_count')
            ->selectRaw('COUNT(DISTINCT reporter_id) AS distinct_reporter_count')
            ->whereBetween('boards_report_logs.created_at', [$from, $until])
            ->whereNull('boards_reports.deleted_at')
            ->when($boardIds !== [], fn ($query) => $query->whereIn('boards_reports.board_id', $boardIds))
            ->groupBy('boards_reports.id', 'boards_reports.board_id', 'boards_reports.target_type', 'boards_reports.target_id')
            ->havingRaw('COUNT(*) >= ?', [$reportThreshold])
            ->havingRaw('COUNT(DISTINCT reporter_id) >= ?', [$reporterThreshold])
            ->orderByDesc('report_count')->limit($limit)->get();
    }

    public function staleReports(CarbonInterface $cutoff, array $boardIds, int $limit): iterable
    {
        return Report::query()->select(['id', 'board_id', 'target_type', 'target_id', 'status', 'created_at'])
            ->whereIn('status', ['pending', 'review'])->where('created_at', '<=', $cutoff)
            ->when($boardIds !== [], fn ($query) => $query->whereIn('board_id', $boardIds))
            ->oldest('created_at')->limit($limit)->get();
    }
}
