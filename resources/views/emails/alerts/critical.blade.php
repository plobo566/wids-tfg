<x-mail::message>
#  ¡Alerta Crítica de Seguridad!

Ataque con nivel de severidad máximo.

**Detalles de la Detección:**
* **Regla:** {{ class_basename($detection->rule_name) }}
* **Objetivo:** {{ $detection->entity_type }} ({{ $detection->entity_value }})
* **Fecha y Hora:** {{ now()->format('d/m/Y H:i:s') }}

<x-mail::panel>
**Información Técnica:**
{{ json_encode($detection->details, JSON_PRETTY_PRINT) }}
</x-mail::panel>

<x-mail::button :url="url('/admin/detections')">
Ver en el Panel de Control
</x-mail::button>

Atentamente,<br>
Tu Sistema WIDS
</x-mail::message>