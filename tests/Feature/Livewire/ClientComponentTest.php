<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Client\ClientComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientComponentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_client_component_successfully(): void
    {
        User::factory()->create(['name' => 'Cliente Ejemplo', 'role' => 'client']);

        Livewire::test(ClientComponent::class)
            ->assertStatus(200)
            ->assertSee('Cliente Ejemplo');
    }

    #[Test]
    public function it_filters_clients_by_search_term(): void
    {
        User::factory()->create(['name' => 'Juan Pérez']);
        User::factory()->create(['name' => 'María Gómez']);

        Livewire::test(ClientComponent::class)
            ->set('search', 'Juan')
            ->assertSee('Juan Pérez')
            ->assertDontSee('María Gómez');
    }

    #[Test]
    public function it_filters_clients_by_selected_role(): void
    {
        User::factory()->create(['name' => 'Admin User', 'role' => 'admin']);
        User::factory()->create(['name' => 'Client User', 'role' => 'client']);

        Livewire::test(ClientComponent::class)
            ->set('selectedUser', 'client')
            ->assertSee('Client User')
            ->assertDontSee('Admin User');
    }

    #[Test]
    public function it_dispatches_open_modal_event_on_create_action(): void
    {
        Livewire::test(ClientComponent::class)
            ->call('create')
            ->assertSet('userId', 0)
            ->assertDispatched('open-modal', 'modalClient');
    }

    #[Test]
    public function it_creates_a_new_client_and_dispatches_events(): void
    {
        Livewire::test(ClientComponent::class)
            ->set('name', 'Nuevo Cliente')
            ->set('email', 'nuevo@example.com')
            ->set('password', 'password123')
            ->set('confirmpassword', 'password123')
            ->set('role', 'client')
            ->call('store')
            ->assertDispatched('close-modal', 'modalClient')
            ->assertDispatched('msg', 'Cliente creado correctamente');

        $this->assertDatabaseHas('users', [
            'name' => 'Nuevo Cliente',
            'email' => 'nuevo@example.com',
            'role' => 'client',
        ]);
    }

    #[Test]
    public function it_sets_user_data_and_dispatches_open_modal_on_edit(): void
    {
        $user = User::factory()->create([
            'name' => 'Cliente A Editar',
            'email' => 'editar@example.com',
            'role' => 'client',
        ]);

        Livewire::test(ClientComponent::class)
            ->call('edit', $user)
            ->assertSet('userId', $user->id)
            ->assertSet('name', 'Cliente A Editar')
            ->assertSet('email', 'editar@example.com')
            ->assertSet('role', 'client')
            ->assertDispatched('open-modal', 'modalClient');
    }

    #[Test]
    public function it_updates_an_existing_client(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        Livewire::test(ClientComponent::class)
            ->call('edit', $user)
            ->set('name', 'Cliente Actualizado')
            ->set('email', 'actualizado@example.com')
            ->set('password', 'newpassword123')
            ->set('confirmpassword', 'newpassword123')
            ->set('role', 'admin')
            ->call('update', $user)
            ->assertDispatched('close-modal', 'modalClient')
            ->assertDispatched('msg', 'Cliente editado correctamente');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Cliente Actualizado',
            'email' => 'actualizado@example.com',
            'role' => 'admin',
        ]);
    }

    #[Test]
    public function it_resets_form_fields_when_clean_is_called(): void
    {
        Livewire::test(ClientComponent::class)
            ->set('userId', 5)
            ->set('name', 'Texto Borrador')
            ->set('email', 'borrador@example.com')
            ->call('clean')
            ->assertSet('userId', 0)
            ->assertSet('name', null)
            ->assertSet('email', null);
    }

    #[Test]
    public function it_deletes_a_client_on_destroy_event(): void
    {
        $user = User::factory()->create();

        Livewire::test(ClientComponent::class)
            ->dispatch('destroyClient', $user->id)
            ->assertDispatched('msg', 'El cliente ha sido eliminado correctamente');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}