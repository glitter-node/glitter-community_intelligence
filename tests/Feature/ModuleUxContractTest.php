<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Feature;

use Modules\Glitter\CommunityIntelligence\Module;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ModuleUxContractTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = dirname(__DIR__, 2);
        require_once $this->root.'/module.php';
    }

    public function test_release_metadata_is_consistent(): void
    {
        $manifest = $this->json('module.json');
        $composer = $this->json('composer.json');
        $readme = file_get_contents($this->root.'/README.md');

        self::assertSame('0.3.4', $manifest['version']);
        self::assertSame('0.3.4', $composer['version']);
        self::assertStringContainsString('- Version: `0.3.4`', $readme);
        self::assertSame('glitter-community_intelligence', $manifest['identifier']);
        self::assertSame('Glitter.kr', $manifest['vendor']);
        self::assertSame('>=7.0.0', $manifest['g7_version']);
        self::assertFileDoesNotExist($this->root.'/package.json');
    }

    public function test_manifest_exposes_localized_display_names_instead_of_internal_identifiers(): void
    {
        $manifest = $this->json('module.json');

        self::assertSame('glitter-community_intelligence', $manifest['identifier']);
        self::assertSame('커뮤니티 인텔리전스', $manifest['name']['ko']);
        self::assertSame('Community Intelligence', $manifest['name']['en']);
        self::assertNotContains($manifest['identifier'], $manifest['name']);
        self::assertNotContains('glitter-community-intelligence', $manifest['name']);
    }

    public function test_admin_menu_navigation_contract(): void
    {
        $menus = (new Module)->getAdminMenus();

        self::assertCount(1, $menus);
        $parent = $menus[0];
        self::assertSame('glitter-community_intelligence', $parent['slug']);
        self::assertNull($parent['url']);
        self::assertSame('fas fa-chart-line', $parent['icon']);
        self::assertSame(35, $parent['order']);
        self::assertArrayNotHasKey('permission', $parent);
        self::assertCount(5, $parent['children']);

        $children = $parent['children'];
        self::assertSame([
            'glitter-community_intelligence.briefing',
            'glitter-community_intelligence.signals',
            'glitter-community_intelligence.incidents',
            'glitter-community_intelligence.audit',
            'glitter-community_intelligence.settings',
        ], array_column($children, 'slug'));
        self::assertSame([
            '/admin/community-intelligence',
            '/admin/community-intelligence/signals',
            '/admin/community-intelligence/incidents',
            '/admin/community-intelligence/audit',
            '/admin/community-intelligence/settings',
        ], array_column($children, 'url'));
        self::assertSame([10, 20, 30, 40, 50], array_column($children, 'order'));
        self::assertSame([
            'glitter-community_intelligence.intelligence.view',
            'glitter-community_intelligence.intelligence.view',
            'glitter-community_intelligence.intelligence.view',
            'glitter-community_intelligence.intelligence.audit',
            'glitter-community_intelligence.intelligence.configure',
        ], array_column($children, 'permission'));
        self::assertCount(5, array_unique(array_column($children, 'slug')));
        self::assertCount(5, array_unique(array_column($children, 'url')));
    }

    public function test_menu_translations_are_complete_and_safe_for_sidebar_output(): void
    {
        $expected = [
            'ko' => ['커뮤니티 인텔리전스', '운영 브리핑', '신호', '사건', '감사 기록', '분석 규칙'],
            'en' => ['Community Intelligence', 'Operational Briefing', 'Signals', 'Incidents', 'Audit Records', 'Analysis Rules'],
        ];
        $menus = (new Module)->getAdminMenus();
        $nodes = [$menus[0], ...$menus[0]['children']];

        foreach ($expected as $locale => $labels) {
            $messages = require $this->root."/src/lang/{$locale}/messages.php";
            self::assertSame($labels, array_values($messages['menu']));
            self::assertSame($labels, array_column(array_column($nodes, 'name'), $locale));
        }

        foreach ($nodes as $node) {
            foreach ($node['name'] as $name) {
                self::assertStringNotContainsString('::', $name);
                self::assertStringNotContainsString('$t:', $name);
            }
        }
    }

    public function test_existing_layout_api_permission_command_and_database_contracts_remain(): void
    {
        $layouts = glob($this->root.'/resources/layouts/admin/*.json');
        self::assertIsArray($layouts);
        self::assertCount(7, $layouts);

        $routes = file_get_contents($this->root.'/src/routes/api.php');
        preg_match_all('/Route::(get|post|put|patch|delete)\s*\(/', $routes, $apiMatches);
        self::assertCount(10, $apiMatches[0]);
        self::assertStringContainsString("middleware(['auth:sanctum'])", $routes);

        $permissionConfig = (new Module)->getPermissions();
        self::assertSame(
            ['view', 'view_sensitive', 'review', 'configure', 'audit'],
            array_column($permissionConfig['categories'][0]['permissions'], 'action')
        );

        $commands = glob($this->root.'/src/Console/Commands/*Command.php');
        self::assertIsArray($commands);
        self::assertCount(2, $commands);
        self::assertStringContainsString('community-intelligence:analyze', file_get_contents($this->root.'/src/Console/Commands/AnalyzeCommand.php'));
        self::assertStringContainsString('community-intelligence:prune', file_get_contents($this->root.'/src/Console/Commands/PruneCommand.php'));

        $migrations = glob($this->root.'/database/migrations/*.php');
        self::assertIsArray($migrations);
        self::assertCount(1, $migrations);
        self::assertFileExists($this->root.'/database/seeders/RuleSeeder.php');
        preg_match_all("/\['(?:comment_surge|report_concentration|stale_report|repeated_conflict)'/", file_get_contents($this->root.'/database/seeders/RuleSeeder.php'), $ruleMatches);
        self::assertCount(4, $ruleMatches[0]);

        self::assertGreaterThanOrEqual(18, $this->countPhpFiles($this->root.'/tests/Unit'));
    }

    private function json(string $path): array
    {
        return json_decode(file_get_contents($this->root.'/'.$path), true, 512, JSON_THROW_ON_ERROR);
    }

    private function countPhpFiles(string $directory): int
    {
        $count = 0;
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $count++;
            }
        }

        return $count;
    }
}
