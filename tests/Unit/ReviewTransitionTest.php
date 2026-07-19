<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use Modules\Glitter\CommunityIntelligence\Enums\ReviewStatus;
use PHPUnit\Framework\TestCase;

class ReviewTransitionTest extends TestCase
{
    public function test_supported_operational_transitions(): void
    {
        self::assertTrue(ReviewStatus::Unreviewed->canTransitionTo(ReviewStatus::Reviewing));
        self::assertTrue(ReviewStatus::Unreviewed->canTransitionTo(ReviewStatus::Resolved));
        self::assertTrue(ReviewStatus::Reviewing->canTransitionTo(ReviewStatus::Observing));
        self::assertTrue(ReviewStatus::Observing->canTransitionTo(ReviewStatus::Reviewing));
        self::assertTrue(ReviewStatus::Observing->canTransitionTo(ReviewStatus::Dismissed));
    }

    public function test_terminal_and_backward_transitions_are_rejected(): void
    {
        self::assertTrue(ReviewStatus::Resolved->isTerminal());
        self::assertTrue(ReviewStatus::Dismissed->isTerminal());
        self::assertFalse(ReviewStatus::Resolved->canTransitionTo(ReviewStatus::Reviewing));
        self::assertFalse(ReviewStatus::Dismissed->canTransitionTo(ReviewStatus::Observing));
        self::assertFalse(ReviewStatus::Reviewing->canTransitionTo(ReviewStatus::Unreviewed));
        self::assertFalse(ReviewStatus::Reviewing->canTransitionTo(ReviewStatus::Reviewing));
    }
}
