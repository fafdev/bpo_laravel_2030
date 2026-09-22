<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Team;
use Illuminate\Http\Request;

trait ResolvesCurrentTeam
{
    /**
     * Resolve the current team from route parameters.
     */
    private function team(Request $request): Team
    {
        $team = $request->route('current_team');

        if (is_string($team)) {
            $team = Team::where('slug', $team)->first();
        }

        abort_if(! $team instanceof Team, 404);

        return $team;
    }
}
