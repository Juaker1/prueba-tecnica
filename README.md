# Módulo de Gestión de Solicitudes Administrativas

Solución desarrollada como prueba técnica.
Permite a usuarios internos registrar solicitudes administrativas y a administradores revisarlas, gestionarlas y dejar retroalimentación.

---

## Requisitos

- XAMPP (o equivalente) con **PHP 8.0+** y **MySQL**
- Navegador moderno

---

## Instrucciones de ejecución

### 1. Obtener el proyecto

Clonar el repositorio o copiar la carpeta del proyecto dentro de `htdocs` de XAMPP:

```bash
git clone <url-del-repositorio> C:\xampp\htdocs\prueba-tecnica
```

O copiar manualmente la carpeta, de modo que quede en:

```
C:\xampp\htdocs\prueba-tecnica\
```

### 2. Iniciar XAMPP

Abrir el **Panel de Control de XAMPP** y hacer clic en **Start** para los módulos **Apache** y **MySQL**. Ambos deben quedar en verde antes de continuar.

### 3. Crear la base de datos

1. Abrir **phpMyAdmin** en el navegador: `http://localhost/phpmyadmin`
2. En el panel izquierdo, hacer clic en **Nueva** (o "New")
3. Escribir `prueba_tecnica` como nombre, seleccionar `utf8mb4_unicode_ci` como cotejamiento y hacer clic en **Crear**
4. Con la base de datos seleccionada, ir a la pestaña **Importar**
5. Hacer clic en **Seleccionar archivo**, elegir `database/schema.sql` del proyecto y hacer clic en **Continuar**

> Alternativa: ir a la pestaña **SQL**, pegar el contenido del archivo `database/schema.sql` y hacer clic en **Continuar**.

### 4. Crear los usuarios iniciales (seed)

**Opción A — Navegador:**

Abrir la siguiente URL (XAMPP debe estar corriendo):

```
http://localhost/prueba-tecnica/database/seed.php
```

**Opción B — Terminal:**

```bash
C:\xampp\php\php.exe C:\xampp\htdocs\prueba-tecnica\database\seed.php
```

Ambas opciones crean los 5 usuarios de prueba con contraseñas hasheadas. Ejecutar **una sola vez**.

### 5. Acceder a la aplicación

Abrir en el navegador:

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

- **Autenticación**: Se consideró necesaria para poder diferenciar quién crea las solicitudes y quién las gestiona.
- **Roles**: se definieron dos roles — `admin` el cual revisa y actualiza el estado de la solicitud y `usuario` que crea y consulta sus propias solicitudes.
- **Aislamiento por usuario**: un usuario solo puede ver sus propias solicitudes. El admin tiene visibilidad total.
- **Campo comentario**: se añadió un campo opcional de comentario en el cambio de estado, para que el admin pueda entregar retroalimentación al solicitante.
- **Seed separado del schema**: las contraseñas se hashean en tiempo de ejecución mediante `seed.php` en lugar de estar hardcodeadas en el SQL.
- **Entorno**: se asumió XAMPP en Windows como entorno de desarrollo local, con el proyecto accesible bajo `/prueba-tecnica/`.

---

## Decisiones técnicas relevantes

- **PHP vanilla sin frameworks**: se optó por no usar frameworks para demostrar comprensión directa del lenguaje, el protocolo HTTP y los patrones de diseño.
- **Patrón MVC + Front Controller**: toda petición entra por `index.php`, que resuelve el controlador y la acción mediante parámetros GET. Esto centraliza el enrutamiento y evita puntos de entrada múltiples.
- **PDO con `EMULATE_PREPARES = false`**: desactivar la emulación de prepared statements fuerza al driver MySQL a usar prepared statements reales, eliminando posibilidad de vulnerabilidades de inyección SQL.
- **bcrypt (`PASSWORD_BCRYPT`)**: algoritmo estándar para hashing de contraseñas. Incluye salt automático y es resistente a ataques de fuerza bruta por diseño.
- **`declare(strict_types=1)`**: activado en todos los archivos PHP para evitar conversiones de tipo implícitas que pueden ocultar errores lógicos.
- **`session_regenerate_id(true)` al login**: previene ataques de session fixation regenerando el ID de sesión tras una autenticación exitosa.
- **Whitelist de controladores**: el front controller valida el parámetro `controller` contra una lista de valores permitidos antes de instanciar cualquier clase, previniendo la inclusión de archivos no autorizados y asegurando que solo se ejecuten componentes definidos.

---

## Limitaciones de la solución

- **Sin paginación**: el listado carga todas las solicitudes que coinciden con los filtros. Con volúmenes grandes esto impactaría el rendimiento.
- **Sin recuperación de contraseña**: no existe flujo de "olvidé mi contraseña".
- **Sin HTTPS**: la aplicación está diseñada para un entorno local. En producción se requeriría HTTPS y cabeceras de seguridad adicionales.
- **Gestión de usuarios manual**: no existe interfaz para crear o administrar usuarios.
- **Sin logs de auditoría**: los cambios de estado no quedan registrados con historial; solo se conserva el valor más reciente.
- **Sin expiración automática de sesión**: la sesión no expira por inactividad; dura hasta que el usuario cierra sesión manualmente o cierra el navegador.
- **Filtrado manual con resultados limitados**: los filtros aplican en tiempo real sobre las filas ya cargadas en la página (filtrado client-side). Si el número de solicitudes es muy grande, la carga inicial de la página se vería afectada, ya que el servidor genera todo el HTML de una vez.
- **Sin protección CSRF**: los formularios POST no implementan tokens CSRF. En producción esto sería obligatorio para prevenir que sitios externos puedan enviar peticiones en nombre de un usuario autenticado.