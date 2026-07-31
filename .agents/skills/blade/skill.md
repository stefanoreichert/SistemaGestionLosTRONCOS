
---

# 2. `blade/SKILL.md`

```md
---
name: los-troncos-blade
description: Refactoriza y organiza vistas Blade del sistema Los Troncos. Usar para extraer CSS o JavaScript inline, crear layouts, componentes y partials, reducir duplicación visual y mantener las vistas sin lógica de negocio.
---

# Blade y frontend del sistema Los Troncos

## Objetivo

Mantener vistas Blade limpias, reutilizables y fáciles de modificar, sin cambiar la apariencia ni el comportamiento actual salvo que se solicite expresamente.

## Contexto visual

El sistema contiene pantallas operativas como:

- Listado de mesas.
- Detalle de una mesa.
- Pedidos activos.
- Cocina.
- Delivery.
- Dashboard.
- Gestión o visualización de productos.
- Modales para agregar productos o crear pedidos.

Estas pantallas deben priorizar:

- Lectura rápida.
- Botones claros.
- Estados visualmente distinguibles.
- Uso simple en computadora, tablet o celular.
- Menor cantidad posible de pasos operativos.

## Reglas para Blade

Blade puede contener:

- HTML.
- Directivas Blade simples.
- Componentes.
- Partials.
- Variables preparadas por backend.
- Condicionales únicamente de presentación.
- Bucles de renderizado.

Blade no debe contener:

- Consultas Eloquent.
- Lógica de negocio.
- Cálculos complejos.
- Transformaciones extensas de colecciones.
- Bloques grandes de PHP.
- CSS extenso dentro de `<style>`.
- JavaScript extenso dentro de `<script>`.

## CSS

Mover CSS reutilizable a:

```text
resources/css/
