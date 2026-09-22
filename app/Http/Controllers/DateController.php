<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentTeam;
use App\Http\Requests\ImportDateCsvRequest;
use App\Models\Date;
use App\Http\Requests\StoreDateRequest;
use App\Http\Requests\UpdateDateRequest;
use App\Support\CsvCatalogImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DateController extends Controller
{
    use ResolvesCurrentTeam;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('manageDates', $this->team($request));

        return Inertia::render('dates/index', [
            'dates' => Date::query()
                ->orderBy('code')
                ->get(['id', 'code', 'name'])
                ->map(fn (Date $date) => [
                    'id' => $date->id,
                    'code' => $date->code,
                    'name' => $date->name,
                ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(StoreDateRequest $request): RedirectResponse
    {
        Gate::authorize('manageDates', $this->team($request));

        Date::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Fecha creada correctamente.')]);

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function update(UpdateDateRequest $request, Date $date): RedirectResponse
    {
        Gate::authorize('manageDates', $this->team($request));

        $date->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Fecha actualizada correctamente.')]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Date $date): RedirectResponse
    {
        Gate::authorize('manageDates', $this->team($request));

        $date->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Fecha eliminada correctamente.')]);

        return back();
    }

    /**
     * Import dates from a CSV file.
     */
    public function importCsv(ImportDateCsvRequest $request, CsvCatalogImporter $csvCatalogImporter): RedirectResponse
    {
        Gate::authorize('manageDates', $this->team($request));

        $stats = $csvCatalogImporter->import(
            $request->file('file'),
            function (array $data): string {
                $date = Date::query()->where('code', $data['code'])->first();

                if (! $date) {
                    Date::create($data);

                    return 'created';
                }

                if ($date->name === $data['name']) {
                    return 'skipped';
                }

                $date->update(['name' => $data['name']]);

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
