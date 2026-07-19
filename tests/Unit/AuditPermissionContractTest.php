<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class AuditPermissionContractTest extends TestCase
{
    public function test_operational_mutations_are_audited(): void
    {
        $review = file_get_contents(dirname(__DIR__, 2).'/src/Services/ReviewService.php');
        $rule = file_get_contents(dirname(__DIR__, 2).'/src/Services/RuleService.php');

        self::assertStringContainsString('AccessAuditService', $review);
        self::assertStringContainsString('AccessAuditService', $rule);
        self::assertStringContainsString("'rule_updated'", $rule);
    }

    public function test_routes_keep_least_privilege_permissions(): void
    {
        $routes = file_get_contents(dirname(__DIR__, 2).'/src/routes/api.php');

        self::assertStringContainsString('intelligence.view_sensitive', $routes);
        self::assertStringContainsString('intelligence.review', $routes);
        self::assertStringContainsString('intelligence.configure', $routes);
        self::assertStringContainsString('intelligence.audit', $routes);
    }
}
