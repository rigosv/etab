# Matriz Configuración

<p style="text-align: justify;">
El SIIG/eTAB es una herramienta que propone proveer información y datos presentando estos de una manera accesible y objetiva, utilizando visualizaciones gráficas de diferentes tipos (gráficas, mapas, tablas interactivas) que buscará distribuir y proveer información de diferentes programas de forma unificada. Por lo tanto, el eTAB será alimentado por los datos obtenidos a partir de la información administrativa producida por el sistema estatal y nacional de información de salud.
</p>

<br>
## Objetivo
<br>

<p style="text-align: justify;">
El siguiente manual es para explicar el uso de la herramienta web eTAB. 
El eTAB en línea es construido para mostrar graficamente la información concentrada de diferentes fuentes de datos
</p>

<br>
## Matriz de seguimiento
<br>

<p style="text-align: justify;">
La matriz de seguimiento esta constituido por una webform para captura de datos de diferentes fuentes para poder hacer una comparativa con una planeación de un indicador y asi poder ver de manera grafica y en un mapa de calor el avance para lograr el objetivo.
</p>
![](reporte.png)

<br>
## Nuevo o Editar Matriz
<br>

<p style="text-align: justify;">
El siguiente formulario contiene todos los campos y opciones para crear la matriz de seguimiento.
a continuacion se describen las acciones generales:
</p>
![](configuracion-1.png)

<br>
>**Formulario de configuracion**

> - 1.- Titulo del modulo (la palabra Editar cambi a anuevo segun sea el caso)
> - 2.- Acciones (Solo disponibles para edición)
> - 3.- Pestañas o secciónes para la configuración
> - 4.- Area del formulario
> - 5.- Acciones: Guardar(Guardar y se queda dentro del formulario), Guardar y cerra(Guarda y regresa al listado), Eliminar(Elimina una matriz de seguimiento completa, solo disponible en edición)

<br>
<p style="text-align: justify;">
Las acciones una vez creadas la matriz de seguimiento son las siguientes.
</p>
![](configuracion-2.png)

<br>
>**Acciones**

> - 1.- Agregar nuevo: Crea un nuevo formulario en blanco para configurar una nueva matriz
> - 2.- Clonar y editar: Clona la matriz de seguimiento, y regresa un formulario para editarlo segun sea necesario
> - 3.- Volver al listado: Regresa a la pantalla donde se listan las matrices creadas

<br>
## General
<br>

<p style="text-align: justify;">
En esta sección se le da el nombre a la matriz de seguimiento y se describe el uso o proposito general
</p>
![](configuracion-3.png)

<br>
>**General**

> - 1.- Nombre: Nombre de la matriz de seguimiento
> - 2.- Descripcion: Descripción general

<br>
## Indicador de desempeño
<br>

<p style="text-align: justify;">
El indicador de desempeño es una clasificación general o un agrupador de indicadores que se le puede poner un orden para contruir el reporte final.
</p>
![](configuracion-4.png)

<br>
>**Elementos generales**

> - 1.- Nombre del indicador de desempeño (Al hacer clic abre o cierra el formulario para la configuracion del indicador)
> - 2.- Quitar un indicador de desempeño de la lista
> - 3.- Acciones, Agregar nuevo y limpiar

<br>
<p style="text-align: justify;">
Al quitar un indicador de desempeño se pierde todos los datos capturados dentro del formulario que contenga, es por eso que se pide una confirmación para eliminar el item de la lista
</p>
![](configuracion-5.png)

<br>
<p style="text-align: justify;">
Al hacer click en limpiarse elimina todos los item de indicadores de sempeño con sus respectivas configuraciones, por ello tambien se pide una confirmacion
</p>
![](configuracion-6.png)

<br>
### Agregar indicador de desempeño
<br>

<p style="text-align: justify;">
Al hacer clien en el boton "Agregar nuevo" se añadira un nuevo formulario para la configuracion del indicador de desempeño, este formulario consta de estos apartados
</p>
![](configuracion-7.png)

<br>
>**Nuevo**

> - 1.- Nombre del indicador de desempeño 
> - 2.- Orden en el que aparacera al momento de crear el reporte
> - 3.- Grupos de indicadores especificos (Con fuente etab y con fuentes fuera del etab)

<br>
### Agregar indicador disponibles en ETAB
<br>

<p style="text-align: justify;">
Los indicadores disponibles en ETAB son aquellos que tienen datos dentro del mismo sistema, para estos indicadores solo se les programa la planeación ya que los datos de captura se extraeran solos al mosmento de general el reporte.
</p>
![](configuracion-8.png)

<br>
>**Nuevo**

> - 1.- Boton indicadores: agregar un nuevo indicador a la lista 
> - 2.- Lista de indicadores que forman parte del indicador de desempeño

<br>
<p style="text-align: justify;">
Para cada item en la lista se puede agregar filtros personalizados y alertas, el boton rojo sirve para eliminar el item de la lista al igual que el indicador de desempeño tambien pide confirmación
</p>
![](configuracion-9.png)

<br>
<p style="text-align: justify;">
Los filtros nos serviran para mostrar toda o una region en especifca y para especificar el tipo de dimensión a mostrar los datos en el reporte, asi tambien para especificar si el dato sera mensual, bimestral, trimestral, ...etc
</p>
![](configuracion-10.png)

<br>
>**Filtros**

> - 1.- Dimensión con los datos de cada una 
> - 2.- Nombre de la dimensión mostrada
> - 3.- Dimensión que contendra el valor a mostrar en el reporte
> - 4.- Forma de mostrar los datos en el reporte (Mensula, bimestral, trimestral, ...etc)
> - 5.- Dimensión para aplicar el filtro
> - 6.- Valores para hacer comprativas (Multiselect)
> - 7.- Aplicar filtros
> - 8.- Quitar filtros (Elimina el filtro pero no limpia el formulario, el filtro solo se aplica a los datos de la dimension especificada)
> - 9.- Ok, aceptar los filtros que se aplicaran al indicador al mostrar el reporte

<br>
<p style="text-align: justify;">
Las alertas se utilizan para pintar el mapa de calor en el reporte
</p>
![](configuracion-11.png)

<br>
>**Alertas**

> - 1.- Agregar un nuevo item a la lsita de alertas
> - 2.- Elimina todos los items de las lista
> - 3.- Aceptar la lista parael indicador
> - 4.- Eliminar el item
> - 5.- Color de la alerta
> - 6.- Valor para el limite superior
> - 7.- Valor para el limite inferior (Se recomienda que este valor sea mayor minimo una decima del valor superior anterior)

<br>
### Agregar indicador NO disponibles en ETAB
<br>

<p style="text-align: justify;">
Los indicadores no disponibles en ETAB son aquellos que tienen datos fuera del sistema, para estos indicadores es necesario captura manualmente los datos
</p>
![](configuracion-12.png)

<br>
>**Nuevo**

> - 1.- Agregar nuevo: agregar un nuevo item a la lista 
> - 2.- Limpiar: Elimina todos los items de la lista con previa confirmación

<br>
<p style="text-align: justify;">
Los items en la lista corresponde a un indicador medible
</p>
![](configuracion-13.png)

<br>
>**Alertas**

> - 1.- Nombre del indicador
> - 2.- Fuente para especificar de donde se esta tomando el dato
> - 3.- Configuración de las alertas para cada indicador
> - 4.- Eliminar el item de la lista

<br>
<p style="text-align: justify;">
Las alertas se utilizan para pintar el mapa de calor en el reporte
</p>
![](configuracion-11.png)

<br>
>**Alertas**

> - 1.- Agregar un nuevo item a la lsita de alertas
> - 2.- Elimina todos los items de las lista
> - 3.- Aceptar la lista parael indicador
> - 4.- Eliminar el item
> - 5.- Color de la alerta
> - 6.- Valor para el limite superior
> - 7.- Valor para el limite inferior (Se recomienda que este valor sea mayor minimo una decima del valor superior anterior)

<br>
## Usuarios
<br>

<p style="text-align: justify;">
Los permisos a usuarios nos permiten decidir que usuarios tendran acceso al reporte de la matriz configurada
</p>
![](configuracion-15.png)

<br>
>**Usuarios**

> - 1.- Filtrar por nombre
> - 2.- Al hacer clic en el item del usuario se asigna a la lista de usuarios permitidos

<br>
<p style="text-align: justify;">
Despues de agregar todos los indicadores con sus respectivas alertas y filtros y asignado los usuarios que podran verla se procede a guardar en las acciones principales
</p>
![](configuracion-14.png)

<p style="text-align: justify;">
Si todo esta bien mostrara un mensaje como el siguiente, si cocurre un error se mostrara el mensaje con otro color
</p>
![](configuracion-16.png)