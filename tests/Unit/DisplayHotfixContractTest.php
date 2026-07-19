<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class DisplayHotfixContractTest extends TestCase
{
    private string $root;
    private array $layout;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 2);
        $this->layout = json_decode(
            file_get_contents($this->root.'/resources/layouts/admin/admin_community_intelligence_settings.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
    }

    public function test_configured_timestamp_and_empty_state_are_mutually_exclusive(): void
    {
        $children = $this->layout['slots']['content'][0]['children'][3]['children'];
        $configured = $children[4];
        $empty = $children[5];

        self::assertSame('{{!!rule.configured_at}}', $configured['if']);
        self::assertSame(
            '$t:glitter-community_intelligence.admin.configured_at|value={{rule.configured_at | datetime}}',
            $configured['text'],
        );
        self::assertSame('{{!rule.configured_at}}', $empty['if']);
        self::assertSame('$t:glitter-community_intelligence.admin.configured_at_empty', $empty['text']);
    }

    public function test_catalogs_define_value_and_empty_messages(): void
    {
        foreach (['ko', 'en'] as $locale) {
            $catalog = json_decode(file_get_contents($this->root.'/resources/lang/'.$locale.'.json'), true, 512, JSON_THROW_ON_ERROR);
            self::assertStringContainsString('{value}', $catalog['admin']['configured_at']);
            self::assertNotSame('', $catalog['admin']['configured_at_empty']);
        }
    }

    public function test_rule_resource_and_layout_share_configured_at_contract(): void
    {
        $resource = file_get_contents($this->root.'/src/Http/Resources/RuleResource.php');
        $layout = file_get_contents($this->root.'/resources/layouts/admin/admin_community_intelligence_settings.json');

        self::assertStringContainsString("'configured_at' => optional(\$this->configured_at)->toIso8601String()", $resource);
        self::assertStringContainsString('rule.configured_at | datetime', $layout);
        self::assertStringNotContainsString('updated_at', $layout);
    }

    public function test_settings_remains_read_only_and_permission_guarded(): void
    {
        $raw = file_get_contents($this->root.'/resources/layouts/admin/admin_community_intelligence_settings.json');
        self::assertSame(['glitter-community_intelligence.intelligence.configure'], $this->layout['permissions']);
        self::assertSame('GET', $this->layout['data_sources'][0]['method']);
        self::assertStringNotContainsString('"method": "PUT"', $raw);
        self::assertStringNotContainsString('Invalid Date', $raw);
        self::assertStringNotContainsString('[object Object]', $raw);
    }

    public function test_every_settings_translation_key_exists_in_both_catalogs(): void
    {
        $raw = file_get_contents($this->root.'/resources/layouts/admin/admin_community_intelligence_settings.json');
        preg_match_all('/\$t:glitter-community_intelligence\.([a-zA-Z0-9_.]+)/', $raw, $matches);

        foreach (['ko', 'en'] as $locale) {
            $catalog = json_decode(file_get_contents($this->root.'/resources/lang/'.$locale.'.json'), true, 512, JSON_THROW_ON_ERROR);
            foreach (array_unique($matches[1]) as $key) {
                $value = $catalog;
                foreach (explode('.', $key) as $segment) {
                    self::assertIsArray($value, $locale.':'.$key);
                    self::assertArrayHasKey($segment, $value, $locale.':'.$key);
                    $value = $value[$segment];
                }
                self::assertIsString($value, $locale.':'.$key);
            }
        }
    }
}
