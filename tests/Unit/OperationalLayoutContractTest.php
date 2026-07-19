<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class OperationalLayoutContractTest extends TestCase
{
    public function test_operational_routes_cover_signal_incident_timeline_and_audit(): void
    {
        $routes = json_decode(file_get_contents(dirname(__DIR__, 2).'/resources/routes.json'), true, 512, JSON_THROW_ON_ERROR);
        $paths = array_column($routes['routes'], 'path');

        self::assertContains('*/admin/community-intelligence/signals/:id', $paths);
        self::assertContains('*/admin/community-intelligence/incidents', $paths);
        self::assertContains('*/admin/community-intelligence/incidents/:id', $paths);
        self::assertContains('*/admin/community-intelligence/audit', $paths);
    }

    public function test_signal_detail_exposes_review_success_and_error_states(): void
    {
        $layout = file_get_contents(dirname(__DIR__, 2).'/resources/layouts/admin/admin_community_intelligence_signal_detail.json');

        self::assertStringContainsString('refetchDataSource', $layout);
        self::assertStringContainsString('review_success', $layout);
        self::assertStringContainsString('review_error', $layout);
        self::assertStringContainsString('_dataSourceErrors?.signal', $layout);
    }

    public function test_all_operational_layouts_have_loading_empty_or_error_contracts(): void
    {
        foreach (glob(dirname(__DIR__, 2).'/resources/layouts/admin/*.json') as $path) {
            $layout = file_get_contents($path);
            self::assertStringContainsString('transition_overlay', $layout, basename($path));
            self::assertTrue(
                str_contains($layout, '_dataSourceErrors') || str_contains($layout, 'review_error'),
                basename($path).' lacks an error state'
            );
        }
    }
}
