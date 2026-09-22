<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentTeam;
use App\Http\Requests\ImportRolCsvRequest;
use App\Models\Rol;
use App\Http\Requests\StoreRolRequest;
use App\Http\Requests\UpdateRolRequest;
use App\Support\CsvCatalogImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class RolController extends Controller
{
    use ResolvesCurrentTeam;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('manageRoles', $this->team($request));

        return Inertia::render('rols/index', [
            'rols' => Rol::query()
                ->orderBy('code')
                ->get(['id', 'code', 'name'])
                ->map(fn (Rol $rol) => [
                    'id' => $rol->id,
                    'code' => $rol->code,
                    'name' => $rol->name,
                ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(StoreRolRequest $request): RedirectResponse
    {
        Gate::authorize('manageRoles', $this->team($request));

        Rol::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Rol creado correctamente.')]);

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function update(UpdateRolRequest $request, Rol $rol): RedirectResponse
    {
        Gate::authorize('manageRoles', $this->team($request));

        $rol->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Rol actualizado correctamente.')]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Rol $rol): RedirectResponse
    {
        Gate::authorize('manageRoles', $this->team($request));

        $rol->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Rol eliminado correctamente.')]);

        return back();
    }

    /**
     * Import roles from a CSV file.
     */
    public function import(ImportRolCsvRequest $request, CsvCatalogImporter $csvCatalogImporter): RedirectResponse
    {
        Gate::authorize('manageRoles', $this->team($request));

        $stats = $csvCatalogImporter->import(
            $request->file('file'),
            function (array $data): string {
                $rol = Rol::query()->where('code', $data['code'])->first();

                if (! $rol) {
                    Rol::create($data);

                    return 'created';
                }

                if ($rol->name === $data['name']) {
                    return 'skipped';
                }

                $rol->update(['name' => $data['name']]);

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
