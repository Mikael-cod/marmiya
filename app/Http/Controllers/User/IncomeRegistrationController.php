<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\ResolvesListPerPage;
use App\Http\Requests\StoreInmateIntakeRegistrationRequest;
use App\Http\Requests\UpdateInmateIntakeRegistrationRequest;
use App\Models\InmateIntakeRegistration;
use App\Services\ParoleScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IncomeRegistrationController extends Controller
{
    use ResolvesListPerPage;

    public function index(Request $request): View|Response
    {
        $filters = [
            'q' => $request->string('q')->trim()->toString(),
            'status' => $request->string('status')->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
            'per_page' => $this->resolvePerPage($request),
        ];

        $registrations = InmateIntakeRegistration::query()
            ->when($filters['q'], fn ($query, string $term) => $query->search($term))
            ->when($filters['status'], fn ($query, string $status) => $query->where('sentence_status', $status))
            ->when($filters['from'], fn ($query, string $from) => $query->whereDate('admission_date', '>=', $from))
            ->when($filters['to'], fn ($query, string $to) => $query->whereDate('admission_date', '<=', $to))
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        if ($request->header('X-Intake-Search') === '1') {
            return response()->view('user.pages.partials.income-results', [
                'registrations' => $registrations,
                'filters' => $filters,
            ]);
        }

        $editingRegistration = null;

        if ($request->filled('edit')) {
            $editingRegistration = InmateIntakeRegistration::query()->find($request->integer('edit'));
        }

        return view('user.pages.income', [
            'title' => __('app.user.nav_income'),
            'description' => __('app.user.income_description'),
            'registrations' => $registrations,
            'filters' => $filters,
            'editingRegistration' => $editingRegistration,
        ]);
    }

    public function store(StoreInmateIntakeRegistrationRequest $request): RedirectResponse
    {
        InmateIntakeRegistration::query()->create([
            ...$request->safe()->except('photo'),
            'photo_path' => $this->storePhoto($request->file('photo')),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('user.income', $this->listQuery($request))
            ->with('success', __('app.income.saved_success'));
    }

    public function paroleReleaseDate(Request $request, ParoleScheduleService $schedule): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'sentence_duration' => ['nullable', 'string', 'max:255'],
        ]);

        $paroleReleaseDate = $schedule->calculateParoleReleaseDate(
            $validated['start'],
            $validated['end'],
            $validated['sentence_duration'] ?? null,
        );

        $matchedRow = $schedule->matchRow(
            Carbon::parse($validated['start']),
            Carbon::parse($validated['end']),
            $validated['sentence_duration'] ?? null,
        );

        return response()->json([
            'parole_release_date' => $paroleReleaseDate?->format('Y-m-d'),
            'matched_sentence' => $matchedRow['sentence'] ?? null,
            'deducted' => $matchedRow['deducted'] ?? null,
            'served' => $matchedRow['served'] ?? null,
        ]);
    }

    public function update(
        UpdateInmateIntakeRegistrationRequest $request,
        InmateIntakeRegistration $registration,
    ): RedirectResponse {
        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            $this->deletePhoto($registration->photo_path);
            $data['photo_path'] = $this->storePhoto($request->file('photo'));
        }

        $registration->update($data);

        return redirect()
            ->route('user.income', $this->listQuery($request))
            ->with('success', __('app.income.updated_success'));
    }

    public function destroy(Request $request, InmateIntakeRegistration $registration): RedirectResponse
    {
        $this->deletePhoto($registration->photo_path);
        $registration->delete();

        return redirect()
            ->route('user.income', $this->listQuery($request))
            ->with('success', __('app.income.deleted_success'));
    }

    /**
     * @return array<string, mixed>
     */
    private function listQuery(Request $request): array
    {
        return array_filter([
            'q' => $request->input('q'),
            'status' => $request->input('status'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'per_page' => $request->input('per_page'),
            'page' => $request->input('page'),
        ], fn ($value) => filled($value));
    }

    private function storePhoto(UploadedFile $photo): string
    {
        return $photo->store('inmate-photos', 'public');
    }

    private function deletePhoto(?string $path): void
    {
        if ($path !== null && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
