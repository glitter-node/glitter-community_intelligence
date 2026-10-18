<?php
return [
    'menu' => [
        'root' => 'Community Intelligence',
        'briefing' => 'Operational Briefing',
        'signals' => 'Signals',
        'incidents' => 'Incidents',
        'audit' => 'Audit Records',
        'settings' => 'Analysis Rules',
    ],
    'briefing' => ['success' => 'Operations briefing retrieved.'],
    'signal' => [
        'fetch_success' => 'Signals retrieved.',
        'comment_surge' => ['summary' => 'Comment activity reached the configured observation threshold.', 'explanation' => 'The comment count in the observation window met the configured threshold.'],
        'report_concentration' => ['summary' => 'Report activity was concentrated on one subject.', 'explanation' => 'Report and distinct reporter counts met their configured thresholds.'],
        'stale_report' => ['summary' => 'Report review exceeded the configured delay.', 'explanation' => 'The report remains pending and exceeded the configured delay threshold.'],
    ],
    'review' => ['success' => 'Review history appended.'],
    'incident' => ['fetch_success' => 'Incident timeline retrieved.', 'title' => ':type incident :id', 'signal_created' => 'An intelligence signal was created.', 'review_changed' => 'Operator review status changed.'],
    'rule' => ['fetch_success' => 'Rules retrieved.', 'update_success' => 'Rule updated.'],
    'audit' => ['fetch_success' => 'Access audit retrieved.'],
    'command' => ['analyze_complete' => ':count analysis candidates processed.', 'prune_complete' => ':count expired records processed.'],
];
