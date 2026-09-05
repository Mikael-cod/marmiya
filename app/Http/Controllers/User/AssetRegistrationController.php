<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\ResolvesListPerPage;
use App\Http\Requests\StoreInmatePropertyRegistrationRequest;
use App\Http\Requests\UpdateInmatePropertyRegistrationRequest;
use App\Models\InmateIntakeRegistration;
use App\Models\InmatePropertyRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AssetRegistrationController extends Controller
{
    use ResolvesListPerPage;

    public function index(Request $request): View|Response
    {
        $filters = [
            'q' => $request->string('q')->trim()->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
            'per_page' => $this->resolvePerPage($request),
        ];

        $properties = InmatePropertyRegistration::query()
            ->with('inmate')
            ->when($filters['q'], fn ($query, string $term) => $query->search($term))
            ->when($filters['from'], fn ($query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'], fn ($query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        if ($request->header('X-Intake-Search') === '1') {
            return response()->view('user.pages.partials.assets-results', [
                'properties' => $properties,
                'filters' => $filters,
            ]);
        }

        $editingProperty = null;

        if ($request->filled('edit')) {
            $editingProperty = InmatePropertyRegistration::query()
                ->with('inmate')
                ->find($request->integer('edit'));
        }

        return view('user.pages.assets', [
            'title' => __('app.user.nav_assets'),
            'description' => __('app.user.assets_description'),
            'properties' => $properties,
            'filters' => $filters,
            'editingProperty' => $editingProperty,
            'inmates' => $this->inmateOptions(),
        ]);
    }

    public function store(StoreInmatePropertyRegistrationRequest $request): RedirectResponse
    {
        InmatePropertyRegistration::query()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('user.assets', $this->listQuery($request))
            ->with('success', __('app.assets.saved_success'));
    }

    public function update(
        UpdateInmatePropertyRegistrationRequest $request,
        InmatePropertyRegistration $inmatePropertyRegistration,
    ): RedirectResponse {
        $inmatePropertyRegistration->update($request->validated());

        return redirect()
            ->route('user.assets', $this->listQuery($request))
            ->with('success', __('app.assets.updated_success'));
    }

    public function destroy(Request $request, InmatePropertyRegistration $inmatePropertyRegistration): RedirectResponse
    {
        $inmatePropertyRegistration->delete();

        return redirect()
            ->route('user.assets', $this->listQuery($request))
            ->with('success', __('app.assets.deleted_success'));
    }

    /**
     * @return array<int, array{id: int, label: string}>
     */
    private function inmateOptions(): array
    {
        return InmateIntakeRegistration::query()
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'institution_file_number'])
            ->map(fn (InmateIntakeRegistration $inmate): array => [
                'id' => $inmate->id,
                'label' => trim($inmate->full_name.($inmate->institution_file_number ? ' ('.$inmate->institution_file_number.')' : '')),
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function listQuery(Request $request): array
    {
        return array_filter([
            'q' => $request->input('q'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'per_page' => $request->input('per_page'),
            'page' => $request->input('page'),
        ], fn ($value) => filled($value));
    }
}
