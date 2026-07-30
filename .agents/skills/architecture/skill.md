---
name: los-troncos-architecture
description: Analiza, diseña y refactoriza la arquitectura del sistema Laravel Los Troncos aplicando principios SOLID, separación de responsabilidades y simplicidad. Usar para revisar controllers, services, models, eventos, dependencias, reglas de negocio y estructura general del proyecto.
---

# Arquitectura del sistema Los Troncos

## Objetivo

Mantener una arquitectura clara, mantenible y fácil de probar para el sistema de gestión del restaurante Los Troncos.

Toda modificación debe:

- Respetar las reglas actuales del negocio.
- Mantener el comportamiento existente salvo solicitud explícita.
- Reducir acoplamiento.
- Mejorar cohesión.
- Aplicar SOLID con criterio.
- Evitar complejidad innecesaria.
- Facilitar futuras modificaciones.

La arquitectura debe resolver problemas reales del proyecto, no introducir patrones solamente por formalidad.

---

# Contexto del sistema

El sistema administra procesos operativos del restaurante Los Troncos, entre ellos:

- Mesas.
- Pedidos asociados a mesas.
- Pedidos de delivery.
- Productos.
- Categorías.
- Ítems de pedidos.
- Cocina.
- Estados de preparación.
- Cierre de pedidos.
- Dashboard.
- Control de stock.
- Actualizaciones por polling o WebSocket.
- Eventos relacionados con pedidos y cocina.

Los principales usuarios del sistema pueden incluir:

- Mozos.
- Personal de cocina.
- Personal de caja.
- Administración.
- Encargados.

Las decisiones arquitectónicas deben considerar que el sistema se utiliza durante la operación real del restaurante.

Priorizar:

- Rapidez operativa.
- Claridad.
- Estabilidad.
- Bajo riesgo de errores.
- Facilidad de mantenimiento.
- Compatibilidad con el flujo actual.

---

# Flujo arquitectónico recomendado

La estructura general debe seguir este flujo cuando corresponda:

```text
Route
  ↓
Controller
  ↓
Form Request
  ↓
Service o caso de uso
  ↓
Modelos y contratos
  ↓
Base de datos o infraestructura
  ↓
Evento o respuesta
