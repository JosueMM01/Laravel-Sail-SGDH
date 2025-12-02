# Resumen funcional de la app móvil

Este documento explica qué puede hacer la aplicación móvil de SGDH, qué roles se habilitaron y a qué recursos de la API tienen acceso.

## Alcance general

- La app móvil está orientada al personal operativo de las áreas hospitalarias para consultar dotaciones, registrar solicitudes extraordinarias y recibir notificaciones de estatus.
- Todo el tráfico usa la API REST documentada en `docs/api-mobile-contract.md` y exige autenticación `Bearer` emitida por Sanctum.
- Solo los roles `personal_area` y `jefe_area` pueden generar tokens móviles; otros roles (administradores, farmacia, etc.) trabajan desde la web.

## Roles y habilidades expuestas

| Rol | Propósito en la app | Habilidades | Comentarios |
| --- | --- | --- | --- |
| `personal_area` | Personal operativo que solicita reposiciones | `solicitudes:view`, `solicitudes:create`, `notifications:view`, `productos:view`, `dotacion:view` | Puede consultar productos/dotaciones de su área, crear solicitudes y revisar notificaciones propias. |
| `jefe_area` | Responsable de validar solicitudes antes de enviarlas a farmacia | `solicitudes:view`, `solicitudes:approve`, `notifications:view`, `productos:view` | Comparte vistas de catálogo, no crea solicitudes nuevas pero sí puede avanzar el flujo a `pendiente_farmacia`. |

> Las políticas internas restringen todos los listados al `area_id` del usuario autenticado.

## Módulos disponibles en la app

### Autenticación
- **Inicio de sesión:** `POST /api/auth/token` con email, password y un nombre opcional del dispositivo.
- **Cierre de sesión:** `DELETE /api/auth/token`, invalida el token actual.
- La respuesta inicial incluye las habilidades y los datos básicos del usuario para que la app defina qué pantallas habilitar.

### Catálogo y dotaciones
- **Listado:** `GET /api/productos` con filtros opcionales `search` y `per_page`.
- **Datos entregados:** ficha del producto, cantidades de dotación configuradas y `stock_disponible` calculado en tiempo real.
- `personal_area` y `jefe_area` comparten este módulo; `dotacion:view` solo aplica al personal.

### Solicitudes extraordinarias
- **Consulta:** `GET /api/solicitudes` y `GET /api/solicitudes/{id}` muestran únicamente solicitudes del área del usuario o creadas por él.
- **Creación:** solo `personal_area` mediante `POST /api/solicitudes`, estatus inicial `pendiente_jefe`.
- **Actualización de estatus:** `PATCH /api/solicitudes/{id}/estatus`, disponible para `jefe_area` con la habilidad `solicitudes:approve` para avanzar la solicitud hacia farmacia.
- Las validaciones y mensajes de error siguen las reglas de negocio del backend (`403` por falta de habilidad, `422` por datos incompletos, etc.).

### Notificaciones
- **Listado:** `GET /api/notifications` con filtros `unread` y `limit`.
- **Marcar como leída:** `PATCH /api/notifications/{notification}`.
- Notifica cambios de estatus de solicitudes y otros eventos relevantes para el área.

### Perfil de usuario
- La API móvil **no** expone endpoints para editar o ver un perfil extendido como en la web (`/profile`).
- Los datos de perfil que recibe la app provienen únicamente de la respuesta del token (id, nombre, email, rol y `area_id`). No se puede cambiar contraseña ni datos personales desde la app.

## Qué no hace la app móvil
- No administra usuarios, inventarios ni reportes avanzados; esas funciones permanecen exclusivas del portal web administrativo.
- No existen vistas de auditoría ni descargas de reportes.
- No permite gestionar configuración de áreas ni aprobar entregas físicas; el alcance se limita al ciclo de solicitudes y consultas de dotación.

## Recomendaciones para nuevos desarrollos móviles
- Reutilizar las habilidades (`abilities`) para controlar la UI sin duplicar lógica de permisos.
- Validar siempre que el usuario tenga un `area_id` antes de presentar módulos de catálogo o solicitudes (la API retorna `422` si falta).
- Manejar la revocación de tokens al cerrar sesión o al detectar `401/403` para mantener la seguridad en dispositivos compartidos.
