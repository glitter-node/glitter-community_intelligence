<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class MigrationContractTest extends TestCase
{
    public function test_every_persisted_column_has_a_korean_comment(): void
    {
        $migration = file_get_contents(dirname(__DIR__, 2).'/database/migrations/2026_07_19_000001_create_community_intelligence_tables.php');

        preg_match_all('/\$table->(?!index|unique|timestamps)(?:[a-zA-Z]+)\([^;]+;/', $migration, $matches);

        foreach ($matches[0] as $definition) {
            self::assertStringContainsString('->comment(', $definition, "Missing column comment: {$definition}");
        }
    }

    public function test_indexes_use_explicit_names_within_mysql_limit(): void
    {
        $migration = file_get_contents(dirname(__DIR__, 2).'/database/migrations/2026_07_19_000001_create_community_intelligence_tables.php');

        self::assertStringNotContainsString('->index();', $migration);
        self::assertStringNotContainsString('->unique();', $migration);
        preg_match_all("/(?:index|unique)\\([^;]*?['\"](ci_[a-z0-9_]+)['\"]\\)/", $migration, $matches);

        self::assertNotEmpty($matches[1]);
        foreach ($matches[1] as $name) {
            self::assertLessThanOrEqual(64, strlen($name), "MySQL identifier too long: {$name}");
        }
    }
}
