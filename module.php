<?php

namespace Modules\Glitter\CommunityIntelligence;

use App\Extension\AbstractModule;
use Modules\Glitter\CommunityIntelligence\Database\Seeders\RuleSeeder;

class Module extends AbstractModule
{
    public function getPermissions(): array
    {
        $permission = static fn (string $action, string $ko, string $en, array $roles): array => [
            'action' => $action,
            'name' => ['ko' => $ko, 'en' => $en],
            'description' => ['ko' => $ko, 'en' => $en],
            'type' => 'admin',
            'roles' => $roles,
        ];

        return [
            'name' => ['ko' => '커뮤니티 인텔리전스', 'en' => 'Community Intelligence'],
            'description' => ['ko' => '커뮤니티 운영 신호 접근 권한', 'en' => 'Community operations intelligence access'],
            'categories' => [[
                'identifier' => 'intelligence',
                'resource_route_key' => null,
                'owner_key' => null,
                'name' => ['ko' => '커뮤니티 인텔리전스', 'en' => 'Community Intelligence'],
                'description' => ['ko' => '분석·검토·설정·감사 권한', 'en' => 'Analysis, review, configuration, and audit permissions'],
                'permissions' => [
                    $permission('view', '운영 신호 조회', 'View intelligence', ['admin', 'manager']),
                    $permission('view_sensitive', '민감 근거 조회', 'View sensitive evidence', ['admin']),
                    $permission('review', '운영 검토', 'Review signals', ['admin', 'manager']),
                    $permission('configure', '분석 규칙 설정', 'Configure rules', ['admin']),
                    $permission('audit', '열람 감사 조회', 'View access audit', ['admin']),
                ],
            ]],
        ];
    }

    public function getSeeders(): array
    {
        return [RuleSeeder::class];
    }

    public function getAdminMenus(): array
    {
        return [[
            'name' => ['ko' => '커뮤니티 인텔리전스', 'en' => 'Community Intelligence'],
            'slug' => 'glitter-community_intelligence',
            'url' => null,
            'icon' => 'fas fa-chart-line',
            'order' => 35,
            'children' => [
                [
                    'name' => ['ko' => '운영 브리핑', 'en' => 'Operational Briefing'],
                    'slug' => 'glitter-community_intelligence.briefing',
                    'url' => '/admin/community-intelligence',
                    'order' => 10,
                    'permission' => 'glitter-community_intelligence.intelligence.view',
                ],
                [
                    'name' => ['ko' => '신호', 'en' => 'Signals'],
                    'slug' => 'glitter-community_intelligence.signals',
                    'url' => '/admin/community-intelligence/signals',
                    'order' => 20,
                    'permission' => 'glitter-community_intelligence.intelligence.view',
                ],
                [
                    'name' => ['ko' => '사건', 'en' => 'Incidents'],
                    'slug' => 'glitter-community_intelligence.incidents',
                    'url' => '/admin/community-intelligence/incidents',
                    'order' => 30,
                    'permission' => 'glitter-community_intelligence.intelligence.view',
                ],
                [
                    'name' => ['ko' => '감사 기록', 'en' => 'Audit Records'],
                    'slug' => 'glitter-community_intelligence.audit',
                    'url' => '/admin/community-intelligence/audit',
                    'order' => 40,
                    'permission' => 'glitter-community_intelligence.intelligence.audit',
                ],
                [
                    'name' => ['ko' => '분석 규칙', 'en' => 'Analysis Rules'],
                    'slug' => 'glitter-community_intelligence.settings',
                    'url' => '/admin/community-intelligence/settings',
                    'order' => 50,
                    'permission' => 'glitter-community_intelligence.intelligence.configure',
                ],
            ],
        ]];
    }
}
