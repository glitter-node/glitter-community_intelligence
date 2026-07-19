<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Feature;

use PHPUnit\Framework\TestCase;

class LifecycleContractTest extends TestCase
{
    public function test_module_declares_internal_lifecycle_artifacts(): void
    {
        $root = dirname(__DIR__, 2);
        self::assertFileExists($root.'/module.php');
        self::assertFileExists($root.'/database/migrations/2026_07_19_000001_create_community_intelligence_tables.php');
        self::assertFileExists($root.'/database/seeders/RuleSeeder.php');
        self::assertFileExists($root.'/src/routes/api.php');
        self::assertFileExists($root.'/resources/routes.json');
    }
}
