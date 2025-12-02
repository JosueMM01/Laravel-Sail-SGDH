
-----------------------------------------------------------------------
-----------------------------------------------------------------------

1. El Alias de Sail (¡El más importante!)
Para evitar escribir ./vendor/bin/sail cada vez, usa este alias. Debes ejecutarlo cada vez que abras una nueva terminal en este proyecto.

alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
Pro-tip: Para hacerlo permanente, agrégalo a tu archivo ~/.bashrc en Ubuntu.

------IMPORTANTE, EST PASO YA NO SE HARA PORQUE SE AGREGO:

 echo "alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'" >> ~/.bashrc 
 
------ASI QUE CUALQUIER TERMINAL NUEVA YA TENDRA EL ALIAS

-----------------------------------------------------------------------

2. ⚙️ Arranque y Gestión del Entorno
Iniciar TODO (Backend y Frontend)
Este es tu comando principal de inicio. Levanta los contenedores de Docker (servidor, BD) en segundo plano y luego inicia el servidor de Vite (CSS/JS) en esta misma terminal.

sail npm start
¡IMPORTANTE! Esta terminal se quedará "observando" los cambios. Debes dejarla abierta mientras trabajas.

-----------------------------------------------------------------------

Detener el entorno
Para detener los contenedores (servidor web y base de datos) cuando termines de trabajar.

sail stop

sail stop
(También puedes usar sail down para un apagado más completo que elimina los contenedores).

-----------------------------------------------------------------------

Reiniciar el entorno
Simplemente reinicia los contenedores.

sail restart

-----------------------------------------------------------------------
-----------------------------------------------------------------------

3. 💻 Comandos Diarios (en una 2da Terminal)
Como tu primera terminal está ocupada con sail npm start, debes abrir una segunda terminal (haz clic en el + en VS Code) para todos los demás comandos.

-----------------------------------------------------------------------
Comandos de Artisan (El corazón de Laravel)
Todos los comandos php artisan se ejecutan con sail artisan.

# Ejecutar las migraciones de la base de datos
sail artisan migrate

# Crear un nuevo modelo (y migración)
sail artisan make:model NombreModelo -m

# Crear un nuevo controlador
sail artisan make:controller NombreController --resource

# Ver todas las rutas de la aplicación
sail artisan route:list
Comandos de Composer (Paquetes de PHP)
Para instalar o actualizar paquetes de PHP.

-----------------------------------------------------------------------

# Instalar un nuevo paquete
sail composer require nombre/paquete

# Actualizar todos los paquetes
sail composer update
Comandos de NPM (Paquetes de JS/CSS)
Para instalar o actualizar paquetes de frontend sin correr el servidor.

-----------------------------------------------------------------------

# Instalar un nuevo paquete de frontend
sail npm install nombre-paquete

# Instalar todos los paquetes definidos en package.json
sail npm install

-----------------------------------------------------------------------
-----------------------------------------------------------------------

4. 💾 Comandos de Base de Datos
Conectarse a la Base de Datos
Te abre una línea de comandos interactiva dentro de tu base de datos MySQL.

sail artisan db

-----------------------------------------------------------------------

Resetear la Base de Datos
¡CUIDADO! Esto borra TODA tu base de datos y vuelve a ejecutar todas las migraciones. Es perfecto para empezar de cero.

sail artisan migrate:fresh

-----------------------------------------------------------------------

Resetear y poblar la Base de Datos
Igual que el anterior, pero además ejecuta los "Seeders" (para llenar la BD con datos de prueba).

sail artisan migrate:fresh --seed

-----------------------------------------------------------------------

Consola Interactiva (Tinker)
Inicia una consola de PHP interactiva con tu aplicación de Laravel cargada. Es genial para probar cosas rápido.

sail artisan tinker

-----------------------------------------------------------------------
-----------------------------------------------------------------------

5. 🛠️ Solución de Problemas
Ver los contenedores activos
Muestra los contenedores que sail está ejecutando (similar a docker ps).

sail ps

-----------------------------------------------------------------------

Reconstruir los contenedores
Si hiciste cambios en el Dockerfile o si algo se corrompió, esto fuerza a Docker a reconstruir las imágenes desde cero.

sail build --no-cache

-----------------------------------------------------------------------

Ver los logs (registros)
Muestra los registros (logs) en tiempo real de todos tus contenedores. Esencial si tu aplicación "crashea" y solo ves una página en blanco.

sail logs -f

-----------------------------------------------------------------------
-----------------------------------------------------------------------
