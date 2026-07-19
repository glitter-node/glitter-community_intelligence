<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class PrivacyContractTest extends TestCase
{
    public function test_module_persistence_does_not_define_forbidden_tracking_columns(): void
    {
        $migration = file_get_contents(dirname(__DIR__, 2).'/database/migrations/2026_07_19_000001_create_community_intelligence_tables.php');
        foreach (['ip_address', 'user_agent', 'fingerprint', 'session', 'auth_token', 'password', 'content_snapshot'] as $forbidden) {
            self::assertStringNotContainsString("'$forbidden'", $migration);
        }
    }

    public function test_observation_adapter_does_not_select_content_or_ip(): void
    {
        $adapter = file_get_contents(dirname(__DIR__, 2).'/src/Adapters/BoardIntelligenceSourceGateway.php');
        self::assertStringNotContainsString('ip_address', $adapter);
        self::assertStringNotContainsString("'content'", $adapter);
        self::assertStringNotContainsString("'snapshot'", $adapter);
    }
}
