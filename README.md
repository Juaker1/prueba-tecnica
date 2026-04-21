# Módulo de Gestión de Solicitudes Administrativas

Solución desarrollada como prueba técnica.
Permite a usuarios internos registrar solicitudes administrativas y a administradores revisarlas, gestionarlas y dejar retroalimentación.

---

## Requisitos

- XAMPP (o equivalente) con **PHP 8.0+** y **MySQL / MariaDB**
- Navegador moderno

---

## Instrucciones de ejecución

### 1. Copiar el proyecto

Colocar la carpeta `prueba-tecnica` dentro de `htdocs`:

```
C:\xampp\htdocs\prueba-tecnica\
```

### 2. Crear la base de datos

En **phpMyAdmin** (`http://localhost/phpmyadmin`):

1. Crear una base de datos llamada `prueba_tecnica` con charset `utf8mb4`
2. Seleccionarla e importar el archivo `database/schema.sql`

### 3. Ejecutar el seed

Abrir en el navegador:

```
http://localhost/prueba-tecnica/database/seed.php
```

Esto crea los 5 usuarios de prueba con contraseñas hasheadas. Ejecutar **una sola vez**.

### 4. Acceder a la aplicación

```
http://localhost/prueba-tecnica/
```

---

## Credenciales de prueba

| Email | Contraseña | Rol |
|---|---|---|
| admin@universidad.cl | Admin1234 | admin |
| juan@universidad.cl | Usuario1234 | usuario |
| maria@universidad.cl | Usuario1234 | usuario |
| carlos@universidad.cl | Usuario1234 | usuario |
| ana@universidad.cl | Usuario1234 | usuario |

---

## Supuestos realizados

El enunciado describe los requerimientos funcionales pero deja abiertos varios aspectos de diseño. Los supuestos tomados fueron:

- **Autenticación**: el enunciado no la exige explícitamente, pero se consideró necesaria para poder diferenciar quién crea las solicitudes y quién las gestiona.
- **Roles**: se definieron dos roles — `admin` (revisa y actualiza estado) y `usuario` (crea y consulta sus propias solicitudes). Esta distinción es la mínima necesaria para que el flujo tenga sentido.
- **Aislamiento por usuario**: un usuario solo puede ver sus propias solicitudes. El admin tiene visibilidad total.
- **Campo comentario**: se añadió un campo opcional de comentario en el cambio de estado, para que el admin pueda entregar retroalimentación al solicitante. No estaba en el enunciado pero es una extensión natural del requerimiento 4.4.
- **Seed separado del schema**: las contraseñas se hashean en tiempo de ejecución mediante `seed.php` en lugar de estar hardcodeadas en el SQL, ya que almacenar hashes en un script versionado expone las contraseñas originales de forma innecesaria.
- **Entorno**: se asumió XAMPP en Windows como entorno de desarrollo local, con el proyecto accesible bajo `/prueba-tecnica/`.

---

## Decisiones técnicas relevantes

- **PHP vanilla sin frameworks**: se optó por no usar Laravel ni Symfony para demostrar comprensión directa del lenguaje, el protocolo HTTP y los patrones de diseño, sin depender de abstracciones.
- **Patrón MVC + Front Controller**: toda petición entra por `index.php`, que resuelve el controlador y la acción mediante parámetros GET. Esto centraliza el enrutamiento y evita puntos de entrada múltiples.
- **PDO con `EMULATE_PREPARES = false`**: desactivar la emulación de prepared statements fuerza al driver MySQL a usar prepared statements reales, eliminando una categoría entera de vulnerabilidades de inyección SQL.
- **bcrypt (`PASSWORD_BCRYPT`)**: algoritmo estándar para hashing de contraseñas. Incluye salt automático y es resistente a ataques de fuerza bruta por diseño.
- **`declare(strict_types=1)`**: activado en todos los archivos PHP para evitar conversiones de tipo implícitas que pueden ocultar errores lógicos.
- **`session_regenerate_id(true)` al login**: previene ataques de session fixation regenerando el ID de sesión tras una autenticación exitosa.
- **Whitelist de controladores**: el front controller valida el parámetro `controller` contra una lista de valores permitidos antes de instanciar cualquier clase, previniendo path traversal.

---

## Limitaciones de la solución

- **Sin paginación**: el listado carga todas las solicitudes que coinciden con los filtros. Con volúmenes grandes esto impactaría el rendimiento.
- **Sin recuperación de contraseña**: no existe flujo de "olvidé mi contraseña".
- **Sin tests automatizados**: no se incluyen tests unitarios ni de integración.
- **Sin HTTPS**: la aplicación está diseñada para un entorno local. En producción se requeriría HTTPS y cabeceras de seguridad adicionales (CSP, HSTS, etc.).
- **Gestión de usuarios manual**: no existe interfaz para crear o administrar usuarios; deben insertarse directamente via `seed.php` o SQL.
- **Sin logs de auditoría**: los cambios de estado no quedan registrados con historial; solo se conserva el valor más reciente.