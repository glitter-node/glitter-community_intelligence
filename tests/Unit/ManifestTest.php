<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ManifestTest extends TestCase
{
    public function test_manifest_contract_and_naming(): void
    {
        $manifest = json_decode(file_get_contents(dirname(__DIR__, 2).'/module.json'), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('glitter-community_intelligence', $manifest['identifier']);
        self::assertSame('Glitter.kr', $manifest['vendor']);
        self::assertSame('커뮤니티 인텔리전스', $manifest['name']['ko']);
        self::assertSame('Community Intelligence', $manifest['name']['en']);
        self::assertArrayHasKey('sirsoft-board', $manifest['dependencies']['modules']);
    }

    public function test_namespace_conversion_contract_is_reflected_in_composer(): void
    {
        $composer = json_decode(file_get_contents(dirname(__DIR__, 2).'/composer.json'), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('src/', $composer['autoload']['psr-4']['Modules\\Glitter\\CommunityIntelligence\\']);
    }
}
