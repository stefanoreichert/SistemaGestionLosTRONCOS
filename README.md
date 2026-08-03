# Sistema Gestión Los Troncos

## Descripción

Sistema web de gestión para el restaurante Los Troncos. Su objetivo es centralizar y facilitar las operaciones diarias del negocio, incluyendo la administración de mesas, pedidos, productos, usuarios, mozos, cocina y reportes, con una estructura mantenible y preparada para crecer.

## Características

- Gestión de mesas.
- Gestión de pedidos.
- Gestión de productos.
- Gestión de usuarios.
- Gestión de mozos.
- Panel de cocina.
- Reportes.
- Autenticación.
- Control de acceso mediante los roles `ADMIN`, `CAJA`, `MOZO` y `COCINA`.
- Control de disponibilidad de productos.
- Asignación de mozos.
- Arquitectura Hexagonal.
- Aplicación de principios SOLID.
- Interfaz construida con DaisyUI y Tailwind CSS.

## Tecnologías utilizadas

- Laravel 12.
- PHP 8.4.
- MySQL.
- Blade.
- DaisyUI.
- Tailwind CSS.
- Vite.
- JavaScript.
- Composer.
- Railway.
- Git.
- GitHub.

## Arquitectura

El proyecto sigue una Arquitectura Hexagonal para mantener separadas la lógica de negocio, los casos de uso y los detalles de infraestructura. Esta organización reduce el acoplamiento y facilita el mantenimiento, las pruebas y la evolución del sistema.

Los principios SOLID se aplican con criterio para promover responsabilidades claras, dependencias controladas y componentes fáciles de extender, evitando abstracciones innecesarias.

## Instalación

1. Instalar las dependencias de PHP:

   ```bash
   composer install
   ```

2. Instalar las dependencias de JavaScript:

   ```bash
   npm install
   ```

3. Crear el archivo de configuración del entorno:

   ```bash
   cp .env.example .env
   ```

4. Generar la clave de la aplicación:

   ```bash
   php artisan key:generate
   ```

5. Configurar la conexión a MySQL en el archivo `.env` y ejecutar las migraciones:

   ```bash
   php artisan migrate
   ```

6. Verificar o actualizar las dependencias del frontend:

   ```bash
   npm install
   ```

7. Generar los recursos optimizados para producción:

   ```bash
   npm run build
   ```

8. Iniciar el servidor local:

   ```bash
   php artisan serve
   ```

## Variables importantes

La configuración principal se encuentra en el archivo `.env`:

- `APP_KEY`: clave utilizada por Laravel para cifrar datos sensibles. Se genera mediante `php artisan key:generate`.
- `APP_ENV`: define el entorno de ejecución, por ejemplo `local` o `production`.
- Base de datos: las variables `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` deben contener los datos de conexión a MySQL correspondientes al entorno.

No se debe publicar ni versionar el archivo `.env` con credenciales reales.

## Usuarios

Para crear el primer usuario administrador, ejecutar el comando interactivo:

```bash
php artisan user:create
```

El comando solicitará los datos necesarios y creará el usuario con rol `ADMIN`.

## Pruebas

Para ejecutar la suite de pruebas automatizadas:

```bash
php artisan test
```

## Build

Para compilar y optimizar los recursos del frontend:

```bash
npm run build
```

## Estructura del proyecto

- `app/`: lógica de aplicación, dominio, infraestructura, controladores y modelos.
- `resources/`: vistas Blade, estilos CSS y código JavaScript del frontend.
- `routes/`: definición de las rutas de la aplicación.
- `database/`: migraciones, factories y seeders de la base de datos.
- `tests/`: pruebas automatizadas unitarias y funcionales.
- `public/`: punto de entrada público y recursos compilados accesibles por el servidor web.

## Estado del proyecto

El sistema continúa en desarrollo. Las nuevas funcionalidades y mejoras se incorporan mediante ramas Git independientes para mantener los cambios aislados y facilitar su revisión.

## Autor

Stefano Reichert

## Licencia

Este proyecto utiliza la licencia MIT. Consulte el archivo `LICENSE` para conocer sus términos.
