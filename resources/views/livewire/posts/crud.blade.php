<?php

use Livewire\Volt\Component;
use App\Models\Post;
use Mary\Traits\Toast;


new class extends Component {
    use Toast;
   //Estructura de registro de un post
    public $data=[
        'title' => '',
        'content' => '',
        'author' => '',
        'category' => '',
        'image' => '',
        'status' => '',
    ];

    public function mount()
    {
        // Initialize the data array with default values
        $this->data = [
    
            'author' => auth()->user()->name,
            'status' => 'draft',
         
        ];
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
        Post::create($this->data);
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

    <x-card title="{{ config('app.name') }}" subtitle="Our findings about you" shadow separator>
        <x-form wire:submit="save" no-separator>
            <x-input label="Titulo" wire:model="data.title" />
         
            <x-input label="Contenido" wire:model="data.content" />
            <x-input label="Autor" wire:model="data.author" />
            <x-input label="Categoria" wire:model="data.category" />
            <x-input label="Imagen" wire:model="data.image" />
            <x-input label="Estado" wire:model="data.status" />
            <x-slot:actions>
                <x-button label="Click me!" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
        
    </x-card>
</div>
