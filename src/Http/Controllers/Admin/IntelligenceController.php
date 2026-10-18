<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Controllers\Admin;

use App\Http\Controllers\Api\Base\AdminBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Glitter\CommunityIntelligence\Http\Requests\ReviewSignalRequest;
use Modules\Glitter\CommunityIntelligence\Http\Requests\SignalListRequest;
use Modules\Glitter\CommunityIntelligence\Http\Requests\UpdateRuleRequest;
use Modules\Glitter\CommunityIntelligence\Http\Resources\AccessLogCollection;
use Modules\Glitter\CommunityIntelligence\Http\Resources\IncidentCollection;
use Modules\Glitter\CommunityIntelligence\Http\Resources\IncidentResource;
use Modules\Glitter\CommunityIntelligence\Http\Resources\ReviewResource;
use Modules\Glitter\CommunityIntelligence\Http\Resources\RuleCollection;
use Modules\Glitter\CommunityIntelligence\Http\Resources\RuleResource;
use Modules\Glitter\CommunityIntelligence\Http\Resources\SignalCollection;
use Modules\Glitter\CommunityIntelligence\Http\Resources\SignalResource;
use Modules\Glitter\CommunityIntelligence\Services\AccessAuditService;
use Modules\Glitter\CommunityIntelligence\Services\BriefingService;
use Modules\Glitter\CommunityIntelligence\Services\IntelligenceQueryService;
use Modules\Glitter\CommunityIntelligence\Services\ReviewService;
use Modules\Glitter\CommunityIntelligence\Services\RuleService;

class IntelligenceController extends AdminBaseController
{
    public function briefing(Request $request, BriefingService $service): JsonResponse
    {
        $hours = min(720, max(1, (int) $request->integer('hours', 24)));
        $data = $service->get($hours);
        $data['priority_signals'] = SignalResource::collection($data['priority_signals']);
        $data['recent_incidents'] = IncidentResource::collection($data['recent_incidents']);
        $data['recent_reviews'] = ReviewResource::collection($data['recent_reviews']);

        return $this->success('glitter-community-intelligence::messages.briefing.success', $data);
    }

    public function signals(SignalListRequest $request, IntelligenceQueryService $queries): JsonResponse
    {
        return $this->success('glitter-community-intelligence::messages.signal.fetch_success', new SignalCollection($queries->signals($request->validated())));
    }

    public function signalSummary(int $id, IntelligenceQueryService $queries): JsonResponse
    {
        return $this->successWithResource('glitter-community-intelligence::messages.signal.fetch_success', new SignalResource($queries->signalSummary($id)));
    }

    public function signal(Request $request, int $id, IntelligenceQueryService $queries, AccessAuditService $audit): JsonResponse
    {
        $signal = $queries->signal($id, true);
        $audit->record((int) $request->user()->id, 'signal', $id, 'signal_sensitive_detail');

        return $this->successWithResource('glitter-community-intelligence::messages.signal.fetch_success', new SignalResource($signal));
    }

    public function review(ReviewSignalRequest $request, int $id, IntelligenceQueryService $queries, ReviewService $service): JsonResponse
    {
        $review = $service->review($queries->signal($id), (int) $request->user()->id, $request->string('status')->toString(), $request->input('reason_code'), $request->input('note'));

        return $this->successWithResource('glitter-community-intelligence::messages.review.success', new ReviewResource($review));
    }

    public function incidents(Request $request, IntelligenceQueryService $queries): JsonResponse
    {
        $perPage = min(100, max(1, $request->integer('per_page', 20)));

        return $this->success('glitter-community-intelligence::messages.incident.fetch_success', new IncidentCollection($queries->incidents($perPage)));
    }

    public function incident(int $id, IntelligenceQueryService $queries): JsonResponse
    {
        return $this->successWithResource('glitter-community-intelligence::messages.incident.fetch_success', new IncidentResource($queries->incident($id)));
    }

    public function rules(IntelligenceQueryService $queries): JsonResponse
    {
        return $this->success('glitter-community-intelligence::messages.rule.fetch_success', new RuleCollection($queries->rules()));
    }

    public function updateRule(UpdateRuleRequest $request, int $id, IntelligenceQueryService $queries, RuleService $service): JsonResponse
    {
        $rule = $service->update($queries->rule($id), $request->validated(), (int) $request->user()->id);

        return $this->successWithResource('glitter-community-intelligence::messages.rule.update_success', new RuleResource($rule));
    }

    public function audits(Request $request, IntelligenceQueryService $queries): JsonResponse
    {
        $perPage = min(100, max(1, $request->integer('per_page', 20)));

        return $this->success('glitter-community-intelligence::messages.audit.fetch_success', new AccessLogCollection($queries->audits($perPage)));
    }
}
