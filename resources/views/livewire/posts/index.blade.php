<?php

use App\Models\Post;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
  use Toast;
  use WithPagination;

  public string $search = '';
  public int $records_count = 0;
  public bool $drawer = false;
  public array $sortBy = ['column' => 'id', 'direction' => 'asc'];

  // Clear filters
  public function clear(): void
  {
    $this->reset();
    $this->resetPage();
    $this->success('Filters cleared.', position: 'toast-bottom');
  }

  // Delete action
  public function delete(Post $post): void
  {
    $post->delete();
    $this->warning("$post->name deleted", 'Good bye!', position: 'toast-bottom');
  }

  // Table headers
  public function headers(): array
  {
    return [
      ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
      ['key' => 'title', 'label' => 'Titulo', 'class' => 'w-64'],
      ['key' => 'category', 'label' => 'Categoria'], 
      ['key' => 'status', 'label' => 'Estado'],
    ];
  }

  /**
   * On real projects you do it with Eloquent collections.
   * Please, refer to maryUI docs to see the eloquent examples.
   */
  public function posts(): LengthAwarePaginator
  {
    $result = Post::query()
      ->when($this->search, fn($q) => $q->where('title', 'like', "%$this->search%")) // Updated 'name' to 'title'
      ->orderBy(...array_values($this->sortBy))
      ->paginate(15);

    $this->records_count = $result->total();

    return $result;
  }

  public function with(): array
  {
    return [
      'posts' => $this->posts(),
      'headers' => $this->headers(),
    ];
  }

  // Reset pagination when any component property changes
  public function updated($property): void
  {
    if (!is_array($property) && $property != '') {
      $this->resetPage();
    }
  }
}; ?>

<div>
  <!-- HEADER -->
  <x-header title="Posts" separator progress-indicator>
    <x-slot:middle class="!justify-end">
      <x-input placeholder="Buscar..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
    </x-slot:middle>
    <x-slot:actions>
      <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
      <x-badge value="{{ $records_count }}" class="badge-primary" />
    </x-slot:actions>
  </x-header>

  <!-- TABLE  -->
  <x-card>
    <x-table :headers="$headers" :rows="$posts" :sort-by="$sortBy" link="posts/{id}/edit" with-pagination>
      @scope('actions', $post)
      <x-button icon="o-trash" wire:click="delete({{ $post['id'] }})" wire:confirm="Are you sure?" spinner
        class="btn-ghost btn-sm text-red-500" />
      @endscope
    </x-table>
  </x-card>

  <!-- FILTER DRAWER -->
  <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
    <div class="grid gap-5">
      <x-input placeholder="Buscar..." wire:model.live.debounce="search" icon="o-magnifying-glass"
        @keydown.enter="$wire.drawer = false" />

    </div>

    <x-slot:actions>
      <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
      <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
    </x-slot:actions>
  </x-drawer>
</div>