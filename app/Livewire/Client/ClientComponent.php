<?php

namespace App\Livewire\Client;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[Title('Clientes')]
class ClientComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //Propiedades de la clase
    public int $totalRegistros = 0;
    public string $search = '';
    public int $cant = 5;

    //Propiedades modelo
    public int $userId = 0;
    public string $name;
    public string $email;
    public string $password;
    public string $confirmpassword;
    public string $role;

    public string $selectedUser;

    public function create()
    {
        $this->userId = 0;

        $this->clean();

        $this->dispatch('open-modal', 'modalClient');
    }

    /**
     * Valida y crea un usuario
     *
     * @param UserRequest $request
     * @return void
     */
    public function store(UserRequest $request): void
    {
        $request->validated();

        $user = new User();

        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = bcrypt($this->password);
        $user->role = $this->role;
        $user->save();

        $this->dispatch('close-modal', 'modalClient');
        $this->dispatch('msg', 'Cliente creado correctamente');
        $this->clean();
    }

    /**
     * Setea los valores del modal y lo abre
     *
     * @param User $user
     * @return void
     */
    public function edit(User $user): void
    {
        $this->clean();
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = $user->password;
        $this->confirmpassword = $user->password;
        $this->role = $user->role;

        $this->dispatch('open-modal', 'modalClient');
    }

    /**
     * Valida y actualiza un usuario
     *
     * @param User $user
     * @param UserRequest $request
     * @return void
     */
    public function update(User $user, UserRequest $request): void
    {
        $request->validated();

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role' => $this->role,
        ]);

        $this->dispatch('close-modal', 'modalClient');
        $this->dispatch('msg', 'Cliente editado correctamente');

        $this->clean();
    }

    //Métdo encargado de la limpieza del modal
    public function clean()
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'password',
            'confirmpassword',
            'role'
        ]);
        $this->resetErrorBag();
    }

    #[On('destroyClient')]
    public function destroy(int $id): void
    {
        $user = User::findOrFail($id);
        $user->delete();

        $this->dispatch('msg', 'El cliente ha sido eliminado correctamente');
    }

    public function render()
    {
        $query = User::query();

        if (!empty($this->selectedUser)) {
            $query->where('role', $this->selectedUser);
        }

        $this->totalRegistros = $query->count();

        $users = $query
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate($this->cant);

        $roles = User::select('role')->distinct()->pluck('role');
        return view('livewire.client.client-component', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
