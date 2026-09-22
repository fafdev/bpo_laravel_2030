<?php

namespace App\Http\Requests;

use App\Enums\TeamPermission;
use App\Models\Status;
use App\Models\Team;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $team = $this->route('current_team');

        if (is_string($team)) {
            $team = Team::where('slug', $team)->first();
        }

        return $team instanceof Team
            && $this->user() !== null
            && $this->user()->hasTeamPermission($team, TeamPermission::ManageStatuses);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $status = $this->route('status');

        abort_if(! $status instanceof Status, 404);

        return [
            'code' => ['required', 'string', 'max:100', Rule::unique('statuses', 'code')->ignore($status->id)],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
