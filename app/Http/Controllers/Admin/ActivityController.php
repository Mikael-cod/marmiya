<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\ResolvesListPerPage;
use App\Services\Admin\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    use ResolvesListPerPage;

    public function __construct(
        private readonly ActivityLogService $activityLogs,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->string('q')->trim()->toString(),
            'category' => $request->string('category')->toString(),
            'event' => $request->string('event')->toString(),
            'date_from' => $request->string('date_from')->toString(),
            'date_to' => $request->string('date_to')->toString(),
            'per_page' => $this->resolveActivityPerPage($request),
        ];

        return view('admin.pages.activity', [
            'title' => __('app.admin.nav_activity'),
            'description' => __('app.admin.activity_description'),
            'logs' => $this->activityLogs->paginate($filters),
            'filters' => $filters,
            'overview' => $this->activityLogs->overview(),
            'categories' => $this->activityLogs->categories(),
            'events' => $this->activityLogs->eventsForCategory($filters['category'] ?: null),
        ]);
    }

    protected function resolveActivityPerPage(Request $request): int
    {
        $perPage = (int) $request->input('per_page', config('activity.default_per_page', 20));
        $allowed = config('activity.per_page_options', [15, 20, 50, 100]);

        return in_array($perPage, $allowed, true)
            ? $perPage
            : (int) config('activity.default_per_page', 20);
    }
}
