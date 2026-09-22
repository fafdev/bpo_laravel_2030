<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DateController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('dates', [DateController::class, 'index'])->name('dates.index');
        Route::post('dates', [DateController::class, 'store'])->name('dates.store');
        Route::patch('dates/{date}', [DateController::class, 'update'])->name('dates.update');
        Route::delete('dates/{date}', [DateController::class, 'destroy'])->name('dates.destroy');
        Route::post('dates/import', [DateController::class, 'importCsv'])->name('dates.import');

        Route::get('rols', [RolController::class, 'index'])->name('rols.index');
        Route::post('rols', [RolController::class, 'store'])->name('rols.store');
        Route::patch('rols/{rol}', [RolController::class, 'update'])->name('rols.update');
        Route::delete('rols/{rol}', [RolController::class, 'destroy'])->name('rols.destroy');
        Route::post('rols/import', [RolController::class, 'import'])->name('rols.import');

        Route::get('statuses', [StatusController::class, 'index'])->name('statuses.index');
        Route::post('statuses', [StatusController::class, 'store'])->name('statuses.store');
        Route::patch('statuses/{status}', [StatusController::class, 'update'])->name('statuses.update');
        Route::delete('statuses/{status}', [StatusController::class, 'destroy'])->name('statuses.destroy');
        Route::post('statuses/import', [StatusController::class, 'importCsv'])->name('statuses.import');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
