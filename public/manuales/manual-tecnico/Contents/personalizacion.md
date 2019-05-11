#Personalización
##Temas
Se tiene una separación en cuanto a la personalización de la interfaz de usuairo en cada instalación del etab. 
Para esto se utilizarán temas. El objetivo es que en cada instalación tome un tema base, 
se hagan los cambios necesarios y se active ese tema, sin cambiar nada de los temas existentes. 
Además si el tema es muy diferente (más allá de cambiar el logo) se puede agregar al repositorio 
y así ir creando un conjunto de temas disponibles. Existen 3 temas: **default**, **MINSAL_ESA**, y **simple**

### Temas de ejemplo
#### Tema default
En este tema no tiene imágenes relacionadas a una institución, el menú principal es vertical del lado izquierdo.

![Pantalla de ingreso, tema default](images/default-ingreso.png)

![Pantalla de ingreso, tema default](images/default-inicial.png)

#### Tema MINSAL_ESA
Es el tema utilizado en El Salvador, contiene imágenes que identifican al Ministerio de Salud, su menú es horizontal.

![Pantalla de ingreso, tema MINSAL_ESA](images/minsal-ingreso.png)

![Pantalla de ingreso, tema MINSAL_ESA](images/minsal-inicial.png)

### Crear un tema

1. En la ruta `public/themes` dentro del directorio del proyecto, crear un nuevo directorio 
(el nombre del directorio será el nombre del tema), este contendrá los archivos de imágenes, css y js.
2. Debe contener al menos los archivos que tenga el tema default, agregando además los nuevos 
que se vayan a utilizar.
![Estructura de un tema](images/estructura-tema.png) 

3. Crear el directorio del nuevo tema para contener la estructura (debe coincidir con el nombre del 
directorio creado para los las imágenes, estilos y js ) para modificar la estructura de la interfaz, 
en la ruta: `templates/themes`
4. Como en el caso anterior el tema default, nos indica los archivos mínimos que será obligatorio 
tener en nuestro nuevo tema
![Estructura de un tema](images/estructura2-tema.png) 

5. Para activar el nuevo tema, cambiar el parámetro `app.theme` en el archivo de configuración 
`config/app_config.yaml` (el nombre del tema será el nombre del directorio).
 ![Estructura de un tema](images/config-tema.png)


## Gestión de carga de orígenes de datos
Se está utilizando **Enqueue bundle** el cual permite integrar el componente **Enqueue**. 
Este último brindará  el servicio de mensajería pudiendo utilizar el protocolo de transporte que 
mejor nos parezca; entre los protocolos posibles tenemos:

- AMQP(s) based on PHP AMQP extension 
- AMQP based on bunny 
- AMQP(s) based on php-amqplib 
- Beanstalk 
- STOMP 
- Amazon SQS 
- Google PubSub 
- Kafka 
- Redis 
- Gearman 
- Doctrine DBAL 
- Filesystem 
- Mongodb 

El eTab utiliza por defecto, el protocolo de transporte basado en Redis por defecto. 
Y la configuración se pone en el archivo `.env.local` los datos para conectarse a nuestra 
instalación del servidor de redis, en la línea:

~~~
 ###> enqueue/redis ###
 ENQUEUE_DSN=redis://localhost
 ###< enqueue/redis ###
~~~

### Cambiar el protocolo de transporte
Si se desea utilizar/probar otro protocolo, por ejemplo RabbitMQ, se debe instalar el servidor 
de RabbitMQ, agregar la extensión para que PHP pueda leer el protocolo y el componente de Enqueue 
correspondiente al paquete. Y luego cambiar la variable de entorno en el archivo `.env.local` 


1. Instalar el servidor RabbitMQ, según la [guía oficial](http://getcomposer.org/)
2. Agregar la extensión de PHP 
~~~
 # apt-get install php-amqp
~~~
3. Instalar el componente de Enqueue para ese protocolo
~~~
 $ composer require enqueue/amqp-ext
~~~

4. Cambiar el archivo de configuración `.env.local`
~~~
 ###> enqueue/amqp ###
 ENQUEUE_DSN=amqp://guest:guest@localhost:5672/%2f/messages
 ###< enqueue/amqp ###
~~~

¡Eso sería todo! La dificultad estará en el costo de instalar el servidor del protocolo que 
deseemos probar, una vez hecho eso, en el eTab solo se debe agregar el paquete de enqueue 
correspondiente y cambiar una línea en el archivo `.env.local` 

### Activar las colas para la carga de datos
Independientemente de la implementación del protocolo la activación de las colas se hará 
con el siguiente comando:
~~~
 bin/console messenger:consume-messages
~~~

En el proceso de instalación, se configuró `supervisor` para que verifique cada 5 minutos si está activas las colas
y las carga si no lo están.

### Gestión manual de mensajes
Si se desea manipular los mensajes de carga de los orígenes de datos, por ejemplo para borrarlos 
y que ya no se tomen en cuenta, se deberá realizar de acuerdo al servidor del protocolo utilizado. 
Por ejemplo actualmente con RabbitMQ se ingresa a la interfaz web de administración de este y desde 
ahí se pueden borrar las colas o mensajes que estas tengan.

Con redis, podemos utilizar el cliente de redis o cualquier otro cliente gráfico que hayamos instalado 
(eso ya depende de cada instalación). 
Si se desea borrar todos los mensajes pendientes, de manera rápida, podemos utilizar el comando 
`bin/console redis:flushall` para borrar todos los datos en caché de redis. 
Esto incluye caché de indicadores mostrados en el tablero los cuales se volverán a generar al volver a 
utilizar un indicador en el tablero.

Si solo se desea borrar los mensajes correspondiente a la carga de datos, se realizan los siguientes pasos:

1. Ingresamos a la consola de administración de redis, comando `redis-cli`
![Consola Redis](images/redis-cli.png)

2. Ver si hay mensajes pendientes de procesar, comando `KEYS *`
![Redis keys](images/redis-keys.png)

3. Si hay mensajes veremos una llave con el nombre **messages**. Para borrar los mensajes usamos el 
comando `DEL`
![Redis DEL](images/redis-del.png)

4. Buscar los procesos del componente messenger que se estén ejecutando: `ps aux | grep messenger`
![ps aux](images/ps-aux.png)

5. Detener los procesos encontrados, utilizar el número de proceso encontrado en el comando anterior : 
`kill -9 3226`






