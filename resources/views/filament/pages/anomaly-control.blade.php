<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        
        {{ $this->form }}

        <div class="flex justify-start mt-4">
            <x-filament::button type="submit" color="primary">
                Guardar Configuración
            </x-filament::button>
        </div>
        
    </x-filament-panels::form>
</x-filament-panels::page>