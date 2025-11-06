# Sistema de Gestión de Inventario Farmacéutico Hospitalario

## Descripción del Proyecto

Este proyecto consiste en el desarrollo de un sistema integral para la gestión del inventario de la farmacia central de un hospital. El sistema tiene como objetivo digitalizar y controlar el flujo completo de medicamentos y material de curación, desde su compra a proveedores hasta su entrega a las distintas áreas del hospital, asegurando la trazabilidad, el control de caducidades y la generación de reportes.

El sistema se compone de dos plataformas interconectadas:

1.  **Aplicación Web (Backend & Frontend Administrativo):** Desarrollada en **Laravel** (usando Laravel Sail y desplegada en Docker sobre un VPS de Google Cloud). Permite a los administradores de farmacia gestionar el catálogo, registrar compras, controlar lotes y caducidades, configurar dotaciones diarias y generar reportes PDF.
2.  **Aplicación Móvil:** Desarrollada en **Android Studio**. Permite al personal de las áreas del hospital (Urgencias, Pisos, Quirófano, etc.) realizar solicitudes extraordinarias de material cuando su dotación diaria es insuficiente.

---

## Flujos Principales

### 1. Abastecimiento (Web)
El administrador registra la entrada de productos al almacén provenientes de un proveedor. Se crea un registro detallado del **lote**, incluyendo fecha de caducidad y cantidad recibida.

### 2. Surtido Diario (Web)
El sistema gestiona una **"Dotación"** predefinida para cada área hospitalaria. Diariamente, el administrador revisa el stock remanente en cada área y surte únicamente la cantidad necesaria para completar dicha dotación, registrando la salida del inventario (FIFO - First In, First Out, priorizando lotes próximos a caducar).

### 3. Solicitud Extraordinaria (Móvil & Web)
Si un área agota su dotación antes del siguiente surtido, el personal utiliza la app móvil para generar una **"Solicitud"** de material extra, justificando el motivo. El administrador recibe, revisa y aprueba/rechaza la solicitud desde la web. Si se aprueba, se registra la entrega y salida del almacén.

---

## Autenticación y Seguridad

El sistema implementa un modelo de seguridad estricto para garantizar que solo el personal autorizado del hospital pueda acceder.

### 1. Gestión de Usuarios (Solo Admin)
* **Registro Restringido:** La ruta de registro público (`/register`) está deshabilitada. Solo los administradores pueden crear nuevas cuentas.
* **Invitación:** El administrador crea el registro del usuario en la base de datos proporcionando únicamente su `email`, `nombre` y `rol`.
* **Contraseña Inicial:** El campo `password` se establece como `NULL` inicialmente. El administrador nunca conoce ni asigna contraseñas.

### 2. Inicio de Sesión (Google & Contraseña)
* **Login con Google (Socialite):** Es el método principal de acceso. El sistema verifica si el email de la cuenta de Google ya existe en la base de datos.
    * **Si existe:** Se permite el acceso.
    * **Si NO existe:** Se deniega el acceso con un mensaje de error (usuario no autorizado).
* **Establecer Contraseña:** Una vez que el usuario accede por primera vez con Google, puede establecer su contraseña personal desde su perfil (o usar el flujo de "Olvidé mi contraseña"). Esto asegura que solo el usuario conozca sus credenciales.

---

## Modelo de Datos (Entidades y Propiedades)

**Nota sobre Auditoría:** La mayoría de las tablas principales incluyen un campo `last_modified_by_user_id` para registrar al último usuario que realizó cambios, garantizando la trazabilidad de las operaciones.

### 1. Catálogos Principales

#### `usuarios`
*Registra a todas las personas que interactúan con el sistema.*
* `id` (PK): Identificador único.
* `name`: Nombre completo.
* `email`: Correo electrónico (clave para login con Google).
* `password`: Contraseña cifrada (puede ser NULL inicialmente).
* `google_id`: ID único de Google (para vincular la cuenta Socialite).
* `rol`: Define permisos (ej. 'admin_farmacia', 'personal_area').
* `area_id` (FK): Relación opcional con `areas` para usuarios móviles.
* *(Timestamps)*

#### `areas`
*Departamentos físicos del hospital.*
* `id` (PK): Identificador único.
* `nombre`: Nombre del área (ej. Urgencias).
* `responsable`: Nombre del encargado (opcional).
* `last_modified_by_user_id` (FK): Auditoría.

#### `proveedores`
*Empresas que suministran los productos.*
* `id` (PK): Identificador único.
* `no_proveedor`: Número interno.
* `rfc`: Registro Federal de Contribuyentes.
* `razon_social`: Nombre legal.
* `direccion`, `telefono`, `correo`, `pagina_web`, `representante`.
* `estatus`: Activo/Inactivo.
* `last_modified_by_user_id` (FK): Auditoría.

#### `productos`
*Catálogo maestro de medicamentos y materiales.*
* `id` (PK): Identificador único.
* `clave`: Código interno/universal.
* `descripcion`: Nombre detallado.
* `presentacion`: (ej. "Caja c/10 tabletas").
* `cuadro_basico`: Booleano.
* `stock_min`, `stock_max`, `stock_optimo`: Niveles de inventario.
* `last_modified_by_user_id` (FK): Auditoría.

### 2. Inventario y Reglas

#### `lotes` (Inventario Físico)
*Registro de entradas con caducidad específica.*
* `id` (PK): Identificador único.
* `producto_id` (FK): Relación con `productos`.
* `proveedor_id` (FK): Quién lo vendió.
* `numero_lote`: Código del fabricante.
* `fecha_caducidad`: Fecha crítica de control.
* `cantidad_recibida`: Cantidad original.
* `cantidad_actual`: **Stock real disponible** de este lote.
* `fecha_compra`: Fecha de adquisición.
* `last_modified_by_user_id` (FK): Auditoría.

#### `dotaciones` (Reglas de Surtido)
*Configuración de stock diario por área.*
* `id` (PK): Identificador único.
* `area_id` (FK): Área asignada.
* `producto_id` (FK): Producto asignado.
* `cantidad_diaria`: Cantidad máxima diaria.

### 3. Operaciones y Movimientos

#### `solicitudes` (Pedidos Móviles)
*Cabecera de pedidos extraordinarios.*
* `id` (PK): Identificador único.
* `area_id` (FK): Área solicitante.
* `usuario_solicitante_id` (FK): Usuario creador.
* `fecha_solicitud`: Timestamp.
* `justificacion`: Motivo del pedido.
* `estatus`: 'Pendiente', 'Aprobada', 'Rechazada', 'Surtida'.
* `last_modified_by_user_id` (FK): Auditoría (quién aprobó en web).

#### `solicitudes_detalles`
*Renglones de cada solicitud.*
* `id` (PK): Identificador único.
* `solicitud_id` (FK): Relación con cabecera.
* `producto_id` (FK): Producto solicitado.
* `cantidad_solicitada`: Cantidad requerida.

#### `entregas` (Salidas de Almacén)
*Registro de salidas de material.*
* `id` (PK): Identificador único.
* `tipo_entrega`: 'Surtido Diario' o 'Solicitud Extraordinaria'.
* `area_id` (FK): Destino.
* `usuario_entrega_id` (FK): Admin que entregó.
* `fecha_entrega`: Timestamp de salida.
* `solicitud_id` (FK): Opcional (si es extraordinaria).

#### `entrega_detalles` (Trazabilidad)
*Vincula salidas con lotes específicos.*
* `id` (PK): Identificador único.
* `entrega_id` (FK): Relación con entrega.
* `lote_id` (FK): **Lote del que se descontó el producto**.
* `cantidad_entregada`: Cantidad real salida de ese lote.