<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use Modules\Glitter\CommunityIntelligence\Enums\ReviewStatus;
use PHPUnit\Framework\TestCase;

class ReviewStatusTest extends TestCase
{
    public function test_supported_states_are_explicit(): void
    {
        self::assertSame(['unreviewed', 'reviewing', 'observing', 'resolved', 'dismissed'], array_column(ReviewStatus::cases(), 'value'));
    }
}
