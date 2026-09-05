<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\ResolvesListPerPage;
use App\Http\Requests\StoreInmateExpenseRegistrationRequest;
use App\Http\Requests\UpdateInmateExpenseRegistrationRequest;
use App\Models\InmateExpenseRegistration;
use App\Models\InmateIntakeRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ExpenseRegistrationController extends Controller
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

        $expenses = InmateExpenseRegistration::query()
            ->with('inmate')
            ->when($filters['q'], fn ($query, string $term) => $query->search($term))
            ->when($filters['from'], fn ($query, string $from) => $query->whereDate('release_date', '>=', $from))
            ->when($filters['to'], fn ($query, string $to) => $query->whereDate('release_date', '<=', $to))
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        if ($request->header('X-Intake-Search') === '1') {
            return response()->view('user.pages.partials.expense-results', [
                'expenses' => $expenses,
                'filters' => $filters,
            ]);
        }

        $editingExpense = null;

        if ($request->filled('edit')) {
            $editingExpense = InmateExpenseRegistration::query()
                ->with('inmate.fileRecord')
                ->find($request->integer('edit'));
        }

        return view('user.pages.expense', [
            'title' => __('app.user.nav_expense'),
            'description' => __('app.user.expense_description'),
            'expenses' => $expenses,
            'filters' => $filters,
            'editingExpense' => $editingExpense,
            'availableInmates' => $this->availableInmates($editingExpense),
        ]);
    }

    public function inmateData(InmateIntakeRegistration $registration): JsonResponse
    {
        $registration->load('fileRecord');

        return response()->json($this->inmateSnapshot($registration));
    }

    public function export(InmateExpenseRegistration $inmateExpenseRegistration): View
    {
        $inmateExpenseRegistration->load('inmate');

        return view('user.pages.expense-certificate', [
            'expense' => $inmateExpenseRegistration,
            'institute' => __('app.institute'),
        ]);
    }

    public function store(StoreInmateExpenseRegistrationRequest $request): RedirectResponse
    {
        $inmate = InmateIntakeRegistration::query()
            ->with('fileRecord')
            ->findOrFail($request->integer('inmate_intake_registration_id'));

        InmateExpenseRegistration::query()->create([
            ...$this->expensePayload($request, $inmate),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('user.expense', $this->listQuery($request))
            ->with('success', __('app.expense.saved_success'));
    }

    public function update(
        UpdateInmateExpenseRegistrationRequest $request,
        InmateExpenseRegistration $inmateExpenseRegistration,
    ): RedirectResponse {
        $inmate = InmateIntakeRegistration::query()
            ->with('fileRecord')
            ->findOrFail($request->integer('inmate_intake_registration_id'));

        $inmateExpenseRegistration->update($this->expensePayload($request, $inmate));

        return redirect()
            ->route('user.expense', $this->listQuery($request))
            ->with('success', __('app.expense.updated_success'));
    }

    public function destroy(Request $request, InmateExpenseRegistration $inmateExpenseRegistration): RedirectResponse
    {
        $inmateExpenseRegistration->delete();

        return redirect()
            ->route('user.expense', $this->listQuery($request))
            ->with('success', __('app.expense.deleted_success'));
    }

    /**
     * @return array<int, array{id: int, label: string}>
     */
    private function availableInmates(?InmateExpenseRegistration $editingExpense): array
    {
        return InmateIntakeRegistration::query()
            ->where(function ($query) use ($editingExpense): void {
                $query->whereDoesntHave('expenseRegistration');

                if ($editingExpense) {
                    $query->orWhere('id', $editingExpense->inmate_intake_registration_id);
                }
            })
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
    private function expensePayload(
        StoreInmateExpenseRegistrationRequest $request,
        InmateIntakeRegistration $inmate,
    ): array {
        $data = $request->safe()->except('signature_confirmed');
        $user = $request->user();

        return [
            ...$this->inmateSnapshot($inmate),
            ...$data,
            'official_name' => $user->name,
            'signature' => $user->name,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function inmateSnapshot(InmateIntakeRegistration $inmate): array
    {
        $file = $inmate->fileRecord;

        $genderLabel = match ($file?->gender) {
            'male' => __('app.prisoners.gender_male'),
            'female' => __('app.prisoners.gender_female'),
            default => $file?->gender,
        };

        $birthPlace = collect([
            $file?->birth_region,
            $file?->birth_zone,
            $file?->birth_woreda,
        ])->filter()->implode(', ');

        return [
            'inmate_intake_registration_id' => $inmate->id,
            'full_name' => $inmate->full_name,
            'gender' => $genderLabel,
            'age' => $file?->age,
            'religion' => $file?->religion,
            'nationality' => $file?->nationality,
            'country_of_birth' => $birthPlace ?: null,
            'admission_date' => $inmate->admission_date?->format('Y-m-d'),
            'sentencing_court' => $inmate->verdict_court,
            'sentence_duration' => $inmate->sentence_duration,
            'crime_type' => $inmate->crime_type,
            'court_file_number' => $inmate->court_file_number,
            'institution_id_number' => $inmate->institution_file_number,
            'education_skill_before' => $file?->education_level,
            'photo_url' => $inmate->photoUrl(),
            'release_reason' => $inmate->release_reason,
            'release_date' => $inmate->full_release_date?->format('Y-m-d'),
        ];
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
