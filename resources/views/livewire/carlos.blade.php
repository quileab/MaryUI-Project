<?php

use Livewire\Volt\Component;

new class extends Component {

    public $nombre="Charly Brow";


    //
}; ?>

<div>
    <x-alert title="Hola {{ $nombre }}" icon="o-exclamation-triangle" />
</div>
