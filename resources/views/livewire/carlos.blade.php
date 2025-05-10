<?php

use Livewire\Volt\Component;

new class extends Component {

    public $nombre = "Charly Brow";

    public $users = [];

    public function mount()
    {
        $this->users = \App\Models\Post::select('id', 'title as name')->get();
    }



    //
}; ?>

<div>
    <x-alert title="Hola {{ $nombre }}" icon="o-exclamation-triangle" />
    <x-select label="Master user" wire:model="selectedUser" :options="$users" icon="o-user" />

</div>