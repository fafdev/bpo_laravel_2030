<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentTeam;
use App\Http\Requests\ImportStatusCsvRequest;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Models\Status;
use App\Support\CsvCatalogImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class StatusController extends Controller
{
    use ResolvesCurrentTeam;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('manageStatuses', $this->team($request));

        return Inertia::render('statuses/index', [
            'statuses' => Status::query()
                ->orderBy('code')
                ->get(['id', 'code', 'name'])
                ->map(fn (Status $status) => [
                    'id' => $status->id,
                    'code' => $status->code,
                    'name' => $status->name,
                ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStatusRequest $request): RedirectResponse
    {
        Gate::authorize('manageStatuses', $this->team($request));

        Status::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Estado creado correctamente.')]);

        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, Status $status): RedirectResponse
    {
        Gate::authorize('manageStatuses', $this->team($request));

        $status->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Estado actualizado correctamente.')]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Status $status): RedirectResponse
    {
        Gate::authorize('manageStatuses', $this->team($request));

        $status->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Estado eliminado correctamente.')]);

        return back();
    }

    /**
     * Import statuses from a CSV file.
     */
    public function importCsv(ImportStatusCsvRequest $request, CsvCatalogImporter $csvCatalogImporter): RedirectResponse
    {
        Gate::authorize('manageStatuses', $this->team($request));

        $stats = $csvCatalogImporter->import(
            $request->file('file'),
            function (array $data): string {
                $status = Status::query()->where('code', $data['code'])->first();

                if (! $status) {
                    Status::create($data);

                    return 'created';
                }

                if ($status->name === $data['name']) {
                    return 'skipped';
                }

                $status->update(['name' => $data['name']]);

                return 'updated';
            },
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Importación finalizada. Creados: :created, actualizados: :updated, omitidos: :skipped', [
                'created' => $stats['created'],
                'updated' => $stats['updated'],
                'skipped' => $stats['skipped'],
            ]),
        ]);

        return back();
    }
}
