<?php

namespace Modules\Glitter\CommunityIntelligence\Providers;

use App\Extension\BaseModuleServiceProvider;
use Modules\Glitter\CommunityIntelligence\Adapters\BoardIntelligenceSourceGateway;
use Modules\Glitter\CommunityIntelligence\Console\Commands\AnalyzeCommand;
use Modules\Glitter\CommunityIntelligence\Console\Commands\PruneCommand;
use Modules\Glitter\CommunityIntelligence\Contracts\IntelligenceSourceGatewayInterface;

class CommunityIntelligenceServiceProvider extends BaseModuleServiceProvider
{
    protected string $moduleIdentifier = 'glitter-community_intelligence';
    protected array $repositories = [IntelligenceSourceGatewayInterface::class => BoardIntelligenceSourceGateway::class];
    public function boot(): void { parent::boot(); if ($this->app->runningInConsole()) $this->commands([AnalyzeCommand::class, PruneCommand::class]); }
}
