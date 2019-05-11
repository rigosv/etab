# Instalación del eTAB

## Requerimientos
* PostgreSQL 9.6+
* PHP 7.2+



## Instalación
### Instalación de los requerimientos desde un servidor Debian 
Es muy importante poner atención al indicador "#" significa que el comando 
debe ser ejecutado como usuario root y "$" que debe ser ejecutado como un usuario normal, 
en ambos casos desde una consola de comandos.

    # apt-get update
    # apt-get install php php-pgsql php-curl php-sqlite3 sqlite php-cli php-xsl php-intl  postgresql acl git-core curl postgresql-contrib php-ldap php-mysql php-sybase php-json php-bcmath php-mbstring redis-server php-redis php-zip php-gd wkhtmltopdf php-fpm composer supervisor php-acpu


### Crear usuario y directorio de trabajo
El directorio y usuario a utilizar pueden variar de acuerdo a los que se deseen elegir en cada instalación, 
como ejemplo se usará un usuario llamado *etab* y el directorio de instalación */var/www/etab*

    # adduser etab
    # mkdir /var/www/etab
    # chown etab:etab /var/www/etab
    # su etab
    $ cd /var/www


### Obtener el código fuente

    $ git clone https://github.com/rigosv/etab


A partir de este punto todos los comandos se deben ejecutar dentro de la carpeta en que se ha descargado 
el código fuente

### Instalar composer
[Composer](http://getcomposer.org/) Es una librería de PHP para el manejo de dependencias. 
Para instalarlo, dentro de la carpeta donde descargaste el código fuente se debe ejecutar:

    $ curl -s https://getcomposer.org/installer | php


## Configuración

### Configuración de Postgres

#### Editar archivo de configuración
Como usuario root realizar:

1. Editar el archivo */etc/postgresql/9.6/main/pg_hba.conf* (Verificar la ruta con la versión correspondiente) 
2. Cambiar la siguiente línea, sustituir la última palabra por *md5* 

    local   all             all                       md5


Reiniciar PostgreSQL

    # /etc/init.d/postgresql restart

#### Crear el usuario dueño de la base de datos y la estructura inicial

Se creará el usuario dueño de la base de datos, las opciones utilizadas dependerán de 
los criterios que se quieran seguir, se muestra un ejemplo, ejecutar *createuser --help* 
para la explicación de las opciones.

    # su postgres
    $ createuser -d -S  -R -P admin
    $ createdb etab -O admin
    $ exit
    # exit
    $ psql -d etab -f src/EstructuraDB/etab_initdb.sql -U admin


#### Configurar los parámetros de conexión

Configurar los valores correspondientes en el archivo **.env.local** 
(Si no existe copiar el contenido de .env: `cp .env .env.local` )


    DATABASE_URL=pgsql://[usuario]:[clave]@[IP_SERVIDOR]:[PUERTO]/[NOMBRE_DB]
        
    // Si los orígenes se guardarán en la misma base de datos, utilizar el mismo nombre de base de datos 
    DATABASE_ETAB_DATOS_URL=pgsql://[usuario]:[clave]@[IP_SERVIDOR]:[PUERTO]/[NOMBRE_DB_ORIGENES]


Configurar el parámetro "server_version" estableciendo la versión de postgres utilizada, en el archivo 
**config/packages/doctrine.yaml**


### Instalar todas las librerías necesarias

Si es un servidor de pruebas

    $ php composer.phar install


Si es un servidor de producción

    $ php composer install --no-dev --optimize-autoloader



### Configurar el supervisor de gestión de colas
crear el archivo etab_colas.conf


    # nano  /etc/supervisor/conf.d/etab_colas.conf

Poner el siguiente contenido en el archivo (verificar la ruta y usuario con que se ha instalado el etab y 
sustituir en este archivo, en el ejemplo se asume que está instalado en /var/www/etab con el usuario admin )

    [code]
    [program:pf_message_consumer]
    command=/var/www/etab/bin/console messenger:consume-messages --env=prod --no-debug --time-limit=300
    user=admin
    process_name=%(program_name)s_%(process_num)02d
    numprocs=1
    autostart=true
    autorestart=true
    startsecs=0
    redirect_stderr=true


reiniciar el supervisor

    # /etc/init.d/supervisor restart


### Permisos sobre carpetas
Es necesario tener [soporte para ACL](https://help.ubuntu.com/community/FilePermissionsACLs) en la 
partición en que está el proyecto y luego ejecutar

    $ setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs web/uploads
    $ setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs web/uploads


### Crear el usuario administrador de la aplicación

    $ bin/console fos:user:create --super-admin


### Ejecutando el etab
En producción, se debe usar un servidor web como Nginx o Apache, puede ver el siguiente enlace para 
[configurar un servidor web](https://symfony.com/doc/current/setup/web_server_configuration.html)
Para desarrollo o una verificación rápida se puede usar un servidor web local, activándolo de la siguiente forma

    $ bin/console server:start

Abra el navegador web y cargue la ruta `http://localhost:8000/`

### Orígenes de datos desde MSSQLServer
Si se leerá orígenes de datos desde MSSQLServer, instalar los controladores de Microsoft, según la siguiente guía
[MSSQLServer](https://docs.microsoft.com/en-us/sql/connect/php/installation-tutorial-linux-mac?view=sql-server-2017)
