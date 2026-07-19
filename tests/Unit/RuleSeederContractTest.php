<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class RuleSeederContractTest extends TestCase
{
    public function test_default_rules_are_idempotent_and_preserve_operator_settings(): void
    {
        $seeder = file_get_contents(dirname(__DIR__, 2).'/database/seeders/RuleSeeder.php');

        foreach (['comment_surge', 'report_concentration', 'stale_report', 'repeated_conflict'] as $identifier) {
            self::assertStringContainsString("'{$identifier}'", $seeder);
        }

        self::assertStringContainsString('firstOrCreate', $seeder);
        self::assertStringNotContainsString('updateOrCreate', $seeder);
        self::assertMatchesRegularExpression("/\['repeated_conflict',\s*false,/", $seeder);
    }
}
