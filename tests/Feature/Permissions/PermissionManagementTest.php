<?php

namespace Tests\Feature\Auth;

use App\Models\Permissions\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionManagementTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    protected function create_user_with_role($role = 'superadmin'): Authenticatable
    {
        $role = Role::whereName($role)->first();
        $user = User::factory()->create();
        $user->assignRole($role);
        return User::find($user->id);
    }

    // Tests for user restrictions on the creation of permissions

    public function test_guest_user_cannot_create_permission()
    {
        $response = $this->json('PUT', '/api/permissions/create', [
            'name' => fake()->word()
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_user_cannot_update_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];
        auth()->forgetGuards();

        $response = $this->json('PATCH', "/api/permissions/update/{$permission_id}", [
            'name' => fake()->word()
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_user_cannot_delete_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];
        auth()->forgetGuards();

        $response = $this->json('DELETE', "/api/permissions/delete/{$permission_id}");

        $response->assertUnauthorized();
    }

    public function test_guest_user_cannot_view_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];
        auth()->forgetGuards();

        $response = $this->json('GET', "/api/permissions/show/{$permission_id}");

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_with_valid_role_can_create_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word()
        ]);

        $response->assertOk();
    }

    public function test_authenticated_user_with_valid_role_can_update_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('PATCH', "/api/permissions/update/{$permission_id}", [
            'name' => fake()->word()
        ]);

        $response->assertOk();
    }

    public function test_authenticated_user_with_valid_role_can_delete_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('DELETE', "/api/permissions/delete/{$permission_id}");

        $response->assertOk();
    }

    public function test_authenticated_user_with_valid_role_can_view_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('GET', "/api/permissions/show/{$permission_id}");

        $response->assertOk();
    }

    public function test_authenticated_user_without_valid_role_cannot_create_permission()
    {
        $user = $this->create_user_with_role('free-customer');
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word()
        ]);

        $response->assertForbidden();
    }

    public function test_authenticated_user_without_valid_role_cannot_update_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];
        $user = $this->create_user_with_role('free-customer');

        $response = $this->actingAs($user)->json('PATCH', "/api/permissions/update/{$permission_id}", [
            'name' => fake()->word()
        ]);

        $response->assertForbidden();
    }

    public function test_authenticated_user_without_valid_role_cannot_delete_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];
        $user = $this->create_user_with_role('free-customer');

        $response = $this->actingAs($user)->json('DELETE', "/api/permissions/delete/{$permission_id}");

        $response->assertForbidden();
    }

    public function test_authenticated_user_without_valid_role_cannot_view_permission()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];
        $user = $this->create_user_with_role('free-customer');

        $response = $this->actingAs($user)->json('GET', "/api/permissions/show/{$permission_id}");

        $response->assertForbidden();
    }

    // Tests for the creation of permissions

    public function test_a_permission_can_be_created()
    {
        $user = $this->create_user_with_role();
        $permission_name = fake()->word();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => $permission_name,
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $response->assertOk();
        $this->assertDatabaseHas('permissions', [
            'id'   => $permission_id,
            'name' => $permission_name,
        ]);
    }

    public function test_a_validation_error_is_returned_when_name_is_not_present()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'description' => fake()->word()
        ]);

        $response->assertInvalid(['name']);
    }

    public function test_a_validation_error_is_returned_when_a_duplicate_name_is_used()
    {
        $user = $this->create_user_with_role();
        $permission = fake()->word();

        $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => $permission,
        ]);

        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => $permission,
        ]);

        $response->assertInvalid(['name']);
    }

    public function test_an_audit_record_is_created_when_a_permission_is_created()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $this->assertDatabaseHas('audits', [
            'auditable_type' => 'App\Models\Permissions\Permission',
            'auditable_id'   => $permission_id,
            'event'          => 'created',
        ]);
    }

    // Tests for the update of permissions

    public function test_a_permission_can_be_updated()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];
        $permission_name = fake()->word();

        $response = $this->actingAs($user)->json('PATCH', "/api/permissions/update/{$permission_id}", [
            'name' => $permission_name,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('permissions', [
            'id'   => $permission_id,
            'name' => $permission_name,
        ]);
    }

    public function test_a_validation_error_is_returned_when_name_is_not_present_on_update()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('PATCH', "/api/permissions/update/{$permission_id}", [
            'description' => fake()->word(),
        ]);

        $response->assertInvalid(['name']);
    }

    public function test_an_audit_record_is_created_when_a_permission_is_updated()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('PATCH', "/api/permissions/update/{$permission_id}", [
            'name' => fake()->word(),
        ]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => 'App\Models\Permissions\Permission',
            'auditable_id'   => $permission_id,
            'event'          => 'updated',
        ]);
    }

    // Tests for the deletion of permissions

    public function test_a_permission_can_be_deleted()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('DELETE', "/api/permissions/delete/{$permission_id}");

        $response->assertOk();
        $this->assertDatabaseMissing('permissions', [
            'id' => $permission_id,
            'deleted_at' => null,
        ]);
    }

    public function test_an_audit_record_is_created_when_a_permission_is_deleted()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('DELETE', "/api/permissions/delete/{$permission_id}");

        $this->assertDatabaseHas('audits', [
            'auditable_type' => 'App\Models\Permissions\Permission',
            'auditable_id'   => $permission_id,
            'event'          => 'deleted',
        ]);
    }

    // Tests for viewing a permission
    

    public function test_a_permission_can_be_viewed()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/permissions/create', [
            'name' => fake()->word(),
        ]);

        $content = json_decode($response->getContent(), true);
        $permission_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('GET', "/api/permissions/show/{$permission_id}");
        $content = json_decode($response->getContent(), true);

        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $permission_id
            ],
        ]);
    }
}
