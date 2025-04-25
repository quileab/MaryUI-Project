<?php

use Livewire\Volt\Component;
use App\Models\Post;
use Mary\Traits\Toast;


new class extends Component {
    use Toast;
    //Estructura de registro de un post
    public $data = [
        'title' => '',
        'content' => '',
        'author' => '',
        'category' => '',
        'image' => '',
        'status' => '',
    ];
    public $post;

    public function mount(Post $post)
    {
        // If the post exists, fill the form with its data
        if ($post->exists) {
            $this->post = $post;
            $this->data = $post->toArray();
        } else {
            // Initialize the data array with default values
            $this->data = [
                'author' => auth()->user()->name,
                'status' => 'draft',
            ];
        }
    }

    public function save()
    {
        // Validate the data
        $this->validate([
            'data.title' => 'required|string|max:255',
            'data.content' => 'required|string',
            'data.author' => 'required|string|max:255',
            'data.category' => 'required|string|max:255',
            'data.image' => 'nullable|image|max:2048', // Optional image field
            'data.status' => 'required|string|in:draft,published', // Only allow draft or published
        ]);

        // Save the data to the database or perform any other action
        // For example, you can use a model to save the data:
        Post::updateOrCreate(
            ['id' => $this->post->exists ? $this->post->id : null],
            $this->data
        );
        // Reset the form data after saving
        // Optionally, you can show a success message or redirect the user
        $this->success('Post Creado');
        $this->data = [
            'title' => '',
            'content' => '',
            'author' => auth()->user()->name,
            'category' => '',
            'image' => '',
            'status' => 'draft',
        ];

    }

}; ?>

<div>
    {{-- colocar el nombre de la app desde .env --}}

    <x-card title="{{ config('app.name') }}" subtitle="{{ isset($post) ? 'Update' : 'Create' }}" shadow separator>
        <x-form wire:submit="save" no-separator>
            <x-input label="Título" wire:model="data.title" />
            @php
                $config = [
                    'plugins' => 'autoresize',
                    'statusbar' => false,
                    'toolbar' => 'undo redo | bold italic underline | forecolor backcolor | h1 h2 h3 h4 h5 h6 | link | removeformat | quicktable',
                    'quickbars_selection_toolbar' => 'bold italic link',
                ];
            @endphp
            <x-editor wire:model="data.content" label="Contenido" :config="$config" />
            <x-input label="Autor" wire:model="data.author" />
            <x-select label="Categoría" wire:model="data.category" :options="[
        ['id' => 'general', 'name' => 'General'],
        ['id' => '15', 'name' => 'Quinces'],
        ['id' => 'weddings', 'name' => 'Casamientos'],
        ['id' => 'birthdays', 'name' => 'Cumpleaños'],
    ]" icon="o-rectangle-stack" />
            <x-input label="Imagen" wire:model="data.image" />
            <x-input label="Estado" wire:model="data.status" />
            <x-slot:actions>
                <x-button label="{{ isset($post) ? 'Actualizar' : 'Crer' }}" class="btn-primary" type="submit"
                    spinner="save" />
            </x-slot:actions>
        </x-form>

    </x-card>
</div>