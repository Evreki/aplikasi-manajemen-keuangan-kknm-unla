<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class UserPage extends Page
{
    use WithPagination;

    protected static string $view       = 'filament.pages.user-page';
    protected static string $layout     = 'layouts.dashboard';
    protected static string $routePath  = '/users';
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $title           = 'Manajemen Pengguna';
    protected static ?int    $navigationSort  = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'super_admin';
    }

    public string $search = '';

    // ---------- Modal Form ----------
    public bool $showFormModal = false;
    public ?int $editId = null;
    public array $formData = [
        'name' => '',
        'email' => '',
        'password' => '',
        'role' => 'admin',
    ];

    // ---------- Modal Hapus ----------
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->editId = null;
        $this->formData = [
            'name' => '',
            'email' => '',
            'password' => '',
            'role' => 'admin',
        ];
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::find($id);
        if ($user) {
            $this->editId = $id;
            $this->formData = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => '', // Leave blank for edit
                'role' => $user->role ?? 'admin',
            ];
            $this->showFormModal = true;
        }
    }

    public function save(): void
    {
        $rules = [
            'formData.name' => 'required|string|max:255',
            'formData.email' => 'required|email|max:255|unique:users,email' . ($this->editId ? ',' . $this->editId : ''),
            'formData.role' => 'required|in:admin,super_admin',
        ];

        if (!$this->editId) {
            $rules['formData.password'] = 'required|string|min:6';
        }

        $this->validate($rules, [
            'formData.name.required' => 'Nama wajib diisi.',
            'formData.email.required' => 'Email wajib diisi.',
            'formData.email.unique' => 'Email sudah digunakan.',
            'formData.password.required' => 'Password wajib diisi untuk pengguna baru.',
        ]);

        $data = [
            'name' => $this->formData['name'],
            'email' => $this->formData['email'],
            'role' => $this->formData['role'],
        ];

        if (!empty($this->formData['password'])) {
            $data['password'] = Hash::make($this->formData['password']);
        }

        if ($this->editId) {
            $user = User::find($this->editId);
            
            // Protect super admin changes
            if ($user->email === 'adminkeuangan@gmail.com' && $this->formData['role'] !== 'super_admin') {
                Notification::make()->title('Role utama Super Admin tidak dapat diubah.')->danger()->send();
                return;
            }

            $user->update($data);
            Notification::make()->title('Pengguna berhasil diperbarui.')->success()->send();
        } else {
            User::create($data);
            Notification::make()->title('Pengguna baru berhasil ditambahkan.')->success()->send();
        }

        $this->closeModal();
    }

    public function openDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if (!$this->deleteId) return;

        $user = User::find($this->deleteId);
        if ($user) {
            if ($user->email === 'adminkeuangan@gmail.com') {
                Notification::make()->title('Admin utama tidak dapat dihapus.')->danger()->send();
                $this->closeModal();
                return;
            }
            if ($user->id === auth()->id()) {
                Notification::make()->title('Anda tidak bisa menghapus akun sendiri!')->danger()->send();
                $this->closeModal();
                return;
            }

            $user->delete();
            Notification::make()->title('Pengguna berhasil dihapus.')->success()->send();
        }
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->editId = null;
        $this->deleteId = null;
    }
}
