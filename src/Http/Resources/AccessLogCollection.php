<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Resources;

use App\Http\Resources\BaseApiCollection;

class AccessLogCollection extends BaseApiCollection
{
    public $collects = AccessLogResource::class;
}
