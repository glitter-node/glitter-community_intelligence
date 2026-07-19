<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class LayoutI18nTest extends TestCase
{
    public function test_layouts_are_valid_json_and_visible_text_uses_translation_keys_or_bindings(): void
    {
        foreach (glob(dirname(__DIR__, 2).'/resources/layouts/admin/*.json') as $path) {
            $layout = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            self::assertStringStartsWith('admin_', $layout['layout_name']);
            $json = file_get_contents($path);
            preg_match_all('/"(?:text|title|description)"\s*:\s*"([^"]+)"/', $json, $matches);
            foreach ($matches[1] as $value) self::assertTrue(str_starts_with($value, '$t:') || str_starts_with($value, '{{'), "Hardcoded layout text: $value");
        }
    }
}
