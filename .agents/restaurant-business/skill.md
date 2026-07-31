---
name: los-troncos-restaurant-business
description: Reglas de negocio del sistema de gestión de Los Troncos Resto Bar. Debe utilizarse antes de modificar mesas, pedidos, cocina, delivery, productos, stock, caja, reportes o tickets.
---

# Reglas de negocio — Los Troncos Resto Bar

## Objetivo

Preservar la lógica operativa real de Los Troncos Resto Bar durante cualquier desarrollo, corrección o refactorización.

Esta skill define reglas de negocio conocidas del sistema.

No inventar reglas nuevas.

Cuando una regla no esté definida o exista ambigüedad:

1. Detener la modificación.
2. Explicar qué información falta.
3. Consultar antes de implementar.

---

# Principios generales

Todo cambio debe priorizar:

1. Correctitud de los pedidos.
2. Prevención de pérdida de información.
3. Facilidad de uso durante el servicio.
4. Velocidad de operación.
5. Trazabilidad.
6. Compatibilidad con el flujo actual del restaurante.

No alterar comportamientos existentes sin autorización.

No convertir una decisión técnica en una nueva regla de negocio.

---

# Contexto operativo

Los Troncos Resto Bar trabaja con:

- Atención en salón.
- Pedidos para retirar.
- Delivery.
- Cocina.
- Mozos.
- Mesas distribuidas en distintas áreas.
- Productos de comida y bebida.
- Tickets e impresión.
- Control de stock.

El sistema debe ser rápido y claro porque se utiliza durante el servicio.

Evitar pasos innecesarios, formularios extensos y confirmaciones repetitivas.

---

# Mesas

## Cantidad de mesas

El sistema contempla hasta 50 mesas.

La numeración y disponibilidad deben administrarse sin crear duplicados.

No generar consultas repetitivas para verificar cada mesa individualmente.

Cuando sea necesario crear un rango de mesas:

- consultar las existentes en una sola operación;
- identificar las faltantes;
- insertar las faltantes de forma masiva.

---

## Estado de una mesa

Una mesa puede considerarse:

- Libre.
- Ocupada.

Una mesa está ocupada cuando tiene un pedido abierto asociado.

Una mesa está libre cuando no tiene un pedido abierto asociado.

No determinar el estado solamente por atributos visuales o variables temporales de la vista.

La fuente de verdad debe ser el estado real del pedido asociado.

---

## Abrir una mesa

Al abrir una mesa:

- reutilizar el pedido abierto existente, si lo hay;
- no crear más de un pedido abierto para la misma mesa;
- no duplicar pedidos;
- no perder productos ya cargados;
- cargar únicamente los datos necesarios.

Si no existe un pedido abierto, se puede crear uno conforme al flujo existente.

---

## Restricción de pedidos abiertos

Una mesa no debe tener dos pedidos abiertos simultáneamente.

Antes de crear un nuevo pedido para una mesa, verificar si existe uno abierto.

Esta validación debe existir en la lógica de negocio o persistencia, no solamente en la interfaz.

---

## Cerrar una mesa

Cerrar una mesa implica cerrar su pedido abierto.

Antes de cerrar:

- el pedido debe existir;
- debe estar abierto;
- debe conservar sus productos;
- debe conservar sus cantidades;
- debe conservar sus importes;
- debe conservar su tipo de operación.

Después del cierre, la mesa debe quedar libre.

No eliminar el pedido al cerrar la mesa.

El pedido cerrado debe permanecer disponible para reportes y trazabilidad.

---

# Pedidos

## Tipos de pedido

El sistema puede manejar:

- Pedido de mesa.
- Pedido de delivery.
- Pedido para retirar, cuando el flujo actual lo contemple.

No asumir que todos los pedidos pertenecen a una mesa.

`table_id` puede ser nulo en pedidos que no corresponden al salón.

---

## Estado del pedido

Como mínimo, un pedido puede estar:

- Abierto.
- Cerrado.

Respetar los nombres y valores reales utilizados por el proyecto.

No cambiar enums, cadenas o estados persistidos sin revisar migraciones, modelos, consultas, vistas y pruebas.

---

## Pedido abierto

Un pedido abierto puede:

- recibir productos;
- modificar cantidades;
- eliminar productos, si la operación actual lo permite;
- enviarse o mostrarse en cocina;
- actualizarse durante el servicio.

No cerrar automáticamente un pedido por navegar hacia otra pantalla.

No crear un nuevo pedido cada vez que se abre la vista.

---

## Pedido cerrado

Un pedido cerrado:

- no debe modificarse accidentalmente;
- debe permanecer en el historial;
- debe incluir el total final;
- debe mantener la fecha y hora de cierre;
- debe poder utilizarse en reportes.

No reabrir pedidos cerrados salvo que exista una funcionalidad explícita y autorizada.

---

## Totales

El total del pedido debe calcularse a partir de:

- precio del producto;
- cantidad;
- reglas adicionales existentes.

No confiar en un total enviado solamente desde el navegador.

El backend debe validar o recalcular el total.

Evitar inconsistencias entre:

- total mostrado;
- total guardado;
- total del ticket;
- total de reportes.

---

## Productos del pedido

Cada línea del pedido debe identificar:

- pedido;
- producto;
- cantidad;
- precio aplicable.

No perder información histórica si posteriormente cambia el precio del producto.

Cuando el sistema ya guarde el precio de venta en la línea del pedido, utilizar ese precio para el historial.

No recalcular pedidos cerrados usando el precio actual del producto.

---

## Cantidades

Las cantidades deben ser válidas.

No permitir:

- cantidades negativas;
- cantidades nulas cuando la operación requiera al menos una unidad;
- decrementos que generen una cantidad inválida.

Cuando una cantidad llega a cero, respetar el comportamiento actual:

- eliminar la línea; o
- impedir la operación.

No cambiar ese comportamiento sin autorización.

---

# Cocina

## Objetivo de cocina

La pantalla de cocina debe mostrar pedidos que requieren preparación.

Debe priorizar:

- claridad;
- legibilidad;
- actualización rápida;
- identificación correcta del pedido;
- reducción de errores.

---

## Identificación del pedido

Un pedido de cocina debe poder identificarse por el dato correspondiente:

- número de mesa;
- delivery;
- retiro;
- etiqueta o número asignado.

No mostrar “mesa” para un pedido que no pertenece a una mesa.

---

## Actualización

El sistema puede utilizar polling o eventos para actualizar la cocina.

No reemplazar el mecanismo actual sin evaluar:

- rendimiento;
- hosting;
- compatibilidad;
- estabilidad;
- complejidad.

Si existe polling, evitar consultas excesivas e innecesarias.

Mantener el intervalo actual salvo que exista una razón medida para cambiarlo.

---

## Estado de cocina

Respetar los estados de cocina existentes.

No inventar nuevos estados ni modificar sus valores sin autorización.

Toda modificación debe contemplar:

- backend;
- persistencia;
- frontend;
- eventos;
- pruebas.

---

## Integridad

Una actualización de cocina no debe:

- eliminar productos del pedido;
- cerrar el pedido;
- cambiar la mesa;
- modificar importes;
- convertir el pedido en delivery.

El estado de cocina y el estado comercial del pedido son responsabilidades distintas.

---

# Delivery

## Identificación

Los pedidos de delivery pueden utilizar campos como:

- `is_delivery`;
- `delivery_label`;
- `delivery_number`;
- `table_id` nulo.

Respetar los campos reales presentes en el proyecto.

No asumir que un delivery tiene una mesa.

---

## Creación

Al crear un delivery:

- debe identificarse claramente;
- no debe ocupar una mesa;
- debe crearse como pedido independiente;
- debe poder recibir productos;
- debe aparecer en las pantallas correspondientes.

Evitar duplicados por doble envío del formulario.

---

## Numeración

La numeración o etiqueta de delivery debe seguir la lógica existente.

No reutilizar identificadores de forma que dos pedidos abiertos puedan confundirse.

No modificar el formato sin autorización.

---

## Modificación

Un delivery abierto puede modificarse según las mismas reglas generales de un pedido abierto.

No permitir que actualizar productos modifique accidentalmente:

- su identificador;
- su tipo;
- su estado;
- su fecha de creación.

---

## Cierre

Cerrar un delivery debe:

- conservar su historial;
- conservar sus productos;
- conservar sus importes;
- registrar su fecha de cierre;
- excluirlo de pedidos abiertos.

No convertirlo en pedido de mesa.

---

# Retiro

Cuando el proyecto contemple pedidos para retirar:

- deben identificarse de manera distinta a mesas y delivery;
- no deben ocupar una mesa;
- deben conservar su tipo durante todo el ciclo;
- deben aparecer correctamente en cocina y tickets.

No implementar el flujo de retiro por suposición si todavía no está definido en el sistema.

---

# Productos

## Datos del producto

Un producto puede incluir:

- nombre;
- precio;
- categoría;
- estado;
- stock, cuando corresponda.

Respetar los campos reales del modelo y las migraciones.

---

## Eliminación

No eliminar físicamente productos que estén asociados a pedidos históricos, salvo que la estructura del sistema lo permita sin perder trazabilidad.

Preferir desactivar o archivar productos cuando exista esa funcionalidad.

No modificar esta política sin revisar claves foráneas y reportes.

---

## Precio

El precio actual del producto se utiliza para nuevas operaciones.

Los pedidos históricos deben conservar el precio aplicado al momento de la venta.

Modificar un precio no debe modificar retrospectivamente pedidos cerrados.

---

## Caché de productos

Si los productos se almacenan en caché:

- invalidar la caché al crear;
- invalidar al editar;
- invalidar al eliminar o desactivar.

No mantener datos obsoletos después de una modificación.

No introducir caché sin una estrategia clara de invalidación.

---

# Stock

## Objetivo

El stock debe ayudar a identificar faltantes de forma simple y visual.

La pantalla principal de stock debe priorizar:

- productos con poco stock;
- productos agotados;
- alertas visibles;
- consulta rápida.

---

## Modificación de stock

No permitir agregar o quitar productos desde una pantalla cuando el requerimiento indique que esa pantalla es solamente informativa.

Diferenciar claramente entre:

- consulta de stock;
- movimientos de stock;
- creación de productos;
- edición de productos.

No agregar botones de alta, baja o ajuste sin autorización.

---

## Stock bajo

El umbral de stock bajo debe provenir de:

- una regla existente;
- una configuración;
- un valor aprobado por el usuario.

No fijar valores arbitrarios sin autorización.

Si actualmente se utiliza un criterio como menos de 3 unidades, conservarlo hasta que se indique otro.

---

## Integridad del stock

No permitir que el stock quede negativo salvo que el negocio lo autorice explícitamente.

Toda operación que modifique stock debe ser trazable.

No actualizar stock únicamente desde JavaScript.

La validación final debe realizarse en el backend.

---

# Caja y pagos

No inventar una lógica de caja completa si todavía no está implementada.

Cuando se modifiquen pagos o cierres:

- mantener el importe;
- mantener el medio de pago;
- evitar duplicaciones;
- conservar la trazabilidad;
- no alterar pedidos cerrados.

No asumir integración automática con:

- Payway;
- Todosoft;
- ARCA;
- terminales físicas.

Esas integraciones requieren definición específica.

---

# Tickets

## Datos mínimos

Un ticket debe reflejar correctamente:

- identificación del restaurante;
- número de mesa, delivery o retiro;
- productos;
- cantidades;
- precios;
- total;
- fecha y hora, cuando corresponda.

---

## Impresión

El sistema puede utilizar formatos específicos, por ejemplo:

- 58 mm;
- 50 mm.

No reemplazar estilos de impresión con componentes DaisyUI.

Mantener CSS específico para tickets.

No modificar:

- anchura;
- saltos;
- visibilidad de impresión;
- tipografía crítica;
- márgenes;

sin verificar una impresión real o vista previa.

---

## Datos históricos

El ticket de un pedido cerrado debe usar los datos guardados en ese pedido.

No utilizar precios actuales para imprimir ventas históricas.

---

# Reportes

## Fuente de datos

Los reportes deben basarse en pedidos persistidos.

No calcular reportes desde información visual de las vistas.

---

## Fechas

Para reportes diarios o mensuales, utilizar rangos de fecha sobre el campo correspondiente, por ejemplo `closed_at`, cuando la lógica real del sistema así lo determine.

Preferir rangos:

```php
whereBetween(...)
```

o condiciones equivalentes que aprovechen índices.

Evitar transformar columnas indexadas dentro de la consulta si eso impide utilizar índices.

---

## Pedidos incluidos

Los reportes de ventas deben indicar claramente qué pedidos incluyen.

Normalmente deben utilizar pedidos cerrados.

No incluir pedidos abiertos como ventas realizadas, salvo regla explícita.

---

## Totales

Los totales del dashboard, reportes y tickets deben ser consistentes.

No realizar cálculos diferentes para el mismo indicador en distintos lugares.

Cuando un valor ya fue calculado correctamente, reutilizarlo.

---

# Dashboard

El dashboard debe ofrecer información útil y rápida.

Puede incluir:

- mesas libres;
- mesas ocupadas;
- pedidos abiertos;
- ventas del día;
- pedidos cerrados del día;
- alertas de stock.

No cargar relaciones o colecciones que la vista no utiliza.

Evitar consultas duplicadas.

No priorizar gráficos decorativos sobre información operativa.

---

# Rendimiento

## Reglas

Las operaciones críticas deben evitar:

- consultas N+1;
- múltiples cargas del mismo pedido;
- consultas dentro de bucles;
- recargas completas innecesarias;
- relaciones no utilizadas;
- escrituras repetidas.

---

## Operaciones críticas

Prestar especial atención a:

- abrir mesa;
- agregar producto;
- quitar producto;
- cambiar cantidad;
- cerrar pedido;
- listar cocina;
- listar deliveries;
- dashboard.

Estas operaciones deben mantenerse simples y rápidas.

---

## Optimización

Toda optimización debe conservar:

- rutas;
- respuestas;
- comportamiento;
- reglas de negocio;
- contenido visible.

No aplicar caché como solución automática.

Primero medir o identificar la causa.

---

# Transacciones

Utilizar transacciones cuando una operación modifique varios datos que deben mantenerse consistentes.

Ejemplos:

- cerrar pedido y liberar mesa;
- crear pedido con datos asociados;
- actualizar líneas e importes;
- movimientos de stock vinculados a ventas.

Si una parte falla, evitar dejar el sistema en un estado intermedio.

No envolver consultas de solo lectura en transacciones innecesarias.

---

# Concurrencia

Considerar que dos usuarios pueden actuar al mismo tiempo.

Evitar:

- dos pedidos abiertos para la misma mesa;
- cierres dobles;
- cantidades perdidas;
- sobrescritura silenciosa;
- duplicación de deliveries.

No depender únicamente de que la interfaz deshabilite un botón.

La protección importante debe existir en el backend y, cuando corresponda, en la base de datos.

---

# Validación

Toda entrada del usuario debe validarse en el servidor.

Validar según corresponda:

- identificadores;
- cantidades;
- precios;
- estado;
- tipo de pedido;
- mesa;
- productos;
- campos de delivery.

No confiar exclusivamente en validaciones HTML o JavaScript.

Preferir `FormRequest` cuando mejora claramente la organización.

No crear un `FormRequest` innecesario para una validación mínima y aislada.

---

# Autorización

No agregar un sistema de roles o permisos sin que sea solicitado.

Cuando ya existan permisos:

- respetarlos;
- no exponer acciones nuevas;
- no omitir validaciones existentes.

No confundir autenticación con reglas de negocio.

---

# Eventos

Eventos conocidos pueden incluir:

- `OrderUpdated`;
- `KitchenStatusUpdated`.

No disparar eventos desde lugares arbitrarios.

Los eventos deben representar cambios reales ya persistidos.

No emitir un evento antes de confirmar que la operación fue exitosa.

No duplicar emisiones para una misma acción.

---

# API

Endpoints conocidos pueden incluir:

```text
POST /api/deliveries
GET /api/deliveries
GET /deliveries/{order}
POST|PUT|PATCH /deliveries/{order}/items
```

Respetar las rutas reales del proyecto.

No cambiar contratos de API sin revisar:

- frontend;
- consumidores;
- validación;
- respuestas;
- pruebas.

Mantener códigos HTTP coherentes.

No devolver HTML desde endpoints diseñados como JSON.

---

# Arquitectura

Las reglas de negocio no deben depender de detalles visuales.

Blade no debe decidir reglas críticas.

Los controladores deben coordinar, no concentrar toda la lógica.

Los repositorios no deben convertirse en contenedores de toda la aplicación.

Los servicios deben existir solamente cuando encapsulen una operación o regla significativa.

No crear capas, interfaces o patrones sin una necesidad concreta.

---

# SOLID aplicado al negocio

## Responsabilidad única

Separar responsabilidades cuando exista una razón clara.

Ejemplo:

- gestionar pedidos;
- actualizar cocina;
- calcular reportes;
- administrar stock;

no deben mezclarse en un único método o clase gigantesca.

---

## Abierto/cerrado

No utilizar este principio para crear abstracciones anticipadas.

Extender mediante estrategias o polimorfismo solamente cuando existan variantes reales.

---

## Sustitución de Liskov

Las implementaciones deben respetar el contrato esperado.

No devolver tipos, estados o valores incompatibles entre implementaciones.

---

## Segregación de interfaces

No obligar a una clase a implementar operaciones que no necesita.

Sin embargo, no dividir interfaces pequeñas sin un beneficio concreto.

---

## Inversión de dependencias

La lógica importante no debe quedar acoplada innecesariamente a detalles externos.

No crear una interfaz para cada clase.

Usar abstracciones cuando exista:

- más de una implementación;
- infraestructura reemplazable;
- necesidad real de aislamiento;
- beneficio claro para pruebas.

---

# Refactorización segura

Antes de modificar una funcionalidad:

1. Identificar el flujo actual.
2. Identificar las reglas afectadas.
3. Revisar modelos, migraciones, rutas y pruebas.
4. Explicar qué se va a cambiar.
5. Modificar una sola responsabilidad.
6. Ejecutar pruebas.
7. Verificar que el comportamiento no cambió.
8. Esperar aprobación.

No refactorizar simultáneamente:

- frontend;
- controlador;
- servicio;
- repositorio;
- base de datos;

salvo que la modificación requiera necesariamente todos esos cambios y haya sido aprobada.

---

# Pruebas mínimas

Cuando se modifique una operación crítica, cubrir cuando corresponda:

- abrir una mesa libre;
- reutilizar el pedido abierto;
- impedir dos pedidos abiertos;
- agregar producto;
- incrementar cantidad;
- reducir cantidad;
- cerrar pedido;
- liberar mesa;
- crear delivery;
- modificar delivery;
- cerrar delivery;
- conservar precios históricos;
- mostrar cocina;
- detectar stock bajo.

No borrar pruebas existentes para hacer pasar una implementación.

No reducir la calidad de una aserción.

---

# Acciones prohibidas sin autorización

No realizar automáticamente:

- rediseño completo del flujo;
- cambio de nombres de estados;
- cambio de rutas;
- cambio de estructura de base de datos;
- eliminación física de historial;
- integración de pagos;
- facturación electrónica;
- descuentos automáticos;
- promociones;
- división de cuentas;
- reapertura de pedidos;
- cambio de numeración de mesas;
- cambio de numeración de delivery;
- descuento automático de stock;
- reemplazo total del sistema de cocina;
- incorporación de roles;
- cambio de moneda;
- cambio de zona horaria.

Estas funciones requieren definición explícita.

---

# Información todavía no definida

No asumir reglas sobre:

- promociones;
- Golden Ticket;
- descuentos;
- propinas;
- medios de pago;
- división de cuenta;
- reservas;
- cancelaciones;
- devoluciones;
- facturación ARCA;
- cierres de caja;
- actualización automática de stock por venta;
- recetas e ingredientes;
- variantes de productos;
- combos;
- adicionales;
- clientes;
- direcciones;
- costos de delivery.

Consultar antes de implementar cualquiera de estas reglas.

---

# Formato de análisis obligatorio

Antes de modificar una función del negocio, informar:

## Funcionalidad analizada

## Regla de negocio actual

## Archivos involucrados

## Riesgos

## Cambio propuesto

## Comportamiento que debe conservarse

## Pruebas necesarias

Esperar aprobación cuando el cambio pueda afectar la operación real.

---

# Validación final

Ejecutar según corresponda:

```bash
php artisan test
npm run build
```

Además verificar:

- no existen pedidos duplicados;
- no se perdió historial;
- las mesas se liberan correctamente;
- los deliveries conservan su identidad;
- cocina sigue mostrando pedidos;
- los totales coinciden;
- los tickets continúan imprimiendo;
- los reportes no incluyen datos incorrectos;
- no se agregaron reglas inventadas.

---

# Regla final

Ante una duda entre:

- preservar el comportamiento actual; o
- aplicar una mejora no solicitada;

preservar el comportamiento actual y consultar.

La estabilidad operativa del restaurante tiene prioridad sobre una arquitectura teóricamente perfecta.
