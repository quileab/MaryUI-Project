<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <x-card title="Your stats" subtitle="Our findings about you" shadow separator>
        <x-form wire:submit="save" no-separator>
            <x-input label="Name" wire:model="name" />
         
            <x-slot:actions>
                <x-button label="Click me!" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
        
    </x-card>
</div>
