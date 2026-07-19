<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Resources;

use App\Http\Resources\BaseApiCollection;

class IncidentCollection extends BaseApiCollection
{
    public $collects = IncidentResource::class;
}
