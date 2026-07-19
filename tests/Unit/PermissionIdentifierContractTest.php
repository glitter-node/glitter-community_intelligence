<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class PermissionIdentifierContractTest extends TestCase
{
    private const PREFIX = 'glitter-community_intelligence.intelligence.';

    public function test_module_identifier_is_the_permission_namespace_source_of_truth(): void
    {
        $manifest = json_decode(file_get_contents(dirname(__DIR__, 2).'/module.json'), true, 512, JSON_THROW_ON_ERROR);
        $module = file_get_contents(dirname(__DIR__, 2).'/module.php');

        self::assertSame('glitter-community_intelligence', $manifest['identifier']);
        self::assertStringContainsString(self::PREFIX.'view', $module);
        self::assertStringNotContainsString('glitter-community-intelligence.intelligence.', $module);
    }

    public function test_admin_menu_uses_the_canonical_view_permission(): void
    {
        $module = file_get_contents(dirname(__DIR__, 2).'/module.php');

        self::assertMatchesRegularExpression("/'permission'\s*=>\s*'".preg_quote(self::PREFIX.'view', '/')."'/", $module);
    }

    public function test_all_layouts_use_only_canonical_permission_identifiers(): void
    {
        foreach (glob(dirname(__DIR__, 2).'/resources/layouts/admin/*.json') as $path) {
            $layout = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            foreach ($layout['permissions'] ?? [] as $permission) {
                self::assertStringStartsWith(self::PREFIX, $permission, basename($path));
            }
        }
    }

    public function test_api_routes_use_only_canonical_permission_identifiers(): void
    {
        $routes = file_get_contents(dirname(__DIR__, 2).'/src/routes/api.php');

        self::assertStringNotContainsString('glitter-community-intelligence.intelligence.', $routes);
        foreach (['view', 'view_sensitive', 'review', 'configure', 'audit'] as $action) {
            self::assertStringContainsString(self::PREFIX.$action, $routes);
        }
    }

    public function test_declared_permissions_keep_independent_role_assignments(): void
    {
        $module = file_get_contents(dirname(__DIR__, 2).'/module.php');

        self::assertStringContainsString("\$permission('view', '운영 신호 조회', 'View intelligence', ['admin', 'manager'])", $module);
        self::assertStringContainsString("\$permission('review', '운영 검토', 'Review signals', ['admin', 'manager'])", $module);
        foreach (['view_sensitive', 'configure', 'audit'] as $action) {
            self::assertMatchesRegularExpression("/\\\$permission\\('".preg_quote($action, "/")."',.*\\['admin'\\]\\)/", $module);
        }
    }

    public function test_update_lifecycle_resynchronizes_permissions_and_role_pivots_idempotently(): void
    {
        $root = dirname(__DIR__, 5);
        $manager = file_get_contents($root.'/app/Extension/ModuleManager.php');
        $helper = file_get_contents($root.'/app/Extension/Helpers/ExtensionRoleSyncHelper.php');

        self::assertStringContainsString('$this->syncDeclarativeArtifacts($module)', $manager);
        self::assertStringContainsString('$this->createModulePermissions($module)', $manager);
        self::assertStringContainsString('$this->assignPermissionsToRoles($module)', $manager);
        self::assertStringContainsString('syncAllRoleAssignments', $helper);
        self::assertStringContainsString('$toAttach = array_diff', $helper);
    }

    public function test_permission_docs_use_the_canonical_identifiers(): void
    {
        $docs = file_get_contents(dirname(__DIR__, 2).'/docs/permissions.md');

        self::assertStringNotContainsString('glitter-community-intelligence.intelligence.', $docs);
        foreach (['view', 'view_sensitive', 'review', 'configure', 'audit'] as $action) {
            self::assertStringContainsString(self::PREFIX.$action, $docs);
        }
    }
}
