<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use App\Helpers\PermissionHelper;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PermissionPolicyTest extends TestCase
{
    private const PREFIX = 'glitter-community_intelligence.intelligence.';

    public static function setUpBeforeClass(): void
    {
        if (! app()->bound(Kernel::class)) {
            $app = require dirname(__DIR__, 5).'/bootstrap/app.php';
            $app->make(Kernel::class)->bootstrap();
        }
    }

    public function test_user_without_permission_is_denied_view(): void
    {
        self::assertFalse(PermissionHelper::check(self::PREFIX.'view', $this->userWithPermissions([])));
    }

    public function test_view_only_user_is_allowed_view(): void
    {
        self::assertTrue(PermissionHelper::check(self::PREFIX.'view', $this->userWithPermissions([self::PREFIX.'view'])));
    }

    #[DataProvider('privilegedActions')]
    public function test_view_only_user_is_denied_independent_privileged_actions(string $action): void
    {
        self::assertFalse(
            PermissionHelper::check(self::PREFIX.$action, $this->userWithPermissions([self::PREFIX.'view']))
        );
    }

    public static function privilegedActions(): array
    {
        return [
            'view_sensitive' => ['view_sensitive'],
            'review' => ['review'],
            'configure' => ['configure'],
            'audit' => ['audit'],
        ];
    }

    private function userWithPermissions(array $permissions): User
    {
        $user = new class extends User
        {
            public array $allowedPermissions = [];

            public function hasPermission(string $permission, ?\App\Enums\PermissionType $type = null): bool
            {
                return in_array($permission, $this->allowedPermissions, true);
            }
        };
        $user->allowedPermissions = $permissions;

        return $user;
    }
}
