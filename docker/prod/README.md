# 🚀 Guía de Despliegue SGDH en Google Cloud con Docker + Caddy

## 📋 Tabla de Contenidos
- [Arquitectura de Archivos](#arquitectura-de-archivos)
- [Requisitos Previos](#requisitos-previos)
- [Paso 1: Construir Imagen Docker](#paso-1-construir-imagen-docker)
- [Paso 2: Subir a Docker Hub](#paso-2-subir-a-docker-hub)
- [Paso 3: Configurar VPS Google Cloud](#paso-3-configurar-vps-google-cloud)
- [Paso 4: Desplegar Aplicación](#paso-4-desplegar-aplicación)
- [Troubleshooting](#troubleshooting)

---

## 📁 Arquitectura de Archivos

### Archivos en `docker/prod/` y su ubicación en despliegue

| Archivo | Descripción | Se usa en | Ubicación final |
|---------|-------------|-----------|-----------------|
| **Dockerfile** | Receta para construir imagen | 🏗️ BUILD (local) | No se sube al VPS |
| **docker-compose.yml** | Orquestación de servicios | ☁️ VPS | `~/sgdh-prod/docker-compose.yml` |
| **Caddyfile** | Configuración proxy HTTPS | ☁️ VPS | `~/sgdh-prod/Caddyfile` |
| **.env.production** | Variables de entorno | ☁️ VPS | `~/sgdh-prod/.env.production` |
| **nginx.conf** | Configuración Nginx principal | 🏗️ BUILD | Dentro de la imagen Docker |
| **default.conf** | Site config Nginx | 🏗️ BUILD | Dentro de la imagen Docker |
| **supervisord.conf** | Gestión procesos (PHP-FPM + Nginx) | 🏗️ BUILD | Dentro de la imagen Docker |
| **entrypoint.sh** | Script inicio contenedor | 🏗️ BUILD | Dentro de la imagen Docker |
| **README.md** | Esta guía | 📖 DOCS | Solo referencia |
| **DEPLOY-RAPIDO.md** | Guía rápida | 📖 DOCS | Solo referencia |

### 🎯 Flujo de archivos

```
FASE 1 - CONSTRUCCIÓN (Local/WSL)
==================================
Dockerfile          ─┐
nginx.conf          ─┤
default.conf        ─┼──> docker build ──> Imagen (josuem01/sgdh:latest) ──> Docker Hub
supervisord.conf    ─┤
entrypoint.sh       ─┘

FASE 2 - DESPLIEGUE (VPS Google Cloud)
=======================================
~/sgdh-prod/
├── docker-compose.yml   ← Subir al VPS
├── Caddyfile            ← Subir al VPS  
└── .env.production      ← Subir al VPS

Docker Hub ──> docker pull josuem01/sgdh:latest ──> VPS
```

---

## 🎯 Requisitos Previos

### En tu máquina local (Windows/WSL):
- ✅ Docker instalado
- ✅ Cuenta en Docker Hub: [hub.docker.com](https://hub.docker.com)
- ✅ Usuario configurado: **josuem01**

### En el VPS Google Cloud:
- ✅ Ubuntu 20.04+ con Docker y Docker Compose instalados
- ✅ Dominio configurado: **sgdh.systems** apuntando al IP del VPS
- ✅ Puertos abiertos: 80 (HTTP), 443 (HTTPS), 443/udp (HTTP/3)

---

## 📦 Paso 1: Construir Imagen Docker

### 1.1 Verificar archivos (todos en `docker/prod/`)

```bash
cd /home/josue/SGDH
ls -la docker/prod/

# Debes ver:
# Dockerfile ✓
# docker-compose.yml ✓ (image: josuem01/sgdh:latest)
# Caddyfile ✓ (dominio: sgdh.systems)
# .env.production ✓ (APP_URL=https://sgdh.systems, MySQL configurado)
# nginx.conf, default.conf, supervisord.conf, entrypoint.sh ✓
```

### 1.2 Construir la imagen

```bash
# Desde la raíz del proyecto SGDH
cd /home/josue/SGDH

# Construir imagen (5-10 minutos la primera vez)
docker build -f docker/prod/Dockerfile -t josuem01/sgdh:latest .

# Verificar que se creó
docker images | grep sgdh
```

**Salida esperada:**
```
josuem01/sgdh   latest   abc123def456   2 minutes ago   ~400MB
```

**¿Qué incluye la imagen?**
- ✅ PHP 8.2-FPM + extensiones (MySQL, GD, Redis, etc.)
- ✅ Nginx configurado para Laravel
- ✅ Supervisor para gestionar procesos
- ✅ Código compilado (Composer + NPM build)
- ✅ Permisos y optimizaciones aplicadas

---

## 🐳 Paso 2: Subir a Docker Hub

### 2.1 Login en Docker Hub

```bash
# Login (usa tu usuario: josuem01)
docker login

# Credenciales:
# Username: josuem01
# Password: [tu password de Docker Hub]
```

### 2.2 Subir la imagen

```bash
# La imagen ya tiene el tag correcto: josuem01/sgdh:latest
# Solo necesitas hacer push

docker push josuem01/sgdh:latest

# Esto tarda 5-10 minutos según tu conexión
```

**Progreso esperado:**
```
The push refers to repository [docker.io/josuem01/sgdh]
abc123: Pushed
def456: Pushed
...
latest: digest: sha256:xyz... size: 1234
```

**Verificar:** Abre [https://hub.docker.com/r/josuem01/sgdh](https://hub.docker.com/r/josuem01/sgdh) y verifica que aparezca tu imagen.

---

## ☁️ Paso 3: Configurar VPS Google Cloud

### 3.1 Conectar al VPS

```bash
# Desde WSL o PowerShell en tu máquina local
ssh usuario@IP_DEL_VPS

# O con SSH key:
ssh -i ~/.ssh/google_cloud_key usuario@IP_DEL_VPS
```

### 3.2 Preparar directorio en el VPS

```bash
# Ya dentro del VPS
mkdir -p ~/sgdh-prod
cd ~/sgdh-prod
```

### 3.3 Subir SOLO estos 3 archivos al VPS

**Archivos requeridos en `~/sgdh-prod/`:**
- ✅ `docker-compose.yml`
- ✅ `Caddyfile`
- ✅ `.env.production`

**Los demás archivos NO se suben** (ya están dentro de la imagen Docker):
- ❌ Dockerfile (solo se usó para build)
- ❌ nginx.conf, default.conf (dentro de la imagen)
- ❌ supervisord.conf, entrypoint.sh (dentro de la imagen)

**Método recomendado - SCP:**

```bash
# Desde WSL en tu máquina local (NO desde el VPS)
cd /home/josue/SGDH/docker/prod

# Subir los 3 archivos necesarios
scp docker-compose.yml usuario@IP_DEL_VPS:~/sgdh-prod/
scp Caddyfile usuario@IP_DEL_VPS:~/sgdh-prod/
scp .env.production usuario@IP_DEL_VPS:~/sgdh-prod/
```

**Alternativa - Copiar manualmente:**

```bash
# En el VPS, crear cada archivo con nano/vim
cd ~/sgdh-prod

nano docker-compose.yml    # Pega el contenido
nano Caddyfile             # Pega el contenido
nano .env.production       # Pega el contenido
```

### 3.4 Verificar archivos en el VPS

```bash
# En el VPS
cd ~/sgdh-prod
ls -la

# Debes ver SOLO:
# docker-compose.yml
# Caddyfile
# .env.production
```

### 3.4 Verificar archivos en el VPS

```bash
# En el VPS
cd ~/sgdh-prod
ls -la

# Debes ver SOLO:
# docker-compose.yml
# Caddyfile
# .env.production
```

### 3.5 Configurar firewall en Google Cloud

```bash
# Opción 1: Desde Google Cloud Console
# VPC Network → Firewall → Create Firewall Rule
# - Nombre: allow-http-https
# - Targets: All instances in the network
# - Source IP ranges: 0.0.0.0/0
# - Protocols and ports: tcp:80,443; udp:443

# Opción 2: Usando gcloud CLI
gcloud compute firewall-rules create allow-http-https \
    --allow tcp:80,tcp:443,udp:443 \
    --source-ranges 0.0.0.0/0 \
    --description "HTTP, HTTPS y HTTP/3 para SGDH"
```

### 3.6 Configurar dominio sgdh.systems

**En tu proveedor de dominios (GoDaddy, Namecheap, Cloudflare, etc.):**

1. Agregar registro **A**:
   - **Host/Name**: `@` (para sgdh.systems)
   - **Type**: A
   - **Value/Points to**: `[IP_DE_TU_VPS_GOOGLE_CLOUD]`
   - **TTL**: 3600

2. Agregar registro **A** para www (opcional):
   - **Host/Name**: `www`
   - **Type**: A
   - **Value**: `[IP_DE_TU_VPS_GOOGLE_CLOUD]`
   - **TTL**: 3600

**Verificar DNS (espera 5-30 minutos):**

```bash
# Desde tu máquina local
nslookup sgdh.systems

# Debe devolver el IP del VPS
# Server: 8.8.8.8
# Address: [IP_DE_TU_VPS]
```

---

## 🎬 Paso 4: Desplegar Aplicación

### 4.1 Descargar imagen desde Docker Hub

```bash
# En el VPS, dentro de ~/sgdh-prod
cd ~/sgdh-prod

# Descargar la imagen desde Docker Hub
docker pull josuem01/sgdh:latest
```

### 4.2 Iniciar base de datos

```bash
# Levantar solo MySQL primero
docker compose up -d mysql

# Esperar 15 segundos a que MySQL inicie
sleep 15

# Verificar que MySQL está corriendo
docker compose ps mysql
docker compose logs mysql | tail -20
```

### 4.3 Ejecutar migraciones (primera vez)

```bash
# Ejecutar migraciones desde un contenedor temporal
docker compose run --rm app php artisan migrate --force

# Si tienes seeders (opcional):
docker compose run --rm app php artisan db:seed --force
```

### 4.4 Levantar todos los servicios

```bash
cd ~/sgdh-prod

# Levantar app + Caddy en modo detached
docker compose up -d

# Ver logs en tiempo real (Ctrl+C para salir)
docker compose logs -f
```

### 4.5 Verificar servicios

```bash
# Ver estado de contenedores
docker compose ps

# Salida esperada:
# NAME           STATUS        PORTS
# sgdh_app       Up (healthy)  
# sgdh_mysql     Up (healthy)  3306/tcp
# sgdh_caddy     Up            80/tcp, 443/tcp, 443/udp

# Ver logs individuales
docker compose logs app --tail 50
docker compose logs caddy --tail 30
docker compose logs mysql --tail 20
```

### 4.6 Probar la aplicación

**Desde tu navegador:**

```
https://sgdh.systems
```

**Primera visita:**
- ⏱️ Caddy tarda 30-60 segundos obteniendo certificado SSL de Let's Encrypt
- 🔒 Si ves advertencia de certificado, espera 1-2 minutos y recarga (F5)
- ✅ Debes ver la página de login de SGDH

**Verificaciones:**

```bash
# En el VPS
curl -I https://sgdh.systems

# Debe devolver:
# HTTP/2 200
# server: Caddy
# ...

# Verificar redirección HTTP → HTTPS
curl -I http://sgdh.systems

# Debe devolver:
# HTTP/1.1 308 Permanent Redirect
# location: https://sgdh.systems/
```

---

## ⚙️ Comandos Útiles de Mantenimiento

### Ver logs
```bash
cd ~/sgdh-prod

# Todos los servicios (tiempo real)
docker compose logs -f

# Solo app
docker compose logs -f app

# Solo Caddy
docker compose logs -f caddy

# Últimas 100 líneas de todos
docker compose logs --tail=100

# Últimas 50 de app
docker compose logs app --tail=50
```

### Reiniciar servicios
```bash
# Reiniciar todo
docker compose restart

# Reiniciar solo app
docker compose restart app

# Reiniciar solo MySQL
docker compose restart mysql
```

### Actualizar aplicación (nueva versión)
```bash
cd ~/sgdh-prod

# 1. Descargar nueva imagen
docker pull josuem01/sgdh:latest

# 2. Recrear solo app (sin downtime de MySQL)
docker compose up -d --force-recreate app

# 3. Ejecutar migraciones si hay cambios
docker compose exec app php artisan migrate --force

# 4. Limpiar cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
```

### Backup de base de datos
```bash
# Crear backup
docker compose exec mysql mysqldump -u sgdh_user -p sgdh_production > backup_$(date +%Y%m%d_%H%M%S).sql

# Te pedirá la contraseña (la de .env.production DB_PASSWORD)

# Restaurar backup
docker compose exec -i mysql mysql -u sgdh_user -p sgdh_production < backup_20241117_153000.sql
```

### Limpiar sistema
```bash
# Ver uso de espacio
docker system df

# Limpiar imágenes antiguas
docker image prune -a

# ⚠️ PELIGRO: Detener y eliminar todo (incluye volúmenes/datos)
docker compose down -v  # Solo usar si quieres resetear TODO
```

### Ejecutar comandos artisan
```bash
# Cache config
docker compose exec app php artisan config:cache

# Optimizar autoload
docker compose exec app php artisan optimize

# Ver información de la app
docker compose exec app php artisan about

# Crear usuario admin
docker compose exec app php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@sgdh.systems', ...]);
```

---

