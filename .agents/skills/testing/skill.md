---
name: los-troncos-testing
description: Define cómo validar cualquier cambio realizado en el proyecto antes de considerarlo terminado. Prioriza la estabilidad del sistema, la verificación funcional y la detección temprana de regresiones.
---

# Testing — Los Troncos

## Objetivo

Todo cambio debe validarse antes de darse por finalizado.

No asumir que un cambio funciona únicamente porque compila.

Toda modificación debe demostrar que mantiene el comportamiento esperado.

---

# Principios

Prioridad:

1. No romper funcionalidad existente.
2. Detectar regresiones.
3. Validar comportamiento.
4. Mantener rendimiento.
5. Mantener experiencia del usuario.

No considerar un cambio terminado si no fue probado.

---

# Compilación

Después de cualquier cambio en frontend ejecutar:

```bash
npm run build
```

El build debe finalizar sin errores.

No ignorar warnings importantes.

---

# Backend

Después de cambios PHP ejecutar:

```bash
php artisan test
```

Si existen pruebas relacionadas únicamente con la funcionalidad modificada, ejecutarlas primero.

No eliminar pruebas para hacer pasar una implementación.

---

# Validación visual

Cuando se modifique Blade, Tailwind o DaisyUI comprobar:

- escritorio
- tablet
- móvil

Verificar:

- alineación
- espaciado
- botones
- tablas
- formularios
- colores
- responsive

---

# Formularios

Comprobar:

- creación
- edición
- eliminación
- validaciones
- mensajes
- botones
- navegación

No asumir funcionamiento únicamente porque la vista carga.

---

# Mesas

Cuando se modifique cualquier funcionalidad de mesas verificar:

- abrir mesa
- reutilizar pedido existente
- agregar productos
- modificar cantidades
- cerrar pedido
- liberar mesa

Nunca permitir dos pedidos abiertos para una misma mesa.

---

# Productos

Cuando se modifique productos comprobar:

- listado
- búsqueda
- crear
- editar
- eliminar
- categorías
- formato del precio

---

# Delivery

Verificar:

- creación
- edición
- actualización
- cierre
- identificación

No debe afectar mesas.

---

# Cocina

Verificar:

- actualización
- estados
- identificación del pedido
- tiempos de respuesta

No deben desaparecer pedidos.

---

# Dashboard

Verificar:

- indicadores
- cantidades
- totales
- enlaces
- tarjetas
- tablas

---

# Reportes

Verificar:

- filtros
- fechas
- totales
- exportaciones
- consistencia

---

# Tickets

Comprobar:

- impresión
- formato
- márgenes
- tamaños
- datos
- totales

No modificar estilos de impresión sin probarlos.

---

# Base de datos

Después de cambios relacionados con persistencia comprobar:

- migraciones
- claves foráneas
- relaciones
- índices

No perder datos históricos.

---

# Rendimiento

Cuando una tarea indique optimización comprobar:

- consultas duplicadas
- N+1
- tiempos de respuesta
- memoria
- caché

No asumir mejoras sin evidencia.

---

# Logs

Después de cambios revisar:

```bash
storage/logs/laravel.log
```

No dejar errores nuevos.

---

# Navegación

Comprobar:

- enlaces
- redirecciones
- botones volver
- breadcrumbs
- menú lateral

---

# Accesibilidad

Verificar:

- labels
- focus
- teclado
- contraste
- aria-label existentes

No eliminar atributos de accesibilidad.

---

# DaisyUI

Cuando se utilicen componentes DaisyUI verificar:

- apariencia
- responsive
- interacción
- consistencia visual

No mezclar componentes distintos para la misma función sin motivo.

---

# Qué NO hacer

No marcar una tarea como terminada solamente porque:

- compila;
- pasa una prueba;
- no hay errores de sintaxis.

Siempre validar el comportamiento.

---

# Informe obligatorio

Al finalizar informar:

## Build

Resultado de:

```bash
npm run build
```

## Tests

Resultado de:

```bash
php artisan test
```

## Validaciones realizadas

Lista de comprobaciones funcionales.

## Archivos modificados

## Riesgos encontrados

## Pendientes

Si alguna validación no pudo realizarse, explicarlo claramente.

---

# Regla final

Si existe duda sobre el funcionamiento de una modificación, detener el proceso e informar el riesgo antes de continuar.

Es preferible un cambio más pequeño y completamente validado que una refactorización grande sin pruebas suficientes.
