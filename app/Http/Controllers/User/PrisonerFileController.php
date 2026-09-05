<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\ResolvesListPerPage;
use App\Http\Requests\StoreInmateFilePageRequest;
use App\Http\Requests\StoreInmateFileRecordRequest;
use App\Http\Requests\UpdateInmateFileRecordRequest;
use App\Models\InmateFilePage;
use App\Models\InmateFileRecord;
use App\Models\InmateIntakeRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PrisonerFileController extends Controller
{
    use ResolvesListPerPage;

    public function index(Request $request): View|Response
    {
        $filters = [
            'q' => $request->string('q')->trim()->toString(),
            'gender' => $request->string('gender')->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
            'per_page' => $this->resolvePerPage($request),
        ];

        $files = InmateFileRecord::query()
            ->with('inmate')
            ->withCount('pages')
            ->when($filters['q'], fn ($query, string $term) => $query->search($term))
            ->when($filters['gender'], fn ($query, string $gender) => $query->where('gender', $gender))
            ->when($filters['from'], fn ($query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'], fn ($query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        if ($request->header('X-Intake-Search') === '1') {
            return response()->view('user.pages.partials.prisoners-results', [
                'files' => $files,
                'filters' => $filters,
            ]);
        }

        $editingFile = null;
        $viewingFile = null;
        $documentsFile = null;

        if ($request->filled('documents')) {
            $documentsFile = InmateFileRecord::query()
                ->with(['inmate', 'pages'])
                ->find($request->integer('documents'));
        } elseif ($request->filled('view')) {
            $viewingFile = InmateFileRecord::query()
                ->with('inmate')
                ->find($request->integer('view'));
        } elseif ($request->filled('edit')) {
            $editingFile = InmateFileRecord::query()
                ->with('inmate')
                ->find($request->integer('edit'));
        }

        return view('user.pages.prisoners', [
            'title' => __('app.user.nav_prisoners'),
            'description' => __('app.user.prisoners_description'),
            'files' => $files,
            'filters' => $filters,
            'editingFile' => $editingFile,
            'viewingFile' => $viewingFile,
            'documentsFile' => $documentsFile,
            'availableInmates' => $this->unregisteredInmates(),
        ]);
    }

    public function store(StoreInmateFileRecordRequest $request): RedirectResponse
    {
        InmateFileRecord::query()->create([
            ...$this->filePayload($request),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('user.prisoners', $this->listQuery($request))
            ->with('success', __('app.prisoners.saved_success'));
    }

    public function update(
        UpdateInmateFileRecordRequest $request,
        InmateFileRecord $inmateFileRecord,
    ): RedirectResponse {
        $inmateFileRecord->update($this->filePayload($request));

        return redirect()
            ->route('user.prisoners', $this->listQuery($request))
            ->with('success', __('app.prisoners.updated_success'));
    }

    public function destroy(Request $request, InmateFileRecord $inmateFileRecord): RedirectResponse
    {
        $inmateFileRecord->pages()->each(function (InmateFilePage $page): void {
            $this->deletePageImage($page->image_path);
        });

        $inmateFileRecord->delete();

        return redirect()
            ->route('user.prisoners', $this->listQuery($request))
            ->with('success', __('app.prisoners.deleted_success'));
    }

    public function storePages(
        StoreInmateFilePageRequest $request,
        InmateFileRecord $inmateFileRecord,
    ): RedirectResponse {
        $nextPage = (int) ($inmateFileRecord->pages()->max('page_number') ?? 0);

        foreach ($request->file('pages', []) as $photo) {
            if (! $photo instanceof UploadedFile) {
                continue;
            }

            $nextPage++;
            $path = $photo->store('inmate-file-pages/'.$inmateFileRecord->id, 'public');

            InmateFilePage::query()->create([
                'user_id' => $request->user()->id,
                'inmate_file_record_id' => $inmateFileRecord->id,
                'page_number' => $nextPage,
                'image_path' => $path,
            ]);
        }

        return redirect()
            ->route('user.prisoners', array_merge($this->listQuery($request), [
                'documents' => $inmateFileRecord->id,
            ]))
            ->with('success', __('app.prisoners.documents_uploaded_success'));
    }

    public function destroyPage(
        Request $request,
        InmateFileRecord $inmateFileRecord,
        InmateFilePage $page,
    ): RedirectResponse {
        if ($page->inmate_file_record_id !== $inmateFileRecord->id) {
            abort(404);
        }

        $this->deletePageImage($page->image_path);
        $page->delete();

        return redirect()
            ->route('user.prisoners', array_merge($this->listQuery($request), [
                'documents' => $inmateFileRecord->id,
            ]))
            ->with('success', __('app.prisoners.documents_deleted_success'));
    }

    public function exportDocuments(InmateFileRecord $inmateFileRecord): View
    {
        $inmateFileRecord->load(['inmate', 'pages']);

        return view('user.pages.prisoner-documents-export', [
            'file' => $inmateFileRecord,
            'pages' => $inmateFileRecord->pages,
        ]);
    }

    private function deletePageImage(?string $path): void
    {
        if ($path !== null && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @return array<int, array{id: int, label: string, court_file_number: string|null, institution_file_number: string|null}>
     */
    private function unregisteredInmates(): array
    {
        return InmateIntakeRegistration::query()
            ->whereDoesntHave('fileRecord')
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'institution_file_number', 'court_file_number'])
            ->map(fn (InmateIntakeRegistration $inmate): array => [
                'id' => $inmate->id,
                'label' => trim($inmate->full_name.($inmate->institution_file_number ? ' ('.$inmate->institution_file_number.')' : '')),
                'court_file_number' => $inmate->court_file_number,
                'institution_file_number' => $inmate->institution_file_number,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function filePayload(StoreInmateFileRecordRequest $request): array
    {
        $data = $request->safe()->except('signature_confirmed');
        $user = $request->user();

        $data['filled_by_professional_name'] = $user->name;
        $data['signature'] = $user->name;

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function listQuery(Request $request): array
    {
        return array_filter([
            'q' => $request->input('q'),
            'gender' => $request->input('gender'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'per_page' => $request->input('per_page'),
            'page' => $request->input('page'),
        ], fn ($value) => filled($value));
    }
}
