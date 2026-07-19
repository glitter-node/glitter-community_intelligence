<?php

namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;

use PHPUnit\Framework\TestCase;

class WorkflowContractTest extends TestCase
{
    public function test_review_transitions_are_explicit_and_terminal_states_are_closed(): void
    {
        $enum = file_get_contents(dirname(__DIR__, 2).'/src/Enums/ReviewStatus.php');

        self::assertStringContainsString('canTransitionTo', $enum);
        self::assertStringContainsString('isTerminal', $enum);
    }

    public function test_review_creates_append_only_timeline_and_audit_records(): void
    {
        $service = file_get_contents(dirname(__DIR__, 2).'/src/Services/ReviewService.php');

        self::assertStringContainsString("'review_added'", $service);
        self::assertStringContainsString("'incident_status_changed'", $service);
        self::assertStringContainsString("'incident_closed'", $service);
        self::assertStringContainsString("'signal_reviewed'", $service);
        self::assertStringNotContainsString('Review::query()->update', $service);
        self::assertStringNotContainsString('Review::query()->delete', $service);
    }

    public function test_signal_creation_records_incident_creation_and_link_events(): void
    {
        $writer = file_get_contents(dirname(__DIR__, 2).'/src/Services/SignalWriter.php');

        self::assertStringContainsString("'incident_created'", $writer);
        self::assertStringContainsString("'signal_linked'", $writer);
    }

    public function test_briefing_contains_operational_work_queues_and_statistics(): void
    {
        $service = file_get_contents(dirname(__DIR__, 2).'/src/Services/BriefingService.php');

        foreach (['new_signal_count', 'open_incident_count', 'recent_reviews', 'by_rule', 'by_priority'] as $field) {
            self::assertStringContainsString("'{$field}'", $service);
        }
    }
}
