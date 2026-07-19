<?php

namespace Modules\Glitter\CommunityIntelligence\Enums;

enum SignalType: string
{
    case CommentSurge = 'comment_surge';
    case ReportConcentration = 'report_concentration';
    case StaleReport = 'stale_report';
    case RepeatedConflict = 'repeated_conflict';
}
