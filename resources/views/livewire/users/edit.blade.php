<?php

use Livewire\Volt\Component;
use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use App\Models\Country;
use Livewire\WithFileUploads;
use App\Models\Language;

new class extends Component {
    use Toast, WithFileUploads;

    public User $user;

    #[Rule('nullable|image|max:1024')]
    public $photo;

    // You could use Livewire "form object" instead.
    #[Rule('required')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    // Selected languages 
    #[Rule('required')]
    public array $my_languages = [];

    // Optional
    #[Rule('sometimes')]
    public ?int $country_id = null;

    // Optional 
    #[Rule('sometimes')]
    public ?string $bio = null;

    public function mount(): void
    {
        $this->fill($this->user);
        // Fill the selected languages property 
        $this->my_languages = $this->user->languages->pluck('id')->all();
    }

    public function save(): void
    {
        $data = $this->validate();// Validate
        $this->user->update($data);// Update
        // Sync selection 
        $this->user->languages()->sync($this->my_languages);
        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }
        // You can toast and redirect to any route
        $this->success('User updated with success.', redirectTo: '/users');
    }
}; ?>

<div>
    <x-header title="Update {{ $user->name }}" separator />
    <x-form wire:submit="save">
        <x-file label="Avatar" wire:model="photo" accept="image/png, image/jpeg">
            <img src="{{ $user->avatar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
        </x-file>
        <x-input label="Name" wire:model="name" />
        <x-input label="Email" wire:model="email" />

        <x-editor wire:model="bio" label="Bio" hint="The great biography" />

        <x-slot:actions>
            <x-button label="Cancel" link="/users" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>