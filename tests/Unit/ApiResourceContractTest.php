<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ApiResourceContractTest extends TestCase
{
    public function test_admin_controller_does_not_return_models_directly(): void
    {
        $controller = file_get_contents(dirname(__DIR__, 2).'/src/Http/Controllers/Admin/IntelligenceController.php');

        foreach (['IncidentResource', 'RuleResource', 'AccessLogCollection'] as $resource) {
            self::assertStringContainsString($resource, $controller);
        }

    }

    public function test_required_resources_extend_the_core_api_resource(): void
    {
        $root = dirname(__DIR__, 2).'/src/Http/Resources';

        foreach (['IncidentResource.php', 'RuleResource.php', 'AccessLogResource.php'] as $file) {
            self::assertFileExists($root.'/'.$file);
            self::assertStringContainsString('extends BaseApiResource', file_get_contents($root.'/'.$file));
        }
    }
}
