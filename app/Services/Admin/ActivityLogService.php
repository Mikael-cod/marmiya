<?php

namespace App\Services\Admin;

use App\Models\ActivityLog;
use App\Models\SecurityLoginAttempt;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogService
{
    public function record(
        string $event,
        ?string $description = null,
        array $metadata = [],
        ?User $actor = null,
        ?Request $request = null,
    ): ActivityLog {
        $category = (string) (config("activity.events.{$event}") ?? 'system');

        return ActivityLog::query()->create([
            'user_id' => $actor?->id,
            'user_name' => $actor?->name,
            'user_email' => $actor?->email,
            'category' => $category,
            'event' => $event,
            'description' => $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent() ? mb_substr((string) $request->userAgent(), 0, 500) : null,
            'metadata' => $metadata === [] ? null : $metadata,
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = ActivityLog::query()
            ->search($filters['q'] ?? null)
            ->when(filled($filters['category'] ?? null), fn ($builder) => $builder->where('category', $filters['category']))
            ->when(filled($filters['event'] ?? null), fn ($builder) => $builder->where('event', $filters['event']))
            ->when(filled($filters['date_from'] ?? null), fn ($builder) => $builder->whereDate('created_at', '>=', $filters['date_from']))
            ->when(filled($filters['date_to'] ?? null), fn ($builder) => $builder->whereDate('created_at', '<=', $filters['date_to']))
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        return $query->paginate((int) ($filters['per_page'] ?? config('activity.default_per_page', 20)))
            ->withQueryString();
    }

    /**
     * @return array<string, int>
     */
    public function overview(): array
    {
        $sessionLifetime = (int) config('session.lifetime', 120);

        return [
            'logins_today' => ActivityLog::query()
                ->where('event', 'auth.login')
                ->whereDate('created_at', today())
                ->count(),
            'logouts_today' => ActivityLog::query()
                ->where('event', 'auth.logout')
                ->whereDate('created_at', today())
                ->count(),
            'failed_logins_24h' => SecurityLoginAttempt::query()
                ->failed()
                ->where('attempted_at', '>=', now()->subDay())
                ->count(),
            'admin_actions_today' => ActivityLog::query()
                ->where('category', 'admin')
                ->whereDate('created_at', today())
                ->count(),
            'active_sessions' => DB::table('sessions')
                ->where('last_activity', '>=', now()->subMinutes($sessionLifetime)->getTimestamp())
                ->count(),
            'total_logs' => ActivityLog::query()->count(),
        ];
    }

    /**
     * @return list<string>
     */
    public function categories(): array
    {
        return array_keys(config('activity.categories', []));
    }

    /**
     * @return list<string>
     */
    public function eventsForCategory(?string $category): array
    {
        $events = config('activity.events', []);

        if (! filled($category)) {
            return array_keys($events);
        }

        return collect($events)
            ->filter(fn (string $eventCategory): bool => $eventCategory === $category)
            ->keys()
            ->values()
            ->all();
    }
}
