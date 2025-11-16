# Contrato de API Móvil

Esta especificación resume los endpoints REST disponibles para la aplicación móvil.

## Autenticación

### Flujo general de uso
1. El cliente móvil solicita un token con credenciales válidas (`POST /api/auth/token`).
2. El backend responde con el token y la lista de habilidades disponibles para el usuario autenticado.
3. El cliente guarda el token de forma segura y lo usa en la cabecera `Authorization: Bearer {token}` para todas las llamadas subsecuentes.
4. Cuando el usuario cierre sesión, el cliente revoca el token (`DELETE /api/auth/token`).

### POST /api/auth/token
- **Descripción:** Genera un token de acceso personal (Bearer) para usuarios de roles `personal_area` o `jefe_area`.
- **Body:**
  ```json
  {
    "email": "usuario@hospital.test",
    "password": "secreto",
    "device_name": "Pixel-8" // opcional
  }
  ```
- **Respuestas:**
  - `200 OK`
    ```json
    {
      "token": "{plain-text-token}",
      "token_type": "Bearer",
      "abilities": ["solicitudes:view", "solicitudes:create", "notifications:view", "productos:view", "dotacion:view"],
      "user": { "id": 5, "name": "Nombre", "email": "...", "rol": "personal_area", "area_id": 3 }
    }
    ```
  - `422 Unprocessable Content` Credenciales inválidas, usuario inactivo o rol no autorizado.
  - **Notas:**
    - `personal_area` recibe las habilidades `solicitudes:view`, `solicitudes:create`, `notifications:view`, `productos:view` y `dotacion:view`.
    - `jefe_area` recibe `solicitudes:view`, `solicitudes:approve`, `notifications:view` y `productos:view`.
    - El campo `device_name` es opcional y se utiliza para identificar el token en el registro de dispositivos.

### DELETE /api/auth/token
- **Auth:** `Bearer {token}`
- **Descripción:** Revoca el token actual.
- **Respuesta:** `204 No Content`

## Resumen de roles y habilidades

| Rol            | Habilidades expuestas                                                                 |
|----------------|----------------------------------------------------------------------------------------|
| `personal_area`| `solicitudes:view`, `solicitudes:create`, `notifications:view`, `productos:view`, `dotacion:view` |
| `jefe_area`    | `solicitudes:view`, `solicitudes:approve`, `notifications:view`, `productos:view`      |

Las políticas internas del backend combinan estas habilidades con el `area_id` del usuario para filtrar datos sensibles
como las dotaciones y las solicitudes de su área.

## Catálogo de productos asignados

> Requiere `auth:sanctum` y la habilidad `productos:view`. El listado se limita a los productos con dotación configurada para el área del usuario autenticado.

### GET /api/productos
- **Query params opcionales:**
  - `search`: texto libre que filtra por clave o descripción.
  - `per_page`: tamaño de página (1-100, por defecto 50).
- **Encabezados obligatorios:** `Authorization: Bearer {token}`.
- **Campos devueltos:**
  - `producto`: datos del producto con `image_url` apuntando al recurso público.
  - `dotacion`: cantidades configuradas para el área.
  - `stock_disponible`: suma de lotes vigentes calculada en tiempo real.
- **Respuesta 200:** Paginación estándar de Laravel con elementos `DotacionProductoResource`:
  ```json
  {
    "data": [
      {
        "producto": {
          "id": 12,
          "clave": "MED-001",
          "descripcion": "Paracetamol 500mg",
          "presentacion": "Caja con 20 tabletas",
          "cuadro_basico": true,
          "image_url": "https://app.test/storage/productos/med-001.jpg",
          "stock_disponible": 35
        },
        "dotacion": {
          "cantidad_diaria": 15
        }
      }
    ],
    "links": { ... },
    "meta": { ... }
  }
  ```
- **Errores:**
  - `403 Forbidden` si el token carece de la habilidad `productos:view`.
  - `422 Unprocessable Content` si el usuario no tiene un `area_id` asignado.

## Solicitudes extraordinarias

> Todos los endpoints requieren `auth:sanctum` y la habilidad `solicitudes:view`. Para crear, también `solicitudes:create`.

### GET /api/solicitudes
- **Query params opcionales:**
  - `estatus`: `pendiente_jefe`, `pendiente_farmacia`, `aprobada`, `rechazada`, `surtida`
  - `per_page`: tamaño de página (1-100)
- **Respuesta 200:** Paginación estándar de Laravel con colecciones de `SolicitudResource`:
  ```json
  {
    "data": [
      {
        "id": 10,
        "estatus": { "value": "pendiente_jefe", "label": "Pendiente de jefe de área" },
        "justificacion": "Urgente",
        "fecha_solicitud": "2025-11-11T12:00:00Z",
        "area": { "id": 3, "nombre": "Urgencias" },
        "usuario_solicitante": { "id": 7, "nombre": "María" },
        "detalles": [
          { "id": 1, "producto": { "id": 5, "clave": "MED-001", "descripcion": "Paracetamol" }, "cantidad_solicitada": 4 }
        ],
        "actualizada_en": "2025-11-11T12:10:00Z"
      }
    ],
    "links": { ... },
    "meta": { ... }
  }
  ```

### GET /api/solicitudes/{id}
- **Descripción:** Devuelve el detalle de una solicitud accesible según políticas (misma área o creada por el usuario).
- **Respuesta 200:** `SolicitudResource`.
- **Errores:** `403` si el usuario no puede verla, `404` si no existe.

### POST /api/solicitudes
- **Habilidad requerida:** `solicitudes:create`.
- **Body:**
  ```json
  {
    "justificacion": "Reposición de material",
    "detalles": [
      { "producto_id": 12, "cantidad_solicitada": 5 },
      { "producto_id": 19, "cantidad_solicitada": 2 }
    ]
  }
  ```
- **Notas:**
  - El `area_id` se infiere del usuario autenticado.
  - El estatus inicial es `pendiente_jefe`.
- **Respuestas:**
  - `201 Created` + `SolicitudResource` con la solicitud recién registrada.
  - `422 Unprocessable Content` si falta justificación, detalles o el usuario no tiene área asignada.

### PATCH /api/solicitudes/{id}/estatus
- **Habilidad requerida:** `solicitudes:approve`.
- **Roles válidos:** `jefe_area` (super admin también puede usarlo si genera un token manualmente).
- **Body:**
  ```json
  {
    "estatus": "pendiente_farmacia",
    "motivo_rechazo": "Datos incompletos" // obligatorio solo cuando estatus = "rechazada"
  }
  ```
- **Descripción:** Permite avanzar o revertir el flujo de la solicitud según las transiciones permitidas para el rol identificado en el token. Por ejemplo, el jefe de área solo puede enviarla a `pendiente_farmacia` y no puede rechazarla.
- **Respuestas:**
  - `200 OK`
    ```json
    {
      "message": "Solicitud enviada a revisión de farmacia.",
      "data": { ...SolicitudResource }
    }
    ```
  - `403 Forbidden` si el token no tiene la habilidad requerida o las políticas rechazan la transición.
  - `422 Unprocessable Content` si el estatus solicitado no es válido para el estado actual o la solicitud ya fue surtida.
  - `404 Not Found` si la solicitud no existe o no es visible para el usuario.

## Notificaciones

> Requiere la habilidad `notifications:view`.

### GET /api/notifications
- **Query opcional:** `unread=true` para filtrar solo no leídas, `limit` (1-100, por defecto 25).
- **Respuesta 200:**
  ```json
  {
    "data": [
      {
        "id": "uuid",
        "type": "SolicitudStatusUpdated",
        "data": { "solicitud_id": 10, ... },
        "read_at": null,
        "created_at": "2025-11-11T12:30:00Z"
      }
    ],
    "meta": { "unread_count": 3 }
  }
  ```

### PATCH /api/notifications/{notification}
- **Descripción:** Marca una notificación como leída.
- **Respuesta 200:** Notificación actualizada (`read_at` distinto de `null`).
- **Errores:** `404` si no pertenece al usuario.

## Convenciones generales

- Autenticación vía encabezado `Authorization: Bearer {token}`.
- Todas las respuestas usan JSON y timestamps ISO 8601 (UTC).
- Códigos de error incluyen mensaje localizado en español.
- Los tokens exponen habilidades (`abilities`) que se usan para validar acceso en cada endpoint.
