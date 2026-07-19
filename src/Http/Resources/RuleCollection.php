<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Resources;

use App\Http\Resources\BaseApiCollection;

class RuleCollection extends BaseApiCollection
{
    public $collects = RuleResource::class;
}
