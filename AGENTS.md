# AGENTS.md

# Sistema Los Troncos

Este proyecto es un sistema de gestión para un restaurante desarrollado con Laravel.

## Objetivo

Mantener una arquitectura limpia, escalable y fácil de mantener sin romper funcionalidades existentes.

---

# Stack

- Laravel
- PHP 8.2+
- Blade
- Vite
- JavaScript
- CSS
- MySQL

---

# Arquitectura

Respetar siempre la separación de responsabilidades.

Controller
- Recibe la petición.
- Valida.
- Llama a Services.
- Devuelve la respuesta.

Service
- Contiene toda la lógica de negocio.

Model
- Acceso a datos.

View (Blade)
- Solo presentación.
- Nunca lógica de negocio.

---

# Reglas

## NO hacer

- No duplicar código.
- No escribir consultas SQL en Blade.
- No agregar lógica de negocio en las vistas.
- No modificar rutas sin necesidad.
- No romper compatibilidad.
- No eliminar funcionalidades existentes.
- No modificar migraciones ya ejecutadas.
- No cambiar nombres de tablas sin autorización.

---

## SI hacer

- Reutilizar código existente.
- Crear métodos pequeños.
- Utilizar Dependency Injection.
- Utilizar Eloquent correctamente.
- Utilizar Services para lógica compleja.
- Mantener nombres descriptivos.
- Mantener compatibilidad hacia atrás.

---

# Frontend

Objetivo:

Separar completamente frontend del backend.

Las vistas Blade deben contener únicamente:

- HTML
- Componentes Blade
- Variables recibidas

Todo CSS debe vivir en:

resources/css

Todo JavaScript debe vivir en:

resources/js

No dejar:

- CSS inline
- JavaScript inline
- Scripts largos dentro de Blade

---

# Backend

Toda lógica debe vivir en:

- Services
- Actions (si existen)
- Helpers (solo cuando sea reutilizable)

Los Controllers deben ser lo más pequeños posible.

---

# Base de datos

Antes de modificar modelos:

- Revisar relaciones existentes.
- Revisar migraciones.
- Evitar consultas N+1.
- Utilizar eager loading cuando corresponda.

---

# Performance

Siempre intentar:

- Reducir consultas.
- Evitar cargas innecesarias.
- Reutilizar resultados.
- Utilizar índices existentes.

Nunca optimizar sacrificando claridad del código.

---

# Estilo

Preferir:

Código simple.

Antes que:

Código inteligente pero difícil de entender.

Funciones pequeñas.

Clases con una única responsabilidad.

Nombres descriptivos.

---

# Antes de modificar

Siempre analizar:

1. Qué hace el archivo.
2. Qué dependencias tiene.
3. Qué puede romper.
4. Qué impacto tendrá.

No modificar archivos innecesarios.

---

# Antes de finalizar una tarea

Verificar:

- El proyecto compila.
- No hay errores PHP.
- No hay errores JavaScript.
- No hay errores Blade.
- No se rompieron rutas.
- No se rompió el flujo existente.

---

# Cuando se solicite un refactor

Priorizar este orden:

1. Extraer CSS.
2. Extraer JavaScript.
3. Reducir lógica en Blade.
4. Reutilizar componentes.
5. Simplificar Controllers.
6. Mejorar Services.
7. Optimizar consultas.

Nunca realizar varios refactorings grandes al mismo tiempo si aumenta el riesgo.

---

# Git

Trabajar únicamente sobre la rama actual.

Nunca hacer merge automáticamente.

Nunca modificar main.

Nunca eliminar ramas.

Los commits deben ser pequeños y descriptivos.

---

# Si existe una duda

No asumir.

Analizar el código existente.

Seguir el estilo ya utilizado por el proyecto.

Mantener consistencia sobre introducir una solución completamente distinta.

# Principios SOLID

Todo código nuevo y toda refactorización debe evaluar los principios SOLID.

SOLID debe utilizarse para mejorar la claridad, mantenibilidad, capacidad de prueba y separación de responsabilidades.

No aplicar SOLID de manera mecánica ni crear abstracciones innecesarias.

## S — Single Responsibility Principle

Cada clase y método debe tener una responsabilidad principal claramente definida.

### Controllers

Los controllers deben encargarse únicamente de:

- Recibir la petición.
- Delegar validaciones.
- Llamar al servicio o caso de uso.
- Retornar una respuesta.

No deben contener:

- Lógica de negocio.
- Consultas complejas.
- Cálculos extensos.
- Procesamiento de colecciones.
- Reglas específicas del restaurante.

### Services

Cada service debe representar una responsabilidad concreta.

Preferir:

- `CreateDeliveryOrderService`
- `CloseOrderService`
- `UpdateKitchenStatusService`

Evitar clases genéricas excesivamente grandes como:

- `RestaurantService`
- `GeneralService`
- `OrderManager`

### Métodos

Los métodos deben:

- Tener un objetivo concreto.
- Ser pequeños y legibles.
- Evitar realizar múltiples tareas independientes.
- Usar nombres que indiquen claramente su intención.

## O — Open/Closed Principle

Las clases deben estar abiertas a extensión, pero cerradas a modificaciones innecesarias.

Cuando aparezcan variantes reales del comportamiento, utilizar:

- Estrategias.
- Polimorfismo.
- Clases especializadas.
- Configuración.
- Eventos.

No agregar patrones por anticipación.

Ejemplo:

Si los pedidos de mesa y delivery comparten la mayor parte del flujo, reutilizar la lógica común y aislar solamente las diferencias reales.

Evitar grandes bloques condicionales que crezcan constantemente:

```php
if ($order->is_delivery) {
    // ...
} elseif ($order->is_takeaway) {
    // ...
} elseif ($order->is_table_order) {
    // ...
}
Antes de crear:

- Interface
- Repository
- Factory
- Strategy
- Action
- Value Object
- Event
- Listener

justificar:

1. qué problema resuelve;
2. por qué una clase normal no alcanza;
3. qué principio SOLID mejora.

Si no existe una justificación clara, mantener la solución más simple.

# DaisyUI

Este proyecto utiliza DaisyUI como biblioteca principal de componentes.

Objetivos:

- Reducir CSS personalizado.
- Mantener consistencia visual.
- Reutilizar componentes existentes.
- Aprovechar Tailwind y DaisyUI antes de escribir CSS nuevo.

Siempre que exista un componente equivalente en DaisyUI, preferirlo sobre CSS personalizado.

Ejemplos:

- button
- input
- select
- textarea
- modal
- drawer
- dropdown
- navbar
- badge
- alert
- loading
- table
- card
- menu
- tabs
- toast
- tooltip

No reemplazar componentes de una sola vez.

Migrar gradualmente.

No modificar el comportamiento.

Mantener la identidad visual del restaurante mediante un tema personalizado.
