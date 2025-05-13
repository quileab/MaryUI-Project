<?php

use Livewire\Volt\Component;
use App\Models\Post;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str; // Import Str facade for unique name

new class extends Component {
    use Toast, WithFileUploads;

    // Estructura data de registro de un post
    public $data = [
        'title' => '',
        'content' => '',
        'author' => '',
        'category' => '',
        'image' => '', // Inicializado como string vacío
        'status' => '',
    ];

    public $categories = [
        ['id' => 'general', 'name' => 'General'],
        ['id' => '15', 'name' => 'Quinces'],
        ['id' => 'weddings', 'name' => 'Casamientos'],
        ['id' => 'birthdays', 'name' => 'Cumpleaños'],
    ];

    public $statuses = [
        ['id' => 'draft', 'name' => 'Borrador'],
        ['id' => 'published', 'name' => 'Publicado'],
    ];

    #[Validate('nullable|image|max:1024')]
    public $photo;

    // Propiedad para el modelo Post
    public Post $post;

    public function mount(Post $post)
    {
        dd(sys_get_temp_dir(), assets());
        $this->post = $post;

        // Valores por defecto para un nuevo post
        $defaults = [
            'title' => '',
            'content' => '',
            'author' => auth()->user()->name,
            'category' => '',
            'image' => '', // Usaremos el asset por defecto en la vista si no hay imagen
            'status' => 'draft',
        ];

        $this->data = $post->exists ? array_merge($defaults, $post->toArray()) : $defaults;

        $this->data = is_array($this->data) ? $this->data : [];

        if (!array_key_exists('image', $this->data) || empty($this->data['image'])) {
            $this->data['image'] = asset('/assets/images/empty.jpg');
        }

    }

    // Método para guardar el post
    public function save()
    {
        // Validar los datos del formulario
        $this->validate([
            'data.title' => 'required|string|max:255',
            'data.content' => 'required|string',
            'data.author' => 'required|string|max:255',
            'data.category' => 'required|string|max:255',
            'data.image' => 'nullable|string|max:255', // Validamos la ruta de la imagen guardada
            'data.status' => 'required|string|in:draft,published', // Solo permitir draft o published
        ]);

        // Si se subió una nueva imagen, guardarla
        if ($this->photo) {
            // Generar un nombre único para el archivo para evitar colisiones
            $imageName = Str::random(40) . '.' . $this->photo->getClientOriginalExtension();

            // Guardar la imagen en el disco 'public' dentro de la carpeta 'images'
            $imagePath = $this->photo->storeAs('images', $imageName, 'public');

            // Opcional: Eliminar la imagen antigua si estamos actualizando un post existente
            // y si la imagen antigua es diferente a la nueva
            if ($this->post->exists && !empty($this->post->image) && $this->post->image !== $imagePath) {
                // Asegurarse de que la imagen antigua existe antes de intentar eliminarla
                if (\Storage::disk('public')->exists($this->post->image)) {
                    \Storage::disk('public')->delete($this->post->image);
                }
            }

            // Actualizar la ruta de la imagen en los datos del formulario
            $this->data['image'] = $imagePath;

            // Limpiar la propiedad $photo después de guardar
            $this->photo = null;
        } else {
            // Si no se subió una nueva foto, mantener la ruta de la imagen existente si la hay
            // Si es un nuevo post y no se subió foto, 'image' ya está vacío por defecto
            if ($this->post->exists && empty($this->data['image'])) {
                // Si estamos actualizando y la imagen se borró (ej. usuario la quitó),
                // podemos establecer la ruta a null o string vacío en la base de datos
                $this->data['image'] = null; // O ''
            }
        }

        // Guardar los datos en la base de datos
        Post::updateOrCreate(
            ['id' => $this->post->exists ? $this->post->id : null], // Si hay ID, actualiza; si no, crea
            $this->data // Guarda todos los datos, incluida la ruta de la imagen
        );

        // Mostrar mensaje de éxito
        $this->success('Post guardado con éxito!');

        // Opcional: Redireccionar o resetear el formulario para un nuevo post
        // if (!$this->post->exists) {
        //     $this->data = [
        //         'title' => '',
        //         'content' => '',
        //         'author' => auth()->user()->name,
        //         'category' => '',
        //         'image' => '',
        //         'status' => 'draft',
        //     ];
        // }
        // return redirect()->route('posts.index'); // Ejemplo de redirección
    }

    // Método reactivo que se llama cuando $photo cambia.
    // Valida solo la propiedad $photo.
    // public function updatingPhoto($value)
    // {
    //     $this->validateOnly('photo');
    //     dd($value);
    // }

}; ?>

<div>
    {{-- col-span-full para que ocupe todo el ancho en dispositivos pequeños --}}
    <x-card title="{{ config('app.name') }}" subtitle="{{ $post && $post->exists ? 'Actualizar' : 'Crear' }}" shadow
        separator class="col-span-full">
        {{-- wire:submit.prevent="save" para evitar el envío tradicional del formulario --}}
        <x-form wire:submit="save" no-separator>
            <x-input label="Título" wire:model="data.title" />
            @php
                // Configuración para el editor TinyMCE
                $config = [
                    'plugins' => 'autoresize link image quickbars', // Añadido 'image' plugin
                    'statusbar' => false,
                    'toolbar' => 'undo redo | bold italic underline | forecolor backcolor | h1 h2 h3 h4 h5 h6 | link image | removeformat | quicktable', // Añadido 'image' a la toolbar
                    'quickbars_selection_toolbar' => 'bold italic link',
                    // Opcional: Configuración para subida de imágenes en TinyMCE (si quieres esa funcionalidad)
                    // 'images_upload_url' => '/your-image-upload-handler', // Define tu ruta de subida de imágenes
                    // 'automatic_uploads' => true,
                    // 'file_picker_types' => 'image',
                ];
            @endphp
            {{-- Editor de contenido --}}
            <x-editor wire:model="data.content" label="Contenido" :config="$config" />

            <x-input label="Autor" wire:model="data.author" disabled />
            <x-select label="Categoría" wire:model="data.category" :options="$categories" icon="o-rectangle-stack"
                placeholder="Selecciona una categoría" />

            {{-- Componente de subida de archivo de MaryUI --}}
            <x-file label="Imagen" wire:model.lazy="photo">
                {{-- Mostramos la URL temporal si hay una foto subida, de lo contrario la imagen guardada, o el asset
                por defecto --}}
                {{-- <img src="{{ $photo ? $photo->temporaryUrl() : $data['image'] }}"
                    class="h-40 rounded-lg object-cover" alt="Previsualización de la imagen" /> --}}
            </x-file>
            <x-select label="Estado" wire:model="data.status" :options="$statuses" />

            {{-- Acciones del formulario --}}
            <x-slot:actions>
                {{-- Botón de guardar --}}
                <x-button label="{{ $post->exists ? 'Actualizar Post' : 'Crear Post' }}" class="btn-primary"
                    type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>