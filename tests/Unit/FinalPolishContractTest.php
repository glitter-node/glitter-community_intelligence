<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class FinalPolishContractTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 2);
    }

    public function test_statistics_have_mutually_exclusive_empty_and_success_conditions(): void
    {
        $layout = json_decode(file_get_contents($this->root.'/resources/layouts/admin/admin_community_intelligence_briefing.json'), true, 512, JSON_THROW_ON_ERROR);
        $stats = $layout['slots']['content'][0]['children'][4]['children'];

        foreach ([0 => 'by_rule', 1 => 'by_priority'] as $index => $field) {
            $empty = $stats[$index]['children'][1];
            $list = $stats[$index]['children'][2];

            self::assertSame("{{Object.keys(briefing?.data?.{$field} ?? {}).length === 0}}", $empty['if']);
            self::assertSame("{{Object.keys(briefing?.data?.{$field} ?? {}).length > 0}}", $list['if']);
        }
        self::assertSame('{{!_dataSourceErrors?.briefing}}', $layout['slots']['content'][0]['children'][4]['if']);
    }

    public function test_priority_zero_values_remain_a_success_list_when_keys_exist(): void
    {
        $layout = file_get_contents($this->root.'/resources/layouts/admin/admin_community_intelligence_briefing.json');

        self::assertStringContainsString("['high', 'medium', 'low'].map", $layout);
        self::assertStringContainsString("briefing?.data?.by_priority?.[level] ?? 0", $layout);
        self::assertStringContainsString("Object.keys(briefing?.data?.by_priority ?? {}).length > 0", $layout);
    }

    public function test_korean_visible_catalog_contains_no_english_domain_terms(): void
    {
        $catalog = json_decode(file_get_contents($this->root.'/resources/lang/ko.json'), true, 512, JSON_THROW_ON_ERROR);
        $values = [];
        array_walk_recursive($catalog, static function ($value) use (&$values): void {
            if (is_string($value)) {
                $values[] = $value;
            }
        });
        $visible = implode("\n", $values);

        foreach (['Signal', 'Incident', 'Review', 'Rule', 'Timeline', 'Audit', 'Evidence', 'Priority', 'Dismissed'] as $term) {
            self::assertDoesNotMatchRegularExpression('/\b'.preg_quote($term, '/').'\b/i', $visible, $term);
        }
    }

    public function test_english_and_korean_catalog_key_structures_match(): void
    {
        $keys = static function (array $catalog): array {
            $result = [];
            $walk = static function (array $node, string $prefix = '') use (&$walk, &$result): void {
                foreach ($node as $key => $value) {
                    $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;
                    if (is_array($value)) {
                        $walk($value, $path);
                    } else {
                        $result[] = $path;
                    }
                }
            };
            $walk($catalog);
            sort($result);
            return $result;
        };

        $ko = json_decode(file_get_contents($this->root.'/resources/lang/ko.json'), true, 512, JSON_THROW_ON_ERROR);
        $en = json_decode(file_get_contents($this->root.'/resources/lang/en.json'), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame($keys($en), $keys($ko));
    }

    public function test_all_layouts_parse_and_never_render_raw_structures(): void
    {
        $layouts = glob($this->root.'/resources/layouts/admin/*.json');
        self::assertCount(7, $layouts);
        foreach ($layouts as $path) {
            json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $raw = file_get_contents($path);
            self::assertStringNotContainsString('JSON.stringify', $raw, basename($path));
            self::assertStringNotContainsString('"name": "Pre"', $raw, basename($path));
            self::assertStringNotContainsString('"text": "[]"', $raw, basename($path));
            self::assertStringNotContainsString('"text": "{}"', $raw, basename($path));
        }
    }
}
