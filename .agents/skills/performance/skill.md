---
name: los-troncos-performance
description: Analiza y mejora el rendimiento del sistema Laravel Los Troncos. Usar cuando una pantalla tarda en cargar, existen consultas duplicadas, problemas N+1, polling excesivo, carga lenta del dashboard, mesas, cocina o delivery.
---

# Rendimiento del sistema Los Troncos

## Objetivo

Identificar la causa real de lentitud antes de aplicar optimizaciones.

No asumir que el problema es la base de datos sin medir.

## Contexto conocido

Las áreas que pueden generar carga incluyen:

- Dashboard.
- Listado de aproximadamente 50 mesas.
- Pedidos abiertos.
- Pedidos recientes.
- Cocina.
- Delivery.
- Productos.
- Cálculos diarios.
- Polling periódico.
- Broadcasting o WebSocket.
- Consultas repetidas desde relaciones Eloquent.

## Principio principal

Medir antes de optimizar.

Para cada problema de rendimiento, determinar:

- Tiempo total de respuesta.
- Cantidad de consultas.
- Duración de consultas.
- Tiempo de renderizado.
- Tiempo de frontend.
- Llamadas de red.
- Uso de polling.
- Carga de assets.
- Dependencias externas.

## Diagnóstico backend

Revisar:

- Consultas N+1.
- Relaciones cargadas innecesariamente.
- Consultas dentro de loops.
- Consultas duplicadas.
- Conteos ejecutados por separado.
- Uso incorrecto de `get()` cuando basta `exists()`, `count()` o `value()`.
- Cálculos repetidos.
- Accesores que ejecutan consultas.
- Scopes ineficientes.
- Falta de índices.
- Ordenamientos sobre columnas sin índice.
- Filtros que aplican funciones sobre columnas indexadas.

## Diagnóstico frontend

Revisar:

- Solicitudes de red duplicadas.
- Polling demasiado frecuente.
- Varios polling sobre la misma información.
- Archivos JavaScript o CSS excesivos.
- Errores que provocan reintentos.
- Renderizado repetido del DOM.
- Imágenes pesadas.
- Vite mal compilado.
- Uso de assets de desarrollo en producción.

## Eloquent

Preferir:

```php
Order::query()
    ->with(['table', 'items.product'])
    ->where('status', 'open')
    ->get();
