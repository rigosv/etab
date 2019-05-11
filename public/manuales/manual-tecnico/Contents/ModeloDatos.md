#Modelo de datos
##Diccionario de datos

### Listado de tablas
<table border="1" style="border-collapse: collapse;" id="tablas">
    <thead>
    <tr style="background-color: #1c94c4;">
        <TH>Tabla</TH>
        <TH>Descripción</TH>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>alerta </td>
            <td>Contiene los colores utilizados en los rangos de alertas</td>
        </tr>
        <tr>
            <td>bitacora </td>
            <td>Guarda las acciones del usuario</td>
        </tr>
        <tr>
            <td>boletin </td>
            <td></td>
        </tr>
        <tr>
            <td>campo </td>
            <td>Los campos para identificar los elementos de los orígenes de datos</td>
        </tr>        
        <tr>
            <td>clasificacion_tecnica </td>
            <td>Es el segundo nivel para clasificar los indicadores</td>
        </tr>
        <tr>
            <td>clasificacion_uso </td>
            <td>Es el primer nivel para clasificar los indicadores</td>
        </tr>
        <tr>
            <td>conexion </td>
            <td>Contiene los parámetros de conexión a las bases de datos de donde se extraen los orígenes de datos </td>
        </tr>
        <tr>
            <td>configuracion_pivot_table </td>
            <td>Guarda los estados de la tabla dinámica</td>
        </tr>        
        <tr>
            <td>ficha_tecnica </td>
            <td>Contiene los campos de la ficha técnica que describen los indicadores</td>
        </tr>
        <tr>
            <td>ficha_tecnica_campo </td>
            <td>Los campos que pertenecen a la ficha técnica</td>
        </tr>
        <tr>
            <td>fichatecnica_clasificaciontecnica </td>
            <td>Las relación que determina a qué clasificaciones pertenece la ficha técnica</td>
        </tr>
        <tr>
            <td>fichatecnica_tiposgraficos </td>
            <td>Determina los tipos de gráficos que permitirá la ficha técnica</td>
        </tr>
        <tr>
            <td>ficha_tecnica_variable_dato </td>
            <td>Relaciona las variables que se utilizará en la fórmula de cálculo del indicador</td>
        </tr>
        <tr>
            <td>fila_origen_dato_v2 </td>
            <td>Estructura modelo para crear el almacenamiento de los orígenes de datos</td>
        </tr>
        <tr>
            <td>fos_user_group </td>
            <td>Grupo de usuarios</td>
        </tr>
        <tr>
            <td>fos_user_user </td>
            <td>Usuarios del sistema</td>
        </tr>
        <tr>
            <td>fos_user_user_group </td>
            <td>Relación para definir los grupos a los que pertenecen los usuarios</td>
        </tr>
        <tr>
            <td>fuente_dato </td>
            <td>Contiene la información de las fuentes de datos</td>
        </tr>
        <tr>
            <td>fusion_origenes_datos </td>
            <td>Relación de los indicadores base con los indicadores que fusiona</td>
        </tr>
        <tr>
            <td>group_fichatecnica </td>
            <td>Determina las fichas técnicas a las que se tiene acceso por nivel de grupo</td>
        </tr>
        <tr>
            <td>group_grupoindicadores </td>
            <td>Relaciona las salas situacionales a las que tiene acceso un grupo de usuarios</td>
        </tr>        
        <tr>
            <td>grupo_indicadores </td>
            <td>Indicadors a los que se tiene acceso a nivel de grupo de usuarios</td>
        </tr>
        <tr>
            <td>grupo_indicadores_indicador </td>
            <td>Los indicadores que pertenecen a una sala situacional</td>
        </tr>        
        <tr>
            <td>indicador_alertas </td>
            <td>Las alertas asociadas a un indicador</td>
        </tr>
        <tr>
            <td>indicador_usuario </td>
            <td>Indicadores asociados a un usuario</td>
        </tr>        
        <tr>
            <td>matriz_indicadores_desempeno </td>
            <td></td>
        </tr>
        <tr>
            <td>matriz_indicadores_desempeno_ficha_tecnica </td>
            <td></td>
        </tr>
        <tr>
            <td>matriz_indicadores_etab </td>
            <td></td>
        </tr>
        <tr>
            <td>matriz_indicadores_etab_alertas </td>
            <td></td>
        </tr>
        <tr>
            <td>matriz_indicadores_relacion </td>
            <td></td>
        </tr>
        <tr>
            <td>matriz_indicadores_relacion_alertas </td>
            <td></td>
        </tr>
        <tr>
            <td>matriz_indicadores_usuario </td>
            <td></td>
        </tr>
        <tr>
            <td>matriz_seguimiento </td>
            <td></td>
        </tr>
        <tr>
            <td>matriz_seguimiento_dato </td>
            <td></td>
        </tr>
        <tr>
            <td>matriz_seguimiento_matriz </td>
            <td></td>
        </tr>
        <tr>
            <td>motor_bd </td>
            <td>Los motores de base de datos soportados para crear orígenes de datos</td>
        </tr>
        <tr>
            <td>origen_datos </td>
            <td>Contiene información para la obtención de los datos desde sus orígenes</td>
        </tr>
        <tr>
            <td>origenes_conexiones </td>
            <td>Las conexiones sobre las que trabajará un origen de datos</td>
        </tr>
        <tr>
            <td>periodos </td>
            <td>Periodos de lectura</td>
        </tr>
        <tr>
            <td>responsable_dato </td>
            <td>Información para identificar al responsable de brindar los datos</td>
        </tr>
        <tr>
            <td>responsable_indicador </td>
            <td>Información del responsable del indicador</td>
        </tr>
        <tr>
            <td>sala_acciones </td>
            <td>Lista de acciones o comentarios sobre una sala situacional</td>
        </tr>
        <tr>
            <td>sala_comentarios </td>
            <td>Comentarios realizados dentro de una sala situacional</td>
        </tr>
        <tr>
            <td>significado_campo </td>
            <td>Se utilizar para estándarizar el significado de los campos de los diferentes orígenes de datos</td>
        </tr>
        <tr>
            <td>significados_tipos_graficos </td>
            <td>Los tipos de gráficos permitidos para un significado de datos</td>
        </tr>        
        <tr>
            <td>tipo_grafico </td>
            <td>Los tipos de gráficos soportados</td>
        </tr>
        <tr>
            <td>usuario_grupo_indicadores </td>
            <td>Las salas situacionales asociadas a un usuario</td>
        </tr>
        <tr>
            <td>usuario_indicadores_favoritos </td>
            <td>Indicadores favoritos de un usuario</td>
        </tr>
        <tr>
            <td>variable_dato </td>
            <td>Las variables que se utilizan en la ficha técnica del indicador</td>
        </tr>
    </tbody>
</table>
</br>


### Descripción de la tabla: **alerta**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-alerta">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td >LLave primaria</td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >codigo</td>
        <td >Código html para identificar la alerta</td>
        <td >character varying</td>
        <td >30</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >color</td>
        <td >Nombre del color</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **bitacora**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-bitacora">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td >Llave primaria</td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_usuario</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    <tr>
        <td >id_session</td>
        <td >Identificador de la sesión en que se realizaron las acciones</td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >fecha_hora</td>
        <td >Fecha y hora en que se realizó la acción</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >accion</td>
        <td >Nombre de la acción realizada</td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >elemento</td>
        <td >Elementos afectados</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **boletin**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-boletin">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >sala</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY grupo_indicadores(id)                    </td>
    </tr>
    <tr>
        <td >grupo</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY fos_user_group(id)                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td ></td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >creado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >actualizado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >token</td>
        <td ></td>
        <td >character varying</td>
        <td >72</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **campo**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-campo">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td >Llave primaria</td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_origen_datos</td>
        <td ></td>
        <td >bigint</td>
        <td >64</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY origen_datos(id)                    </td>
    </tr>
    <tr>
        <td >id_tipo_campo</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY tipo_campo(id)                    </td>
    </tr>
    <tr>
        <td >id_significado_campo</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY significado_campo(id)                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td >Nombre del campo</td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >descripcion</td>
        <td >Texto descriptivo del campo</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>    
    </tbody>
</table>


### Descripicón de la tabla: **clasificacion_tecnica**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-clasificacion_tecnica">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >codigo</td>
        <td >Código que describe la clasificación técnica</td>
        <td >character varying</td>
        <td >15</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >descripcion</td>
        <td >Texto descriptivo de la clasificación técnica</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >comentario</td>
        <td >Comentarios u observaciones de la clasficación</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >clasificacionuso_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY clasificacion_uso(id)                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **clasificacion_uso**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-clasificacion_uso">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >codigo</td>
        <td >El código de la clasificación de uso</td>
        <td >character varying</td>
        <td >15</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >descripcion</td>
        <td >Texto descriptivo de la clasificación de uso</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >comentario</td>
        <td >Comentarios generales</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **conexion**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-conexion">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_motor</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY motor_bd(id)                    </td>
    </tr>
    <tr>
        <td >nombre_conexion</td>
        <td >Nombre que describe la conexión</td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >comentario</td>
        <td >Comentario general</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >ip</td>
        <td >Dirección IP del host al que se hará la conexión </td>
        <td >character varying</td>
        <td >15</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >usuario</td>
        <td >Usuario para realizar la conexión</td>
        <td >character varying</td>
        <td >25</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >clave</td>
        <td >Clave del usuario para la conexión</td>
        <td >character varying</td>
        <td >150</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >nombre_base_datos</td>
        <td >Nombre de la base de datos a la que se conectará</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >puerto</td>
        <td >Puerto utilizado en la conexión</td>
        <td >character varying</td>
        <td >5</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >instancia</td>
        <td >Instancia de la base de datos a utilizar</td>
        <td >character varying</td>
        <td >50</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **configuracion_pivot_table**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-configuracion_pivot_table">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td >Nombre del escenario a guardar</td>
        <td >character varying</td>
        <td >255</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >por_defecto</td>
        <td >true si es un escenario por defecto</td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >configuracion</td>
        <td >Configuración guardada de la tabla dinámica</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >id_elemento</td>
        <td >Identificador del elemento sobre la que se realiza la tabla dinámica</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >tipo_elemento</td>
        <td >Tipo de elemento que utilizará la configuración guardada de la tabla dinámica</td>
        <td >character varying</td>
        <td >50</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >id_usuario</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **ficha_tecnica**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-ficha_tecnica">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td >Nombre de la ficha técnica</td>
        <td >character varying</td>
        <td >150</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >tema</td>
        <td >Texto explicativo del indicador </td>
        <td >text</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >concepto</td>
        <td >Concepto u objetivo del indicador</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >unidad_medida</td>
        <td >Unida de medida en que se mostrarán los resultados</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >formula</td>
        <td >Fórmula para cálcular el indicador</td>
        <td >character varying</td>
        <td >300</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >observacion</td>
        <td >Comentarios generales</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >campos_indicador</td>
        <td >Campos que utilizará la ficha técnica</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >confiabilidad</td>
        <td >Para ingresar un número que indique el porcentaje de confiabilidad de los cálculos</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >updated_at</td>
        <td >La fecha en que se actualizó la ficha técnica</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >id_periodo</td>
        <td >Periodo de lectura del indicador</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY periodos(id)                    </td>
    </tr>
    <tr>
        <td >ultima_lectura</td>
        <td >Fecha en que se realizó la última carga de datos</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >es_acumulado</td>
        <td >true si es un indicador acumulado</td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >meta</td>
        <td >Meta de la medición del indicador</td>
        <td >double precision</td>
        <td >53</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >codigo</td>
        <td >Código de la ficha técnica</td>
        <td >character varying</td>
        <td >100</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >ruta</td>
        <td >Si los datos se obtienen de un sistema, se puede registrar la ruta para obtener los datos</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >cantidad_decimales</td>
        <td >Cantidad de decimales que se usarán para mostrar el resultado del cálculo del indicador</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **ficha_tecnica_campo**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-ficha_tecnica_campo">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id_ficha_tecnica</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    <tr>
        <td >id_campo</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY campo(id)                    </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **fichatecnica_clasificaciontecnica**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-fichatecnica_clasificaciontecnica">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >fichatecnica_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    <tr>
        <td >clasificaciontecnica_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY clasificacion_tecnica(id)                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **fichatecnica_tiposgraficos**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-fichatecnica_tiposgraficos">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >fichatecnica_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    <tr>
        <td >tipografico_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY tipo_grafico(id)                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **ficha_tecnica_variable_dato**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-ficha_tecnica_variable_dato">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id_ficha_tecnica</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    <tr>
        <td >id_variable_dato</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **fila_origen_dato_v2**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-fila_origen_dato_v2">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id_origen_dato</td>
        <td >Identificador del origen de datos</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >datos</td>
        <td >Datos en formato json</td>
        <td >jsonb</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >ultima_lectura</td>
        <td >Fecha en que se realizó la última carga de datos</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >id_conexion</td>
        <td >Identificador de la conexión de la que se obtuvieron los datos</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **fos_user_group**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-fos_user_group">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >name</td>
        <td >Nombre del grupo</td>
        <td >character varying</td>
        <td >255</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >roles</td>
        <td >Roles asignados al grupo</td>
        <td >text</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **fos_user_user**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-fos_user_user">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >username</td>
        <td >Código de ingreso del usuario</td>
        <td >character varying</td>
        <td >180</td>
        <td >No</td>
        <td>
        </td>
    </tr>   
    <tr>
        <td >email</td>
        <td >Dirección electrónica</td>
        <td >character varying</td>
        <td >180</td>
        <td >No</td>
        <td>
        </td>
    </tr>    
    <tr>
        <td >enabled</td>
        <td >true si el usuario está habilitado</td>
        <td >boolean</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>    
    <tr>
        <td >password</td>
        <td >Clave de acceso</td>
        <td >character varying</td>
        <td >255</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >last_login</td>
        <td >Fecha de última conexión</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >roles</td>
        <td >Roles del usuario</td>
        <td >text</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >created_at</td>
        <td >Fecha de creación del usuario</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >updated_at</td>
        <td >Fecha de actualización del usuario</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >date_of_birth</td>
        <td >Fecha de nacimiento del usuario</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >firstname</td>
        <td >Nombre</td>
        <td >character varying</td>
        <td >64</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >lastname</td>
        <td >Apellidos del usuario</td>
        <td >character varying</td>
        <td >64</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>  
    <tr>
        <td >gender</td>
        <td >Género del usuario</td>
        <td >character varying</td>
        <td >1</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>      
    <tr>
        <td >establecimientoprincipal_id</td>
        <td >Identificar del establecimiento al que pertenece el usuario</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **fos_user_user_group**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-fos_user_user_group">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >user_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    <tr>
        <td >group_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY fos_user_group(id)                    </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla; **fuente_dato**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-fuente_dato">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >establecimiento</td>
        <td >Establecimiento al que pertenece el usurio</td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >contacto</td>
        <td >Nombre del contacto</td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >correo</td>
        <td >Dirección de correo electrónico</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >telefono</td>
        <td ></td>
        <td >character varying</td>
        <td >15</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >cargo</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **fusion_origenes_datos**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-fusion_origenes_datos">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_origen_datos</td>
        <td >Identificador del origen de datos principal</td>
        <td >bigint</td>
        <td >64</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY origen_datos(id)                    </td>
    </tr>
    <tr>
        <td >id_origen_datos_fusionado</td>
        <td >Identificador del origen de datos que es fusionado</td>
        <td >bigint</td>
        <td >64</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY origen_datos(id)                    </td>
    </tr>
    <tr>
        <td >campos</td>
        <td ></td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **group_fichatecnica**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-group_fichatecnica">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >group_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY fos_user_group(id)                    </td>
    </tr>
    <tr>
        <td >fichatecnica_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **group_grupoindicadores**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-group_grupoindicadores">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >group_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY fos_user_group(id)                    </td>
    </tr>
    <tr>
        <td >grupoindicadores_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY grupo_indicadores(id)                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **grupo_indicadores**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-grupo_indicadores">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td >Nombre de la sala situacional</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >updated_at</td>
        <td >Fecha en que se actualizó</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **grupo_indicadores_indicador**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-grupo_indicadores_indicador">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >indicador_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    <tr>
        <td >grupo_indicadores_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY grupo_indicadores(id)                    </td>
    </tr>
    <tr>
        <td >dimension</td>
        <td >La dimensión que se usó para el indicador en la sala</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >filtro</td>
        <td >El filtro del indicador</td>
        <td >character varying</td>
        <td >500</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >filtro_posicion_desde</td>
        <td >Valor inicial del filtro </td>
        <td >character varying</td>
        <td >10</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >filtro_posicion_hasta</td>
        <td >Valor final del filtro</td>
        <td >character varying</td>
        <td >10</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >filtro_elementos</td>
        <td >Elementos filtrados</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >posicion</td>
        <td >Posición a filtrar</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >tipo_grafico</td>
        <td >Tipo de gráfico utilizado</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >vista</td>
        <td >Forma de presentación: gráfico, tabla</td>
        <td >character varying</td>
        <td >20</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >orden</td>
        <td >La posición del indicador en la sala</td>
        <td >character varying</td>
        <td >100</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **indicador_alertas**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-indicador_alertas">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_color_alerta</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            FOREIGN KEY alerta(id)                    </td>
    </tr>
    <tr>
        <td >id_indicador</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    <tr>
        <td >limite_inferior</td>
        <td >Límite inferior del rango de la alerta</td>
        <td >double precision</td>
        <td >53</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >limite_superior</td>
        <td >Límite superior del rango de la alerta</td>
        <td >double precision</td>
        <td >53</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >comentario</td>
        <td >Comentario general</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **indicador_usuario**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-indicador_usuario">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id_usuario</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    <tr>
        <td >id_indicador</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **indicador_variablecaptura**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-indicador_variablecaptura">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >indicador_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >variablecaptura_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **matriz_indicadores_desempeno**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_indicadores_desempeno">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_matriz</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY matriz_seguimiento_matriz(id)                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td ></td>
        <td >character varying</td>
        <td >500</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >orden</td>
        <td ></td>
        <td >character varying</td>
        <td >4</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >creado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >actualizado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **matriz_indicadores_desempeno_ficha_tecnica**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_indicadores_desempeno_ficha_tecnica">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >matriz_indicadores_desempeno_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY matriz_indicadores_desempeno(id)                    </td>
    </tr>
    <tr>
        <td >ficha_tecnica_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **matriz_indicadores_etab**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_indicadores_etab">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_ficha_tecnica</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    <tr>
        <td >id_desempeno</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            FOREIGN KEY matriz_indicadores_desempeno(id)                    </td>
    </tr>
    <tr>
        <td >filtros</td>
        <td ></td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >creado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >actualizado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

###Descripción de la tabla: **matriz_indicadores_etab_alertas**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_indicadores_etab_alertas">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >matriz_indicador_etab_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            FOREIGN KEY matriz_indicadores_etab(id)                    </td>
    </tr>
    <tr>
        <td >limite_inferior</td>
        <td ></td>
        <td >double precision</td>
        <td >53</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >limite_superior</td>
        <td ></td>
        <td >double precision</td>
        <td >53</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >color</td>
        <td ></td>
        <td >text</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >creado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >actualizado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **matriz_indicadores_relacion**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_indicadores_relacion">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_desempeno</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            FOREIGN KEY matriz_indicadores_desempeno(id)                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td ></td>
        <td >character varying</td>
        <td >500</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >fuente</td>
        <td ></td>
        <td >character varying</td>
        <td >500</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >creado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >actualizado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **matriz_indicadores_relacion_alertas**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_indicadores_relacion_alertas">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >matriz_indicador_relacion_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            FOREIGN KEY matriz_indicadores_relacion(id)                    </td>
    </tr>
    <tr>
        <td >limite_inferior</td>
        <td ></td>
        <td >double precision</td>
        <td >53</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >limite_superior</td>
        <td ></td>
        <td >double precision</td>
        <td >53</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >color</td>
        <td ></td>
        <td >text</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >creado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >actualizado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **matriz_indicadores_usuario**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_indicadores_usuario">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_matriz</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY matriz_seguimiento_matriz(id)                    </td>
    </tr>
    <tr>
        <td >id_usuario</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    <tr>
        <td >creado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >actualizado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **matriz_seguimiento**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_seguimiento">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_desempeno</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY matriz_indicadores_desempeno(id)                    </td>
    </tr>
    <tr>
        <td >anio</td>
        <td ></td>
        <td >character varying</td>
        <td >4</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >etab</td>
        <td ></td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >meta</td>
        <td ></td>
        <td >character varying</td>
        <td >65</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >indicador</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >creado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >actualizado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **matriz_seguimiento_dato**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_seguimiento_dato">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_matriz</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY matriz_seguimiento(id)                    </td>
    </tr>
    <tr>
        <td >mes</td>
        <td ></td>
        <td >character varying</td>
        <td >20</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >planificado</td>
        <td ></td>
        <td >character varying</td>
        <td >20</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >real</td>
        <td ></td>
        <td >character varying</td>
        <td >20</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >creado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >actualizado</td>
        <td ></td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **matriz_seguimiento_matriz**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-matriz_seguimiento_matriz">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td ></td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >descripcion</td>
        <td ></td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **motor_bd**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-motor_bd">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td >Nombre que identifica al motor de la base de datos</td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >codigo</td>
        <td >Código a utilizar para identificar el motod de base de datos</td>
        <td >character varying</td>
        <td >20</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **origen_datos**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-origen_datos">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >bigint</td>
        <td >64</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td >Nombre del origen de datos</td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >descripcion</td>
        <td >Texto descriptivo del origen de datos</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >sentencia_sql</td>
        <td >Sentencia SQL para extraer los datos</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >archivo_nombre</td>
        <td >Nombre del archivo cuando la carga es desde hoja de cálculo o archivo csv</td>
        <td >character varying</td>
        <td >255</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >es_fusionado</td>
        <td >true si es un origen de datos fusionado</td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >es_catalogo</td>
        <td >true si es un origen de datos para crear una tabla catálogo</td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >nombre_catalogo</td>
        <td >Nombre de la tabla catálogo</td>
        <td >character varying</td>
        <td >100</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >campos_fusionados</td>
        <td >Si es un origen fusionado, que campos se han fusionado</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >ultima_actualizacion</td>
        <td >Fecha de la última actualización</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >ventana_limite_inferior</td>
        <td >Valor utilizado en carga incremental</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >ventana_limite_superior</td>
        <td >Valor utilizado en carga incremental</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >campolecturaincremental_id</td>
        <td >El campo que se usará para el control de la lectura incremental</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY campo(id)                    </td>
    </tr>
    <tr>
        <td >tiempo_segundos_ultima_carga</td>
        <td >Para controlar el tiempo que se tarda la carga de datos</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >carga_finalizada</td>
        <td >Indica si la última carga de datos se realizó correctamente</td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >error_carga</td>
        <td >true si ocurrió un error al cargar los datos</td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >mensaje_error_carga</td>
        <td >Mensaje de error si la carga de datos falló</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >valor_corte</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >formato_valor_corte</td>
        <td >Formato del campo que se usará para controlar la carga incremental</td>
        <td >character varying</td>
        <td >100</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >acciones_poscarga</td>
        <td >Sentencias SQL separadas por ; que se ejecutarán después de realizada la carga de datos</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **origen_datos_fusiones**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-origen_datos_fusiones">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id_origen_dato</td>
        <td ></td>
        <td >bigint</td>
        <td >64</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY origen_datos(id)                    </td>
    </tr>
    <tr>
        <td >id_origen_dato_fusionado</td>
        <td ></td>
        <td >bigint</td>
        <td >64</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY origen_datos(id)                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **origenes_conexiones**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-origenes_conexiones">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >origendatos_id</td>
        <td ></td>
        <td >bigint</td>
        <td >64</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY origen_datos(id)                    </td>
    </tr>
    <tr>
        <td >conexion_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY conexion(id)                    </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **periodos**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-periodos">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >descripcion</td>
        <td >Descripción del periodo</td>
        <td >character varying</td>
        <td >25</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >codigo</td>
        <td >Código que identifica el perido</td>
        <td >character varying</td>
        <td >7</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **responsable_dato**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-responsable_dato">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >establecimiento</td>
        <td >Establecimiento del responsable del dato</td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >contacto</td>
        <td >Nombre del contacto</td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >correo</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >telefono</td>
        <td ></td>
        <td >character varying</td>
        <td >15</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >cargo</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **responsable_indicador**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-responsable_indicador">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >establecimiento</td>
        <td ></td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >contacto</td>
        <td ></td>
        <td >character varying</td>
        <td >100</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >correo</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >telefono</td>
        <td ></td>
        <td >character varying</td>
        <td >15</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >cargo</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **sala_acciones**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-sala_acciones">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >grupo_indicadores_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY grupo_indicadores(id)                    </td>
    </tr>
    <tr>
        <td >usuario_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    <tr>
        <td >acciones</td>
        <td >Texto con las acciones a realizar</td>
        <td >text</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >observaciones</td>
        <td >Observaciones generales</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >responsables</td>
        <td >Nombre de los responsables de realizar las acciones</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >fecha</td>
        <td >Fecha de asignación de acciones</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **sala_comentarios**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-sala_comentarios">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >grupo_indicadores_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY grupo_indicadores(id)                    </td>
    </tr>
    <tr>
        <td >usuario_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    <tr>
        <td >comentario</td>
        <td >Texto del comentario realizado en la sala situacional</td>
        <td >text</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >fecha</td>
        <td >Fecha en que se realizó el comentario</td>
        <td >timestamp without time zone</td>
        <td ></td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **significado_campo**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-significado_campo">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >descripcion</td>
        <td ></td>
        <td >character varying</td>
        <td >200</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >codigo</td>
        <td ></td>
        <td >character varying</td>
        <td >40</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >uso_en_catalogo</td>
        <td >true si es un significado para utilizar en catálogos</td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >catalogo</td>
        <td >Nombre del catálogo asociado al significado de campo</td>
        <td >character varying</td>
        <td >255</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >acumulable</td>
        <td >true si el signficado de campo es acumulable</td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **significados_tipos_graficos**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-significados_tipos_graficos">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >significadocampo_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY significado_campo(id)                    </td>
    </tr>
    <tr>
        <td >tipografico_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY tipo_grafico(id)                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la base de datos: **tipo_campo**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-tipo_campo">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >descripcion</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >codigo</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: tipo_grafico**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-tipo_grafico">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >descripcion</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >codigo</td>
        <td ></td>
        <td >character varying</td>
        <td >50</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **usuario_grupo_indicadores**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-usuario_grupo_indicadores">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >grupo_indicadores_id</td>
        <td >Identificador de la sala situacional</td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY grupo_indicadores(id)                    </td>
    </tr>
    <tr>
        <td >usuario_id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    <tr>
        <td >es_duenio</td>
        <td >true si el usuario es dueño de la sala situacional</td>
        <td >boolean</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >usuario_asigno_id</td>
        <td >Identificador del usuario que asignó la sala situacional</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
            FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    </tbody>
</table>

### Descripción de la tabla: **usuario_indicadores_favoritos**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-usuario_indicadores_favoritos">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id_usuario</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY fos_user_user(id)                    </td>
    </tr>
    <tr>
        <td >id_indicador</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY, FOREIGN KEY ficha_tecnica(id)                    </td>
    </tr>
    </tbody>
</table>


### Descripción de la tabla: **variable_dato**

<table border="1" style="border-collapse: collapse; width: 80%" id="dic-variable_dato">
    <thead>
    <tr style="background-color: #1c94c4; ">
        <TH style="width: 20%" >Campo</TH>
        <TH style="width: 40%" >Descripción</TH>
        <th style="width: 10%" >Tipo dato</th>
        <th style="width: 5%" >Longitud</th>
        <th style="width: 5%">Nulo</th>
        <th style="width: 15%">Restricciones</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td >id</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >No</td>
        <td>
            PRIMARY KEY                    </td>
    </tr>
    <tr>
        <td >id_fuente_dato</td>
        <td ></td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >id_responsable_dato</td>
        <td >Identificador del responsable del dato</td>
        <td >integer</td>
        <td >32</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >id_origen_datos</td>
        <td >Identificador del origen de datos</td>
        <td >bigint</td>
        <td >64</td>
        <td >Sí</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >nombre</td>
        <td >Nombre de la variable</td>
        <td >character varying</td>
        <td >200</td>
        <td >No</td>
        <td>
        </td>
    </tr>  
    <tr>
        <td >iniciales</td>
        <td >Iniciales o código identificador de la variable</td>
        <td >character varying</td>
        <td >255</td>
        <td >No</td>
        <td>
        </td>
    </tr>
    <tr>
        <td >comentario</td>
        <td >Comentario general</td>
        <td >text</td>
        <td ></td>
        <td >Sí</td>
        <td>
        </td>
    </tr>    
    </tbody>
</table>

        