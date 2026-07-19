<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_intelligence_rules', function (Blueprint $table): void {
            $table->id()->comment('분석 규칙 ID');
            $table->string('identifier', 64)->unique('ci_rules_identifier_uq')->comment('분석 규칙 식별자');
            $table->unsignedInteger('version')->default(1)->comment('분석 규칙 버전');
            $table->boolean('enabled')->default(true)->comment('활성화 여부 (1: 활성, 0: 비활성)');
            $table->text('board_scope')->nullable()->comment('분석 대상 게시판 ID 목록 JSON');
            $table->unsignedInteger('observation_window_minutes')->comment('관찰 시간 범위(분)');
            $table->text('thresholds')->comment('분석 임계값 JSON');
            $table->unsignedBigInteger('configured_by')->nullable()->comment('마지막 설정 관리자 ID');
            $table->timestamp('configured_at')->nullable()->comment('마지막 설정 일시');
            $table->timestamps();
        });

        Schema::create('community_intelligence_signals', function (Blueprint $table): void {
            $table->id()->comment('운영 신호 ID');
            $table->string('type', 64)->index('ci_signals_type_idx')->comment('운영 신호 유형');
            $table->string('subject_type', 32)->comment('분석 대상 유형');
            $table->unsignedBigInteger('subject_id')->comment('분석 대상 ID');
            $table->unsignedBigInteger('board_id')->nullable()->index('ci_signals_board_idx')->comment('게시판 ID');
            $table->string('status', 24)->default('unreviewed')->index('ci_signals_status_idx')->comment('검토 상태');
            $table->unsignedTinyInteger('priority')->default(50)->index('ci_signals_priority_idx')->comment('우선순위 (0~100)');
            $table->timestamp('observed_from')->nullable()->comment('관찰 시작 일시');
            $table->timestamp('observed_until')->nullable()->comment('관찰 종료 일시');
            $table->string('rule_identifier', 64)->comment('적용 분석 규칙 식별자');
            $table->unsignedInteger('rule_version')->comment('적용 분석 규칙 버전');
            $table->string('summary_key', 160)->comment('요약 번역 키');
            $table->text('summary_params')->nullable()->comment('요약 번역 파라미터 JSON');
            $table->string('explanation_key', 160)->comment('설명 번역 키');
            $table->text('explanation_params')->nullable()->comment('설명 번역 파라미터 JSON');
            $table->unsignedInteger('evidence_count')->default(0)->comment('근거 개수');
            $table->string('deduplication_key', 64)->unique('ci_signals_dedup_uq')->comment('중복 방지 키');
            $table->timestamp('detected_at')->index('ci_signals_detected_idx')->comment('탐지 일시');
            $table->timestamp('last_evaluated_at')->nullable()->comment('마지막 평가 일시');
            $table->timestamps();
            $table->index(['subject_type', 'subject_id'], 'ci_signals_subject_idx');
            $table->index(['rule_identifier', 'rule_version'], 'ci_signals_rule_idx');
            $table->index(['observed_from', 'observed_until'], 'ci_signals_window_idx');
        });

        Schema::create('community_intelligence_observations', function (Blueprint $table): void {
            $table->id()->comment('관찰 근거 ID');
            $table->unsignedBigInteger('signal_id')->index('ci_observations_signal_idx')->comment('운영 신호 ID');
            $table->string('kind', 64)->comment('관찰 근거 유형');
            $table->string('source_type', 32)->comment('원본 데이터 유형');
            $table->unsignedBigInteger('source_id')->nullable()->comment('원본 데이터 ID');
            $table->unsignedBigInteger('actor_id')->nullable()->comment('행위자 사용자 ID');
            $table->timestamp('occurred_at')->index('ci_observations_occurred_idx')->comment('발생 일시');
            $table->text('facts')->comment('집계 사실 JSON');
            $table->timestamps();
            $table->index(['source_type', 'source_id'], 'ci_observations_source_idx');
        });

        Schema::create('community_intelligence_incidents', function (Blueprint $table): void {
            $table->id()->comment('운영 사건 ID');
            $table->string('type', 64)->comment('운영 사건 유형');
            $table->string('primary_subject_type', 32)->comment('주 대상 유형');
            $table->unsignedBigInteger('primary_subject_id')->comment('주 대상 ID');
            $table->unsignedBigInteger('board_id')->nullable()->index('ci_incidents_board_idx')->comment('게시판 ID');
            $table->string('title_key', 160)->comment('제목 번역 키');
            $table->text('title_params')->nullable()->comment('제목 번역 파라미터 JSON');
            $table->string('status', 24)->default('open')->index('ci_incidents_status_idx')->comment('운영 사건 상태');
            $table->timestamp('started_at')->comment('사건 시작 일시');
            $table->timestamp('last_activity_at')->index('ci_incidents_activity_idx')->comment('마지막 활동 일시');
            $table->timestamps();
            $table->unique(['primary_subject_type', 'primary_subject_id'], 'ci_incidents_subject_uq');
        });

        Schema::create('community_intelligence_incident_events', function (Blueprint $table): void {
            $table->id()->comment('운영 사건 이벤트 ID');
            $table->unsignedBigInteger('incident_id')->index('ci_events_incident_idx')->comment('운영 사건 ID');
            $table->string('event_type', 64)->index('ci_events_type_idx')->comment('이벤트 유형');
            $table->string('source_type', 32)->comment('원본 데이터 유형');
            $table->unsignedBigInteger('source_id')->nullable()->comment('원본 데이터 ID');
            $table->unsignedBigInteger('actor_id')->nullable()->comment('행위자 사용자 ID');
            $table->timestamp('occurred_at')->index('ci_events_occurred_idx')->comment('발생 일시');
            $table->string('summary_key', 160)->comment('요약 번역 키');
            $table->text('metadata')->nullable()->comment('이벤트 메타데이터 JSON');
            $table->string('idempotency_key', 64)->unique('ci_events_idempotency_uq')->comment('멱등성 보장 키');
            $table->timestamps();
            $table->index(['source_type', 'source_id'], 'ci_events_source_idx');
        });

        Schema::create('community_intelligence_reviews', function (Blueprint $table): void {
            $table->id()->comment('운영 신호 검토 ID');
            $table->unsignedBigInteger('signal_id')->index('ci_reviews_signal_idx')->comment('운영 신호 ID');
            $table->unsignedBigInteger('reviewer_id')->index('ci_reviews_reviewer_idx')->comment('검토 관리자 ID');
            $table->string('from_status', 24)->comment('변경 전 검토 상태');
            $table->string('to_status', 24)->comment('변경 후 검토 상태');
            $table->string('reason_code', 64)->nullable()->comment('검토 사유 코드');
            $table->text('note')->nullable()->comment('검토 메모');
            $table->timestamp('created_at')->useCurrent()->comment('검토 일시');
        });

        Schema::create('community_intelligence_access_logs', function (Blueprint $table): void {
            $table->id()->comment('민감 근거 열람 로그 ID');
            $table->unsignedBigInteger('user_id')->index('ci_access_user_idx')->comment('열람 관리자 ID');
            $table->string('target_type', 32)->comment('열람 대상 유형');
            $table->unsignedBigInteger('target_id')->comment('열람 대상 ID');
            $table->string('feature_identifier', 120)->comment('열람 기능 식별자');
            $table->timestamp('viewed_at')->index('ci_access_viewed_idx')->comment('열람 일시');
            $table->timestamps();
            $table->index(['target_type', 'target_id'], 'ci_access_target_idx');
        });
    }

    public function down(): void
    {
        foreach (['community_intelligence_access_logs', 'community_intelligence_reviews', 'community_intelligence_incident_events', 'community_intelligence_incidents', 'community_intelligence_observations', 'community_intelligence_signals', 'community_intelligence_rules'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
