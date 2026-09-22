<?php

use App\Enums\TeamRole;
use App\Models\Status;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('owners can view statuses index', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    $response = $this
        ->actingAs($owner)
        ->get(route('statuses.index', ['current_team' => $team->slug]));

    $response->assertOk();
});

test('members cannot view statuses index', function () {
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $response = $this
        ->actingAs($member)
        ->get(route('statuses.index', ['current_team' => $team->slug]));

    $response->assertForbidden();
});

test('admins can create statuses', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);

    $token = 'test-token';

    $response = $this
        ->actingAs($admin)
        ->withSession(['_token' => $token])
        ->post(route('statuses.store', ['current_team' => $team->slug]), [
            '_token' => $token,
            'code' => 'OPEN',
            'name' => 'Abierto',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('statuses', [
        'code' => 'OPEN',
        'name' => 'Abierto',
    ]);
});

test('members cannot create statuses', function () {
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $token = 'test-token';

    $response = $this
        ->actingAs($member)
        ->withSession(['_token' => $token])
        ->post(route('statuses.store', ['current_team' => $team->slug]), [
            '_token' => $token,
            'code' => 'OPEN',
            'name' => 'Abierto',
        ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('statuses', [
        'code' => 'OPEN',
    ]);
});

test('owners can import statuses from csv with created updated and skipped rows', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    Status::create([
        'code' => 'OPEN',
        'name' => 'Pendiente',
    ]);

    $csv = implode("\n", [
        'code,name',
        'open,Abierto',
        'closed,Cerrado',
        'invalid-only-column',
        ',SinCodigo',
    ]);

    $file = UploadedFile::fake()->createWithContent('statuses.csv', $csv);
    $token = 'test-token';

    $response = $this
        ->actingAs($owner)
        ->withSession(['_token' => $token])
        ->post(route('statuses.import', ['current_team' => $team->slug]), [
            '_token' => $token,
            'file' => $file,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('statuses', [
        'code' => 'OPEN',
        'name' => 'Abierto',
    ]);

    $this->assertDatabaseHas('statuses', [
        'code' => 'CLOSED',
        'name' => 'Cerrado',
    ]);

    expect(Status::query()->count())->toBe(2);
});
