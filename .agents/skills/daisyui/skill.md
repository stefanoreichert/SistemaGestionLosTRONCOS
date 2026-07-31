---
name: los-troncos-daisyui
description: Utiliza DaisyUI y Tailwind CSS para construir y refactorizar la interfaz del sistema Los Troncos. Prioriza componentes de DaisyUI antes de escribir CSS personalizado, manteniendo el diseño, comportamiento y experiencia del usuario.
---

# DaisyUI para Los Troncos

## Objetivo

Utilizar DaisyUI como biblioteca principal de componentes visuales del sistema.

Objetivos:

- Reducir CSS personalizado.
- Mantener una interfaz consistente.
- Aprovechar Tailwind CSS.
- Reutilizar componentes existentes.
- Simplificar el mantenimiento.
- Mantener el diseño actual del sistema.

No modificar el funcionamiento del sistema.

---

# Principios generales

Antes de escribir CSS nuevo, comprobar si DaisyUI ya ofrece un componente equivalente.

Prioridad:

1. DaisyUI
2. Tailwind CSS
3. CSS personalizado únicamente cuando sea necesario

Evitar escribir estilos que ya existen en DaisyUI.

---

# Componentes preferidos

Utilizar DaisyUI para:

- Botones
- Inputs
- Textareas
- Selects
- Checkboxes
- Radios
- Badges
- Alerts
- Cards
- Tables
- Menús
- Dropdowns
- Modals
- Drawers
- Tabs
- Breadcrumbs
- Tooltips
- Loading
- Toasts
- Avatares
- Dividers
- Pagination
- Skeletons
- Stats
- Hero
- Navbar
- Footer

---

# Diseño

Mantener:

- Distribución actual.
- Espaciados actuales cuando sean importantes.
- Responsive existente.
- Colores del restaurante.
- Flujo visual actual.

No rediseñar pantallas sin autorización.

---

# Tailwind

Preferir utilidades Tailwind antes que crear reglas CSS.

Ejemplo:

Correcto:

```html
<div class="flex items-center gap-2">
```

Evitar:

```css
.my-container {
    display:flex;
    align-items:center;
    gap:8px;
}
```

si puede resolverse con Tailwind.

---

# CSS personalizado

Crear CSS solamente cuando:

- DaisyUI no tenga un componente equivalente.
- Tailwind vuelva el HTML excesivamente complejo.
- Existan estilos muy específicos del restaurante.
- Existan estilos de impresión.
- Sea necesario mantener compatibilidad.

Todo CSS nuevo debe ir en:

resources/css/

Nunca dentro de Blade.

---

# Botones

Preferir:

```html
<button class="btn btn-primary">
```

antes que botones completamente personalizados.

---

# Formularios

Preferir:

```html
<input class="input input-bordered">
```

```html
<select class="select select-bordered">
```

```html
<textarea class="textarea textarea-bordered">
```

---

# Tablas

Preferir:

```html
<table class="table">
```

Agregar únicamente clases Tailwind necesarias para:

- Responsive.
- Scroll horizontal.
- Espaciado.

---

# Badges

Preferir:

```html
<span class="badge badge-success">
```

```html
<span class="badge badge-error">
```

```html
<span class="badge badge-warning">
```

---

# Alertas

Utilizar:

```html
<div class="alert alert-success">
```

```html
<div class="alert alert-error">
```

---

# Modales

Utilizar el componente Modal de DaisyUI.

No crear modales personalizados salvo necesidad específica.

---

# Cards

Utilizar:

```html
<div class="card bg-base-100 shadow">
```

---

# Navbar

Utilizar el componente Navbar cuando se modifique la navegación.

Mantener la estructura actual.

---

# Drawer

Cuando corresponda utilizar menús laterales, preferir Drawer de DaisyUI antes que implementar JavaScript propio.

No reemplazar el sidebar actual sin autorización.

---

# Temas

Mantener un único tema para todo el sistema.

No mezclar múltiples temas.

No cambiar colores globales sin autorización.

---

# Accesibilidad

Mantener:

- labels
- aria-label
- focus visible
- navegación por teclado
- contraste suficiente

Nunca eliminar atributos de accesibilidad.

---

# Responsive

Todo cambio debe funcionar correctamente en:

- escritorio
- tablet
- móvil

No romper impresión.

---

# Impresión

No utilizar DaisyUI para reemplazar estilos específicos de impresión.

Mantener CSS dedicado para:

- tickets
- impresión
- tamaños 58 mm
- tamaños 50 mm

---

# Procedimiento de refactorización

Para cada vista:

1. Identificar componentes reutilizables.
2. Reemplazar CSS por DaisyUI cuando exista equivalente.
3. Utilizar Tailwind para ajustes menores.
4. Eliminar CSS obsoleto.
5. Mantener comportamiento.
6. Ejecutar build.
7. Verificar visualmente.
8. Continuar con la siguiente vista.

Nunca refactorizar múltiples pantallas simultáneamente.

---

# Restricciones

No modificar:

- rutas
- lógica PHP
- JavaScript
- controladores
- servicios

salvo que la tarea lo solicite.

No instalar plugins adicionales de Tailwind.

No instalar otras librerías UI.

No cambiar Bootstrap por otro framework.

No introducir componentes React o Vue.

---

# Validación final

Ejecutar:

```bash
npm run build
php artisan test
```

Verificar:

- No hay errores de Vite.
- No hay CSS roto.
- No hay clases inexistentes.
- La interfaz mantiene el diseño.
- Responsive correcto.
- Sidebar funciona.
- Botones funcionan.
- Modales funcionan.
- Tablas funcionan.
- Impresión continúa igual.

---

# Formato del resultado

Al finalizar informar:

## Componentes DaisyUI utilizados

## Componentes Tailwind utilizados

## CSS eliminado

## CSS agregado

## Archivos modificados

## Resultado del build

## Riesgos encontrados

Esperar aprobación antes de continuar con la siguiente pantalla.
