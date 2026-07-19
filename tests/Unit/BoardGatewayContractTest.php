<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class BoardGatewayContractTest extends TestCase
{
    public function test_raw_aggregates_do_not_hardcode_unprefixed_table_names(): void
    {
        $gateway = file_get_contents(dirname(__DIR__, 2).'/src/Adapters/BoardIntelligenceSourceGateway.php');

        self::assertStringNotContainsString("COUNT(boards_report_logs.id)", $gateway);
        self::assertStringNotContainsString("COUNT(DISTINCT boards_report_logs.reporter_id)", $gateway);
        self::assertStringContainsString("COUNT(*) AS report_count", $gateway);
        self::assertStringContainsString("COUNT(DISTINCT reporter_id) AS distinct_reporter_count", $gateway);
    }
}
