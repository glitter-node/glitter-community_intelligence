<?php

namespace Modules\Glitter\CommunityIntelligence\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Glitter\CommunityIntelligence\Models\AccessLog;
use Modules\Glitter\CommunityIntelligence\Models\Incident;
use Modules\Glitter\CommunityIntelligence\Models\Rule;
use Modules\Glitter\CommunityIntelligence\Models\Signal;

class IntelligenceQueryService
{
    public function signals(array $filters): LengthAwarePaginator
    {
        $sort = $filters['sort_by'] ?? 'detected_at';
        $direction = $filters['sort_order'] ?? 'desc';
        $query = Signal::query();

        foreach (['type', 'status', 'board_id', 'rule_version'] as $field) {
            if (isset($filters[$field])) $query->where($field, $filters[$field]);
        }
        if (isset($filters['priority'])) $query->where('priority', '>=', $filters['priority']);
        if (isset($filters['from'])) $query->where('detected_at', '>=', $filters['from']);
        if (isset($filters['until'])) $query->where('detected_at', '<=', $filters['until']);

        return $query->orderBy($sort, $direction)->paginate($filters['per_page'] ?? 20);
    }

    public function signal(int $id, bool $withSensitive = false): Signal
    {
        return Signal::query()->when($withSensitive, fn ($query) => $query->with(['observations', 'reviews']))->findOrFail($id);
    }

    public function signalSummary(int $id): Signal
    {
        return Signal::query()->with('reviews')->findOrFail($id);
    }

    public function incidents(int $perPage): LengthAwarePaginator
    {
        return Incident::query()->latest('last_activity_at')->paginate($perPage);
    }

    public function incident(int $id): Incident
    {
        return Incident::query()->with('events')->findOrFail($id);
    }

    public function rules(): Collection
    {
        return Rule::query()->orderBy('identifier')->get();
    }

    public function rule(int $id): Rule
    {
        return Rule::query()->findOrFail($id);
    }

    public function audits(int $perPage): LengthAwarePaginator
    {
        return AccessLog::query()->latest('viewed_at')->paginate($perPage);
    }
}
