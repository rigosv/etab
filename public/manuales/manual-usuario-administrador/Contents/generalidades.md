# Generalidades

## Flujo de trabajo
El flujo de trabajo principal, consiste en los siguientes pasos:

1. Configurar una conexión desde donde se obtendrán los datos (Orígenes de datos -> Conexión a bases de datos -> Agregar
 Nuevo)
1. Crear el origen de datos, se puede extraer desde una base de datos o un archivo (Orígenes de datos -> 
Origen de datos -> Agregar Nuevo)
1. Configurar el origen de datos, se deben especificar el significado de cada campo (Orígenes de datos -> 
Origen de datos -> Seleccionar un origen para editar)
1. Crear las variables (Indicadores -> Variables -> Agregar Nuevo)
1. Crear la ficha técnica, la cual contendrá las especificaciones del indicador y la fórmula para calcularlo 
(Indicadores -> Ficha Técnica -> Agregar Nuevo)
1. Uso de los indicadores desde el tablero (Indicadores -> Tablero)

Pasos alternos:

1. Si los datos se obtendrán desde un archivo, no es necesario configurar una conexión a una base de datos

## Interfaz principal
![Interfaz Principal](images/area_principal.png)

1. Nombre del usuario actual y la opción para salir.
1. Logo/Imagen principal, la cual puede cambiar según la personalización que se haga en la instalación
1. Menú principal, se mostrarán las opciones de acuerdo al perfil del usuario
1. Acciones, contendrá opciones como agregar un nuevo elemento o filtrar resultados, dependerá de que se esté 
mostrando en el área de trabajo
1. Área de trabajo. Se mostrará su contenido de acuerdo a las opciones seleccionadas

## Listado
La mayoría de interfaces se componen de un listado, desde la cual podemos realizar las siguientes acciones:

1. Crear un nuevo elemento
1. Seleccionar un elemento para Editar/Borrar
1. Cambiar el orden del listado, dando clic en el título de la columna
1. Aplicar filtros
1. Exportar los datos del listado


![Configuración del origen de datos](images/listado.png)

## Filtrando elementos
Cuando está en la vista **listado** se mostrará, en la barra de acciones, la opción Filtros, al darle clic se mostrará 
una nueva sección que mostrará las opciones por las cuales puede filtrar el listado que se muestra actualmente, 
los campos disponibles serán propios de cada listado y puede seleccionar uno o varios campos para filtrar. 
Al establecer un filtro debe dar clic en el botón **Filtrar** para que éste se aplicado

![Filtros](images/filtros.gif)

Por defecto se buscará los elementos que contengan el texto ingresado como filtro, pero también puede elegir la opción 
**Filtros avanzados** para poder elegir la forma en que se debe buscar el texto ingresado: **contiene**, **no contiene**, **es igual a**; 
como se muestra en la imagen siguiente

![Filtros avanzados](images/filtros_avanzados.gif)



## Acciones sobre un elemento
Cuando se está creando un nuevo elemento se dispondrá en la parte inferior del formulario los siguientes botones de acción:

1. Crear y editar. Guarda los datos actuales y permanece el formulario abierto para edición
1. Crear y regresar al listado. Guarda los datos actuales y regresa a mostrar el listado.
1. Crear y agregar otro. Guarda los datos y muestra el formulario en blanco para agregar otro elemento

![Crear - Acciones](images/botones_crear.png)


Si se está editando un elemento existente dispondremos de los siguientes botones de acción:

1. Actualizar. Guarda los cambios realizados y mantiene el formulario abierto para poder realizar más modificaciones.
1. Actualizar y cerrar. Guarda los cambios y vuelve al listado.
1. Borrar. Permite elimitar el elemento actual, se pedirá confirmación de la acción.

![Editar - Acciones](images/botones_editar.png)

