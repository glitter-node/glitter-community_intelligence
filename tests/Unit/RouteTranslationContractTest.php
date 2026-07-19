<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\TestCase;

class RouteTranslationContractTest extends TestCase
{
    private const IDENTIFIER = 'glitter-community_intelligence';
    private const API_PREFIX = '/api/modules/glitter-community_intelligence/';
    private const TRANSLATION_PREFIX = 'glitter-community_intelligence.';

    private static string $moduleRoot;

    public static function setUpBeforeClass(): void
    {
        self::$moduleRoot = dirname(__DIR__, 2);
        if (! app()->bound(Kernel::class)) {
            $app = require dirname(__DIR__, 5).'/bootstrap/app.php';
            $app->make(Kernel::class)->bootstrap();
        }
    }

    public function test_manifest_identifier_is_route_and_translation_source_of_truth(): void
    {
        $manifest = $this->json(self::$moduleRoot.'/module.json');
        self::assertSame(self::IDENTIFIER, $manifest['identifier']);
    }

    public function test_briefing_layout_endpoint_matches_registered_route(): void
    {
        $layout = $this->json(self::$moduleRoot.'/resources/layouts/admin/admin_community_intelligence_briefing.json');
        self::assertSame(self::API_PREFIX.'admin/briefing', $layout['data_sources'][0]['endpoint']);
        self::assertTrue($this->routeExists('GET', self::API_PREFIX.'admin/briefing'));
    }

    public function test_every_layout_get_endpoint_matches_a_registered_route(): void
    {
        foreach ($this->layoutRequests() as [$file, $method, $endpoint]) {
            if ($method !== 'GET') {
                continue;
            }
            self::assertTrue($this->routeExists($method, $endpoint), $file.': '.$endpoint);
        }
    }

    public function test_every_layout_write_endpoint_matches_a_registered_route(): void
    {
        foreach ($this->layoutRequests() as [$file, $method, $endpoint]) {
            if ($method === 'GET') {
                continue;
            }
            self::assertTrue($this->routeExists($method, $endpoint), $file.': '.$method.' '.$endpoint);
        }
    }

    public function test_layout_endpoints_do_not_duplicate_the_automatic_module_prefix(): void
    {
        foreach ($this->layoutRequests() as [$file, , $endpoint]) {
            self::assertSame(1, substr_count($endpoint, '/api/modules/'), $file.': '.$endpoint);
        }
    }

    public function test_layout_endpoints_do_not_mix_hyphen_and_underscore_module_segments(): void
    {
        foreach ($this->layoutRequests() as [$file, , $endpoint]) {
            self::assertStringStartsWith(self::API_PREFIX, $endpoint, $file);
            self::assertStringNotContainsString('/glitter-community-intelligence/', $endpoint, $file);
        }
    }

    public function test_korean_catalog_contains_every_layout_translation_key(): void
    {
        $this->assertCatalogContainsAllLayoutKeys('ko');
    }

    public function test_english_catalog_contains_every_layout_translation_key(): void
    {
        $this->assertCatalogContainsAllLayoutKeys('en');
    }

    public function test_routes_and_layouts_use_only_the_served_translation_namespace(): void
    {
        foreach ($this->translationKeys() as [$file, $key]) {
            self::assertStringStartsWith(self::TRANSLATION_PREFIX, $key, $file.': '.$key);
            self::assertStringNotContainsString('glitter-community-intelligence.', $key, $file.': '.$key);
        }
    }

    public function test_translation_keys_resolve_in_both_catalogs_without_raw_fallback(): void
    {
        foreach (['ko', 'en'] as $locale) {
            $catalog = $this->json(self::$moduleRoot.'/resources/lang/'.$locale.'.json');
            foreach ($this->translationKeys() as [$file, $key]) {
                $localKey = substr($key, strlen(self::TRANSLATION_PREFIX));
                self::assertNotNull(data_get($catalog, $localKey), $locale.' '.$file.': '.$key);
                self::assertNotSame($key, data_get($catalog, $localKey));
            }
        }
    }

    public function test_briefing_error_is_not_rendered_as_successful_zero_data(): void
    {
        $layout = $this->json(self::$moduleRoot.'/resources/layouts/admin/admin_community_intelligence_briefing.json');
        self::assertArrayNotHasKey('fallback', $layout['data_sources'][0]);
        $encoded = json_encode($layout, JSON_THROW_ON_ERROR);
        self::assertStringContainsString('{{!_dataSourceErrors?.briefing}}', $encoded);
        self::assertStringContainsString('{{_dataSourceErrors?.briefing}}', $encoded);
    }

    private function assertCatalogContainsAllLayoutKeys(string $locale): void
    {
        $catalog = $this->json(self::$moduleRoot.'/resources/lang/'.$locale.'.json');
        foreach ($this->translationKeys() as [$file, $key]) {
            self::assertStringStartsWith(self::TRANSLATION_PREFIX, $key, $file);
            self::assertNotNull(data_get($catalog, substr($key, strlen(self::TRANSLATION_PREFIX))), $file.': '.$key);
        }
    }

    private function routeExists(string $method, string $endpoint): bool
    {
        $expected = ltrim(preg_replace('/\{\{route\.([a-zA-Z_][a-zA-Z0-9_]*)\}\}/', '{$1}', $endpoint), '/');
        foreach (Route::getRoutes() as $route) {
            if ($route->uri() === $expected && in_array($method, $route->methods(), true)) {
                return true;
            }
        }
        return false;
    }

    private function layoutRequests(): array
    {
        $requests = [];
        foreach (glob(self::$moduleRoot.'/resources/layouts/admin/*.json') as $path) {
            $layout = $this->json($path);
            $this->walk($layout, function (array $node) use (&$requests, $path): void {
                $endpoint = $node['endpoint'] ?? $node['target'] ?? null;
                if (! is_string($endpoint) || ! str_starts_with($endpoint, '/api/modules/')) {
                    return;
                }
                $method = strtoupper((string) ($node['method'] ?? data_get($node, 'params.method', 'GET')));
                $requests[] = [basename($path), $method, $endpoint];
            });
        }
        return $requests;
    }

    private function translationKeys(): array
    {
        $keys = [];
        $paths = array_merge(
            glob(self::$moduleRoot.'/resources/layouts/admin/*.json'),
            [self::$moduleRoot.'/resources/routes.json']
        );
        foreach ($paths as $path) {
            $this->walk($this->json($path), function (array $node) use (&$keys, $path): void {
                foreach ($node as $value) {
                    if (! is_string($value)) {
                        continue;
                    }
                    if (preg_match_all('/\$t:([a-zA-Z0-9_.-]+)/', $value, $matches)) {
                        foreach ($matches[1] as $key) {
                            $keys[] = [basename($path), $key];
                        }
                    }
                }
            });
        }
        return $keys;
    }

    private function walk(array $value, callable $callback): void
    {
        $callback($value);
        foreach ($value as $child) {
            if (is_array($child)) {
                $this->walk($child, $callback);
            }
        }
    }

    private function json(string $path): array
    {
        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }
}
