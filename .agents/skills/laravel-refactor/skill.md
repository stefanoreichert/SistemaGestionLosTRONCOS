
---

# 3. `laravel-refactor/SKILL.md`

```md
---
name: los-troncos-laravel-refactor
description: Refactoriza código Laravel del sistema Los Troncos sin alterar funcionalidades. Usar para limpiar controllers, services, models, rutas, validaciones y código duplicado, o para separar backend y frontend.
---

# Refactorización Laravel del sistema Los Troncos

## Objetivo

Mejorar claridad, mantenimiento y organización del código sin alterar el comportamiento operativo del restaurante.

## Prioridad principal

Preservar el funcionamiento de:

1. Mesas.
2. Pedidos.
3. Ítems de pedidos.
4. Cocina.
5. Delivery.
6. Cierre de pedidos.
7. Dashboard.
8. Productos y stock.

## Regla general

Antes de modificar código, explicar brevemente:

- Qué problema existe.
- Qué archivos están involucrados.
- Qué se propone cambiar.
- Qué riesgo tiene el cambio.
- Cómo se verificará.

Para cambios grandes, crear primero un plan y esperar aprobación cuando el usuario así lo solicite.

## Controllers

Los controllers deben ser pequeños y explícitos.

Preferir:

```php
public function store(StoreOrderRequest $request): RedirectResponse
{
    $order = $this->orderService->create($request->validated());

    return redirect()
        ->route('orders.show', $order)
        ->with('success', 'Pedido creado correctamente.');
}
