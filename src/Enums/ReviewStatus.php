<?php

namespace Modules\Glitter\CommunityIntelligence\Enums;

enum ReviewStatus: string
{
    case Unreviewed = 'unreviewed';
    case Reviewing = 'reviewing';
    case Observing = 'observing';
    case Resolved = 'resolved';
    case Dismissed = 'dismissed';

    public function canTransitionTo(self $next): bool
    {
        if ($this === $next || $this->isTerminal() || $next === self::Unreviewed) {
            return false;
        }

        return match ($this) {
            self::Unreviewed => in_array($next, [self::Reviewing, self::Observing, self::Resolved, self::Dismissed], true),
            self::Reviewing => in_array($next, [self::Observing, self::Resolved, self::Dismissed], true),
            self::Observing => in_array($next, [self::Reviewing, self::Resolved, self::Dismissed], true),
            self::Resolved, self::Dismissed => false,
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Resolved, self::Dismissed], true);
    }
}
