<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    // Tests for user restrictions on the creation of roles

    public function test_guest_user_cannot_create_role()
    {
        $response = $this->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_user_cannot_update_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];
        auth()->forgetGuards();

        $response = $this->json('PATCH', "/api/roles/update/{$role_id}", [
            'name' => fake()->word()
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_user_cannot_delete_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
            // 'permissions' => 
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];
        auth()->forgetGuards();

        $response = $this->json('DELETE', "/api/roles/delete/{$role_id}");

        $response->assertUnauthorized();
    }

    public function test_guest_user_cannot_view_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];
        auth()->forgetGuards();

        $response = $this->json('GET', "/api/roles/show/{$role_id}");

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_with_valid_role_can_create_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $response->assertOk();
    }

    public function test_authenticated_user_with_valid_role_can_update_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('PATCH', "/api/roles/update/{$role_id}", [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $response->assertOk();
    }

    public function test_authenticated_user_with_valid_role_can_delete_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('DELETE', "/api/roles/delete/{$role_id}");

        $response->assertOk();
    }

    public function test_authenticated_user_with_valid_role_can_view_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('GET', "/api/roles/show/{$role_id}");

        $response->assertOk();
    }

    public function test_authenticated_user_without_valid_role_cannot_create_role()
    {
        $user = $this->create_user_with_role('free-customer');
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $response->assertForbidden();
    }

    public function test_authenticated_user_without_valid_role_cannot_update_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];
        $user = $this->create_user_with_role('free-customer');

        $response = $this->actingAs($user)->json('PATCH', "/api/roles/update/{$role_id}", [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $response->assertForbidden();
    }

    public function test_authenticated_user_without_valid_role_cannot_delete_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];
        $user = $this->create_user_with_role('free-customer');

        $response = $this->actingAs($user)->json('DELETE', "/api/roles/delete/{$role_id}");

        $response->assertForbidden();
    }

    public function test_authenticated_user_without_valid_role_cannot_view_role()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];
        $user = $this->create_user_with_role('free-customer');

        $response = $this->actingAs($user)->json('GET', "/api/roles/show/{$role_id}");

        $response->assertForbidden();
    }

    // Tests for the creation of roles

    public function test_a_role_can_be_created()
    {
        $user = $this->create_user_with_role();
        $role_name = fake()->word();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => $role_name,
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $response->assertOk();
        $this->assertDatabaseHas('roles', [
            'id'   => $role_id,
            'name' => $role_name,
        ]);
    }

    public function test_a_validation_error_is_returned_when_name_is_not_present()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'description' => fake()->word()
        ]);

        $response->assertInvalid(['name']);
    }

    public function test_a_validation_error_is_returned_when_a_duplicate_name_is_used()
    {
        $user = $this->create_user_with_role();
        $role = fake()->word();

        $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => $role,
            'permissions' => null,
        ]);

        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => $role,
            'permissions' => null,
        ]);

        $response->assertInvalid(['name']);
    }

    public function test_an_audit_record_is_created_when_a_role_is_created()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $this->assertDatabaseHas('audits', [
            'auditable_type' => 'App\Models\Permissions\Role',
            'auditable_id'   => $role_id,
            'event'          => 'created',
        ]);
    }

    // Tests for the update of roles

    public function test_a_role_can_be_updated()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];
        $role_name = fake()->word();

        $response = $this->actingAs($user)->json('PATCH', "/api/roles/update/{$role_id}", [
            'name' => $role_name,
            'permissions' => null,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('roles', [
            'id'   => $role_id,
            'name' => $role_name,
        ]);
    }

    public function test_a_validation_error_is_returned_when_name_is_not_present_on_update()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('PATCH', "/api/roles/update/{$role_id}", [
            'description' => fake()->word(),
        ]);

        $response->assertInvalid(['name']);
    }

    public function test_an_audit_record_is_created_when_a_role_is_updated()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('PATCH', "/api/roles/update/{$role_id}", [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => 'App\Models\Permissions\Role',
            'auditable_id'   => $role_id,
            'event'          => 'updated',
        ]);
    }

    // Tests for the deletion of roles

    public function test_a_role_can_be_deleted()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('DELETE', "/api/roles/delete/{$role_id}");

        $response->assertOk();
        $this->assertDatabaseMissing('roles', [
            'id' => $role_id,
            'deleted_at' => null,
        ]);
    }

    public function test_an_audit_record_is_created_when_a_role_is_deleted()
    {
        $user = $this->create_user_with_role();

        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('DELETE', "/api/roles/delete/{$role_id}");

        $this->assertDatabaseHas('audits', [
            'auditable_type' => 'App\Models\Permissions\Role',
            'auditable_id'   => $role_id,
            'event'          => 'deleted',
        ]);
    }

    // Tests for viewing a role
    

    public function test_a_role_can_be_viewed()
    {
        $user = $this->create_user_with_role();
        $response = $this->actingAs($user)->json('PUT', '/api/roles/create', [
            'name' => fake()->word(),
            'permissions' => null,
        ]);

        $content = json_decode($response->getContent(), true);
        $role_id = $content['data']['id'];

        $response = $this->actingAs($user)->json('GET', "/api/roles/show/{$role_id}");
        $content = json_decode($response->getContent(), true);

        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $role_id
            ],
        ]);
    }
}
