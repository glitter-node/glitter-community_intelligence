<?php

namespace Modules\Glitter\CommunityIntelligence\Contracts;

use Carbon\CarbonInterface;

interface IntelligenceSourceGatewayInterface
{
    public function commentSurges(CarbonInterface $from, CarbonInterface $until, int $threshold, array $boardIds, int $limit): iterable;
    public function reportConcentrations(CarbonInterface $from, CarbonInterface $until, int $reportThreshold, int $reporterThreshold, array $boardIds, int $limit): iterable;
    public function staleReports(CarbonInterface $cutoff, array $boardIds, int $limit): iterable;
}
