#  Tecnologías utilizadas

El Tablero eTAB es un servicio Web disponible para que dependencias del sistema de salud suban 
sus datos para poder analizarlos, generar gráficas y reportes. 

La aplicación cuenta con un módulo para efectuar la extracción,transformación y carga de datos (ETL) 
desde diferentes fuentes. Estos datos son agregados y almacenados en una base de datos relacional (OLTP). 
Los datos están organizados por catálogos de referencia e Indicadores medibles. Los usuarios del sistema 
pueden administrar estos indicadores y catálogos y todos sus tributos usando el las herramientas que brinda 
el sistema.
Para efectuar consultas en línea los datos son agregados dentro de tablas optimizadas para el análisis en 
linea (OLAP). 
Las tablas para análisis son actualizadas periódicamente usando procedimientos almacenados de PostgresSQL.  
La gestión de consultas a las tablas de análisis OLAP se realiza por medio de un servidor dedicado. 
La interacción entre el servidor OLAP y el resto de la aplicación se realiza por medio de consultas AJAX. 
El resultado de las consultas al servidor OLAP, es porcesado usando JQuery y graficado usando la libreria de gráficos D3.  

Todo el software utilizado para creación del SIIG/eTAB son paquetes de software libre.
Estos incluyen:

* GitHub: Gestor de control de versiones de código fuente
* Apache: Servidor de paginas web
* PostgreSQL: Gestor de bases de Datos
* Symfony: Entorno de desarrollo para PHP
* PHP: Lenguaje de desarrollo de la Aplicación eTAB
* D3.js: Librería para la generación de gráficos
* JQuery: Lenguaje para interfaces de usuario
* RabbitMQ: Servidor de Mensajería
* EasyBook: Generador de documentos en formato PDF
* Bootstrap: Framework para interfaces de usuario
* PivotTable.js: Librería para crear tabla pivote
* Redis: Motor de base de datos en memoria.
* AngularJS: Framework javascript
