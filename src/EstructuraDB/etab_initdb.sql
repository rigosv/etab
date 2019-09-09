--
-- PostgreSQL database dump
--

-- Dumped from database version 10.6 (Ubuntu 10.6-0ubuntu0.18.04.1)
-- Dumped by pg_dump version 10.6 (Ubuntu 10.6-0ubuntu0.18.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

CREATE SCHEMA origenes;
CREATE SCHEMA temporales;



--
-- Name: acceso_externo; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.acceso_externo (
    id integer NOT NULL,
    token character varying(255) NOT NULL,
    caducidad timestamp(0) without time zone NOT NULL,
    usuariocrea_id integer
);


--
-- Name: acceso_externo_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.acceso_externo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: accesoexterno_grupoindicadores; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.accesoexterno_grupoindicadores (
    accesoexterno_id integer NOT NULL,
    grupoindicadores_id integer NOT NULL
);


--
-- Name: agencia; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.agencia (
    id integer NOT NULL,
    codigo character varying(20) NOT NULL,
    nombre character varying(200) NOT NULL
);


--
-- Name: agencia_formulario; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.agencia_formulario (
    id_agencia integer NOT NULL,
    id_formulario integer NOT NULL
);


--
-- Name: agencia_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.agencia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: alerta; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.alerta (
    id integer NOT NULL,
    codigo character varying(30) NOT NULL,
    color character varying(50) NOT NULL
);


--
-- Name: alerta_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.alerta_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: area_estandar_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.area_estandar_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: bitacora; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.bitacora (
    id integer NOT NULL,
    id_usuario integer,
    id_session character varying(100) NOT NULL,
    fecha_hora timestamp(0) without time zone NOT NULL,
    accion character varying(100) NOT NULL,
    elemento text
);


--
-- Name: bitacora_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.bitacora_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- Name: boletin; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.boletin (
    id integer NOT NULL,
    sala integer,
    grupo integer,
    nombre character varying(100) NOT NULL,
    creado timestamp(0) without time zone NOT NULL,
    actualizado timestamp(0) without time zone NOT NULL,
    token character varying(72) NOT NULL
);


--
-- Name: boletin_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.boletin_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campo; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.campo (
    id integer NOT NULL,
    id_origen_datos bigint,
    id_tipo_campo integer,
    id_significado_campo integer,
    nombre character varying(100) NOT NULL,
    descripcion text,
    id_diccionario integer,
    formula text
);


--
-- Name: campo_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.campo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: clasificacion_nivel; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.clasificacion_nivel (
    id integer NOT NULL,
    codigo character varying(15) NOT NULL,
    descripcion character varying(50) NOT NULL,
    comentario text
);


--
-- Name: clasificacion_nivel_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.clasificacion_nivel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: clasificacion_privacidad_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.clasificacion_privacidad_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: clasificacion_tecnica; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.clasificacion_tecnica (
    id integer NOT NULL,
    codigo character varying(15) NOT NULL,
    descripcion character varying(50) NOT NULL,
    comentario text,
    clasificacionuso_id integer
);


--
-- Name: clasificacion_tecnica_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.clasificacion_tecnica_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: clasificacion_uso; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.clasificacion_uso (
    id integer NOT NULL,
    codigo character varying(15) NOT NULL,
    descripcion character varying(50) NOT NULL,
    comentario text
);


--
-- Name: clasificacion_uso_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.clasificacion_uso_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: codificacion_caracteres_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.codificacion_caracteres_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- Name: conexion; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.conexion (
    id integer NOT NULL,
    id_motor integer,
    nombre_conexion character varying(100) NOT NULL,
    comentario text,
    ip character varying(15) NOT NULL,
    usuario character varying(25) NOT NULL,
    clave character varying(150) NOT NULL,
    nombre_base_datos character varying(50) NOT NULL,
    puerto character varying(5) DEFAULT NULL::character varying,
    instancia character varying(50) DEFAULT NULL::character varying
);


--
-- Name: conexion_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.conexion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: configuracion_pivot_table; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.configuracion_pivot_table (
    id integer NOT NULL,
    nombre character varying(255),
    por_defecto boolean,
    configuracion text,
    id_elemento integer,
    tipo_elemento character varying(50),
    id_usuario integer
);


--
-- Name: configuracion_pivot_table_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.configuracion_pivot_table_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: configuracion_pivot_table_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.configuracion_pivot_table_id_seq OWNED BY public.configuracion_pivot_table.id;


--
-- Name: diccionario; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.diccionario (
    id integer NOT NULL,
    descripcion character varying(200) NOT NULL,
    codigo character varying(20) NOT NULL
);


--
-- Name: diccionario_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.diccionario_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ficha_tecnica; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.ficha_tecnica (
    id integer NOT NULL,
    nombre character varying(150) NOT NULL,
    tema text NOT NULL,
    concepto text,
    unidad_medida character varying(50) NOT NULL,
    formula character varying(300) NOT NULL,
    observacion text,
    campos_indicador text,
    confiabilidad integer,
    updated_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    id_periodo integer,
    ultima_lectura timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    es_acumulado boolean,
    agencia_id integer,
    id_sala_reporte integer,
    meta double precision,
    codigo character varying(100) DEFAULT NULL::character varying,
    ruta text,
    cantidad_decimales integer
);


--
-- Name: ficha_tecnica_campo; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.ficha_tecnica_campo (
    id_ficha_tecnica integer NOT NULL,
    id_campo integer NOT NULL
);


--
-- Name: ficha_tecnica_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.ficha_tecnica_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ficha_tecnica_variable_dato; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.ficha_tecnica_variable_dato (
    id_ficha_tecnica integer NOT NULL,
    id_variable_dato integer NOT NULL
);


--
-- Name: fichatecnica_clasificaciontecnica; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.fichatecnica_clasificaciontecnica (
    fichatecnica_id integer NOT NULL,
    clasificaciontecnica_id integer NOT NULL
);


--
-- Name: fichatecnica_tiposgraficos; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.fichatecnica_tiposgraficos (
    fichatecnica_id integer NOT NULL,
    tipografico_id integer NOT NULL
);


--
-- Name: fila_origen_dato_v2; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.fila_origen_dato_v2 (
    id_origen_dato integer,
    datos jsonb,
    ultima_lectura timestamp without time zone,
    id_conexion integer
);


--
-- Name: fos_user_group; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.fos_user_group (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    roles text NOT NULL
);


--
-- Name: COLUMN fos_user_group.roles; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.fos_user_group.roles IS '(DC2Type:array)';


--
-- Name: fos_user_group_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.fos_user_group_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.fos_user_group_id_seq OWNER TO admin;

--
-- Name: fos_user_user; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.fos_user_user (
    id integer NOT NULL,
    username character varying(180) NOT NULL,
    username_canonical character varying(180) NOT NULL,
    email character varying(180) NOT NULL,
    email_canonical character varying(180) NOT NULL,
    enabled boolean NOT NULL,
    salt character varying(255),
    password character varying(255) NOT NULL,
    last_login timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    confirmation_token character varying(180) DEFAULT NULL::character varying,
    password_requested_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    roles text NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    date_of_birth timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    firstname character varying(64) DEFAULT NULL::character varying,
    lastname character varying(64) DEFAULT NULL::character varying,
    website character varying(64) DEFAULT NULL::character varying,
    biography character varying(1000) DEFAULT NULL::character varying,
    gender character varying(1) DEFAULT NULL::character varying,
    locale character varying(8) DEFAULT NULL::character varying,
    timezone character varying(64) DEFAULT NULL::character varying,
    phone character varying(64) DEFAULT NULL::character varying,
    facebook_uid character varying(255) DEFAULT NULL::character varying,
    facebook_name character varying(255) DEFAULT NULL::character varying,
    facebook_data text,
    twitter_uid character varying(255) DEFAULT NULL::character varying,
    twitter_name character varying(255) DEFAULT NULL::character varying,
    twitter_data text,
    gplus_uid character varying(255) DEFAULT NULL::character varying,
    gplus_name character varying(255) DEFAULT NULL::character varying,
    gplus_data text,
    token character varying(255) DEFAULT NULL::character varying,
    two_step_code character varying(255) DEFAULT NULL::character varying,
    clasificacionuso_id integer,
    agencia_id integer,
    establecimientoprincipal_id integer
);


--
-- Name: COLUMN fos_user_user.roles; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.fos_user_user.roles IS '(DC2Type:array)';


--
-- Name: COLUMN fos_user_user.facebook_data; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.fos_user_user.facebook_data IS '(DC2Type:json)';


--
-- Name: COLUMN fos_user_user.twitter_data; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.fos_user_user.twitter_data IS '(DC2Type:json)';


--
-- Name: COLUMN fos_user_user.gplus_data; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.fos_user_user.gplus_data IS '(DC2Type:json)';


--
-- Name: fos_user_user_group; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.fos_user_user_group (
    user_id integer NOT NULL,
    group_id integer NOT NULL
);


--
-- Name: fos_user_user_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.fos_user_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: fuente_dato; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.fuente_dato (
    id integer NOT NULL,
    establecimiento character varying(100) NOT NULL,
    contacto character varying(100) NOT NULL,
    correo character varying(50) NOT NULL,
    telefono character varying(15) NOT NULL,
    cargo character varying(50) NOT NULL
);


--
-- Name: fuente_dato_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.fuente_dato_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: fusion_origenes_datos; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.fusion_origenes_datos (
    id integer NOT NULL,
    id_origen_datos bigint,
    id_origen_datos_fusionado bigint,
    campos text
);


--
-- Name: fusion_origenes_datos_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.fusion_origenes_datos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: group_fichatecnica; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.group_fichatecnica (
    group_id integer NOT NULL,
    fichatecnica_id integer NOT NULL
);


--
-- Name: group_grupoindicadores; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.group_grupoindicadores (
    group_id integer NOT NULL,
    grupoindicadores_id integer NOT NULL
);


--
-- Name: grupo_establecimiento; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.grupo_establecimiento (
    id_grupo integer,
    codigo_establecimiento character varying(20)
);


--
-- Name: grupo_indicadores; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.grupo_indicadores (
    id integer NOT NULL,
    nombre character varying(50) NOT NULL,
    updated_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: grupo_indicadores_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.grupo_indicadores_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: grupo_indicadores_indicador; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.grupo_indicadores_indicador (
    id integer NOT NULL,
    indicador_id integer,
    grupo_indicadores_id integer,
    dimension character varying(50) NOT NULL,
    filtro character varying(500) DEFAULT NULL::character varying,
    filtro_posicion_desde character varying(10) DEFAULT NULL::character varying,
    filtro_posicion_hasta character varying(10) DEFAULT NULL::character varying,
    filtro_elementos text,
    posicion integer,
    tipo_grafico character varying(50) NOT NULL,
    vista character varying(20) DEFAULT NULL::character varying,
    orden text DEFAULT NULL::character varying
);


--
-- Name: grupo_indicadores_indicador_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.grupo_indicadores_indicador_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: imagen_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.imagen_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- Name: indicador_agencia; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.indicador_agencia (
    id_agencia integer NOT NULL,
    id_indicador integer NOT NULL
);


--
-- Name: indicador_alertas; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.indicador_alertas (
    id integer NOT NULL,
    id_color_alerta integer NOT NULL,
    id_indicador integer NOT NULL,
    limite_inferior double precision NOT NULL,
    limite_superior double precision NOT NULL,
    comentario text
);


--
-- Name: indicador_alertas_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.indicador_alertas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: indicador_usuario; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.indicador_usuario (
    id_usuario integer NOT NULL,
    id_indicador integer NOT NULL
);


--
-- Name: indicador_variablecaptura; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.indicador_variablecaptura (
    indicador_id integer NOT NULL,
    variablecaptura_id integer NOT NULL
);


--
-- Name: matriz_indicadores_desempeno; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_indicadores_desempeno (
    id integer NOT NULL,
    id_matriz integer,
    nombre character varying(500) NOT NULL,
    orden character varying(4) DEFAULT NULL::character varying,
    creado timestamp(0) without time zone NOT NULL,
    actualizado timestamp(0) without time zone NOT NULL
);

--
-- Name: matriz_indicadores_desempeno_ficha_tecnica; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_indicadores_desempeno_ficha_tecnica (
    matriz_indicadores_desempeno_id integer NOT NULL,
    ficha_tecnica_id integer NOT NULL
);


--
-- Name: matriz_indicadores_desempeno_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.matriz_indicadores_desempeno_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: matriz_indicadores_etab; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_indicadores_etab (
    id integer NOT NULL,
    id_ficha_tecnica integer NOT NULL,
    id_desempeno integer NOT NULL,
    filtros text,
    creado timestamp(0) without time zone NOT NULL,
    actualizado timestamp(0) without time zone NOT NULL
);


--
-- Name: matriz_indicadores_etab_alertas; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_indicadores_etab_alertas (
    id integer NOT NULL,
    matriz_indicador_etab_id integer NOT NULL,
    limite_inferior double precision,
    limite_superior double precision,
    color text NOT NULL,
    creado timestamp(0) without time zone NOT NULL,
    actualizado timestamp(0) without time zone NOT NULL
);


--
-- Name: matriz_indicadores_etab_alertas_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.matriz_indicadores_etab_alertas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: matriz_indicadores_etab_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.matriz_indicadores_etab_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: matriz_indicadores_relacion; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_indicadores_relacion (
    id integer NOT NULL,
    id_desempeno integer NOT NULL,
    nombre character varying(500) NOT NULL,
    fuente character varying(500) DEFAULT NULL::character varying,
    creado timestamp(0) without time zone NOT NULL,
    actualizado timestamp(0) without time zone NOT NULL
);


--
-- Name: matriz_indicadores_relacion_alertas; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_indicadores_relacion_alertas (
    id integer NOT NULL,
    matriz_indicador_relacion_id integer NOT NULL,
    limite_inferior double precision,
    limite_superior double precision,
    color text NOT NULL,
    creado timestamp(0) without time zone NOT NULL,
    actualizado timestamp(0) without time zone NOT NULL
);


--
-- Name: matriz_indicadores_relacion_alertas_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.matriz_indicadores_relacion_alertas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: matriz_indicadores_relacion_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.matriz_indicadores_relacion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: matriz_indicadores_usuario; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_indicadores_usuario (
    id integer NOT NULL,
    id_matriz integer,
    id_usuario integer,
    creado timestamp(0) without time zone NOT NULL,
    actualizado timestamp(0) without time zone NOT NULL
);


--
-- Name: matriz_indicadores_usuario_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.matriz_indicadores_usuario_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: matriz_seguimiento; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_seguimiento (
    id integer NOT NULL,
    id_desempeno integer,
    anio character varying(4) NOT NULL,
    etab boolean,
    meta character varying(65) DEFAULT NULL::character varying,
    indicador integer NOT NULL,
    creado timestamp(0) without time zone NOT NULL,
    actualizado timestamp(0) without time zone NOT NULL
);


--
-- Name: matriz_seguimiento_dato; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_seguimiento_dato (
    id integer NOT NULL,
    id_matriz integer,
    mes character varying(20) NOT NULL,
    planificado character varying(20) NOT NULL,
    "real" character varying(20) DEFAULT NULL::character varying,
    creado timestamp(0) without time zone NOT NULL,
    actualizado timestamp(0) without time zone NOT NULL
);


--
-- Name: matriz_seguimiento_dato_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.matriz_seguimiento_dato_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: matriz_seguimiento_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.matriz_seguimiento_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: matriz_seguimiento_matriz; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.matriz_seguimiento_matriz (
    id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text
);


--
-- Name: matriz_seguimiento_matriz_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.matriz_seguimiento_matriz_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: motor_bd; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.motor_bd (
    id integer NOT NULL,
    nombre character varying(50) NOT NULL,
    codigo character varying(20) DEFAULT NULL::character varying
);


--
-- Name: motor_bd_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.motor_bd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: origen_datos; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.origen_datos (
    id bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text,
    sentencia_sql text,
    archivo_nombre character varying(255) DEFAULT NULL::character varying,
    es_fusionado boolean,
    es_catalogo boolean,
    nombre_catalogo character varying(100) DEFAULT NULL::character varying,
    es_pivote boolean,
    area_costeo character varying(50) DEFAULT NULL::character varying,
    campos_fusionados text,
    ultima_actualizacion timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    ventana_limite_inferior integer,
    ventana_limite_superior integer,
    campolecturaincremental_id integer,
    tiempo_segundos_ultima_carga integer,
    carga_finalizada boolean,
    error_carga boolean,
    mensaje_error_carga text,
    valor_corte character varying(50),
    formato_valor_corte character varying(100) DEFAULT 'Y-m-d H:i:s'::character varying,
    acciones_poscarga text
);


--
-- Name: origen_datos_fusiones; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.origen_datos_fusiones (
    id_origen_dato bigint NOT NULL,
    id_origen_dato_fusionado bigint NOT NULL
);


--
-- Name: origen_datos_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.origen_datos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: origenes_conexiones; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.origenes_conexiones (
    origendatos_id bigint NOT NULL,
    conexion_id integer NOT NULL
);


--
-- Name: periodos; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.periodos (
    id integer NOT NULL,
    descripcion character varying(25) NOT NULL,
    codigo character varying(7) NOT NULL,
    sentencia text
);


--
-- Name: periodos_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.periodos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rangos_alertas_generales; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.rangos_alertas_generales (
    limite_inferior double precision NOT NULL,
    limite_superior double precision NOT NULL,
    color character varying(50) NOT NULL,
    numero_rango real
);


--
-- Name: regla_transformacion; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.regla_transformacion (
    id integer NOT NULL,
    id_diccionario integer,
    regla character varying(15) NOT NULL,
    limite_inferior character varying(100) NOT NULL,
    limite_superior character varying(100) DEFAULT NULL::character varying,
    transformacion character varying(100) NOT NULL
);


--
-- Name: regla_transformacion_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.regla_transformacion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: responsable_dato; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.responsable_dato (
    id integer NOT NULL,
    establecimiento character varying(100) NOT NULL,
    contacto character varying(100) NOT NULL,
    correo character varying(50) NOT NULL,
    telefono character varying(15) NOT NULL,
    cargo character varying(50) NOT NULL
);


--
-- Name: responsable_dato_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.responsable_dato_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: responsable_indicador; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.responsable_indicador (
    id integer NOT NULL,
    establecimiento character varying(100) NOT NULL,
    contacto character varying(100) NOT NULL,
    correo character varying(50) NOT NULL,
    telefono character varying(15) NOT NULL,
    cargo character varying(50) NOT NULL
);


--
-- Name: responsable_indicador_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.responsable_indicador_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- Name: sala_acciones; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.sala_acciones (
    id integer NOT NULL,
    grupo_indicadores_id integer,
    usuario_id integer,
    acciones text NOT NULL,
    observaciones text,
    responsables text,
    fecha timestamp(0) without time zone NOT NULL
);


--
-- Name: sala_acciones_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.sala_acciones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sala_comentarios; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.sala_comentarios (
    id integer NOT NULL,
    grupo_indicadores_id integer,
    usuario_id integer,
    comentario text NOT NULL,
    fecha timestamp(0) without time zone NOT NULL
);


--
-- Name: sala_comentarios_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.sala_comentarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: significado_campo; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.significado_campo (
    id integer NOT NULL,
    descripcion character varying(200) NOT NULL,
    codigo character varying(40) NOT NULL,
    uso_en_catalogo boolean,
    catalogo character varying(255) DEFAULT NULL::character varying,
    nombre_mapa character varying(200),
    escala double precision,
    origen_x double precision,
    origen_y double precision,
    uso_costeo boolean,
    acumulable boolean
);


--
-- Name: significado_campo_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.significado_campo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: significados_tipos_graficos; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.significados_tipos_graficos (
    significadocampo_id integer NOT NULL,
    tipografico_id integer NOT NULL
);


--
-- Name: social; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.social (
    id integer NOT NULL,
    sala integer,
    creado timestamp(0) without time zone NOT NULL,
    token character varying(72) NOT NULL,
    tiempo_dias integer,
    es_permanente boolean
);


--
-- Name: social_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.social_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: social_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.social_id_seq OWNED BY public.social.id;


--
-- Name: tipo_campo; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.tipo_campo (
    id integer NOT NULL,
    descripcion character varying(50) DEFAULT NULL::character varying,
    codigo character varying(50) NOT NULL
);

--
-- Name: tipo_campo_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.tipo_campo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tipo_grafico; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.tipo_grafico (
    id integer NOT NULL,
    descripcion character varying(50) DEFAULT NULL::character varying,
    codigo character varying(50) NOT NULL
);


--
-- Name: tipo_grafico_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.tipo_grafico_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuario_grupo_indicadores; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.usuario_grupo_indicadores (
    grupo_indicadores_id integer NOT NULL,
    usuario_id integer NOT NULL,
    es_duenio boolean,
    usuario_asigno_id integer
);


--
-- Name: usuario_indicadores_favoritos; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.usuario_indicadores_favoritos (
    id_usuario integer NOT NULL,
    id_indicador integer NOT NULL
);


--
-- Name: variable_dato; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.variable_dato (
    id integer NOT NULL,
    id_fuente_dato integer,
    id_responsable_dato integer,
    id_origen_datos bigint,
    nombre character varying(200) NOT NULL,
    confiabilidad integer,
    iniciales character varying(255) NOT NULL,
    comentario text,
    es_poblacion boolean
);

--
-- Name: variable_dato_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.variable_dato_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: configuracion_pivot_table id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.configuracion_pivot_table ALTER COLUMN id SET DEFAULT nextval('public.configuracion_pivot_table_id_seq'::regclass);


--
-- Name: social id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.social ALTER COLUMN id SET DEFAULT nextval('public.social_id_seq'::regclass);


--
-- Data for Name: acceso_externo; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.acceso_externo (id, token, caducidad, usuariocrea_id) FROM stdin;
\.


--
-- Data for Name: accesoexterno_grupoindicadores; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.accesoexterno_grupoindicadores (accesoexterno_id, grupoindicadores_id) FROM stdin;
\.


--
-- Data for Name: agencia; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.agencia (id, codigo, nombre) FROM stdin;
\.


--
-- Data for Name: agencia_formulario; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.agencia_formulario (id_agencia, id_formulario) FROM stdin;
\.


--
-- Data for Name: alerta; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.alerta (id, codigo, color) FROM stdin;
1	green	Verde
3	red	Rojo
2	#FF8000	Naranja
4	#FFFF66	Amarillo
5	#7700ff	Violeta
6	#00ffd4	Acua
\.


--
-- Data for Name: bitacora; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.bitacora (id, id_usuario, id_session, fecha_hora, accion, elemento) FROM stdin;
\.


--
-- Data for Name: boletin; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.boletin (id, sala, grupo, nombre, creado, actualizado, token) FROM stdin;
\.


--
-- Data for Name: campo; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.campo (id, id_origen_datos, id_tipo_campo, id_significado_campo, nombre, descripcion, id_diccionario, formula) FROM stdin;
\.


--
-- Data for Name: clasificacion_nivel; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.clasificacion_nivel (id, codigo, descripcion, comentario) FROM stdin;
\.


--
-- Data for Name: clasificacion_tecnica; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.clasificacion_tecnica (id, codigo, descripcion, comentario, clasificacionuso_id) FROM stdin;
\.


--
-- Data for Name: clasificacion_uso; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.clasificacion_uso (id, codigo, descripcion, comentario) FROM stdin;
\.


--
-- Data for Name: conexion; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.conexion (id, id_motor, nombre_conexion, comentario, ip, usuario, clave, nombre_base_datos, puerto, instancia) FROM stdin;
\.


--
-- Data for Name: configuracion_pivot_table; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.configuracion_pivot_table (id, nombre, por_defecto, configuracion, id_elemento, tipo_elemento, id_usuario) FROM stdin;
\.


--
-- Data for Name: diccionario; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.diccionario (id, descripcion, codigo) FROM stdin;
\.


--
-- Data for Name: ficha_tecnica; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.ficha_tecnica (id, nombre, tema, concepto, unidad_medida, formula, observacion, campos_indicador, confiabilidad, updated_at, id_periodo, ultima_lectura, es_acumulado, agencia_id, id_sala_reporte, meta, codigo, ruta, cantidad_decimales) FROM stdin;
\.


--
-- Data for Name: ficha_tecnica_campo; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.ficha_tecnica_campo (id_ficha_tecnica, id_campo) FROM stdin;
\.


--
-- Data for Name: ficha_tecnica_variable_dato; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.ficha_tecnica_variable_dato (id_ficha_tecnica, id_variable_dato) FROM stdin;
\.


--
-- Data for Name: fichatecnica_clasificaciontecnica; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.fichatecnica_clasificaciontecnica (fichatecnica_id, clasificaciontecnica_id) FROM stdin;
\.


--
-- Data for Name: fichatecnica_tiposgraficos; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.fichatecnica_tiposgraficos (fichatecnica_id, tipografico_id) FROM stdin;
\.


--
-- Data for Name: fila_origen_dato_v2; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.fila_origen_dato_v2 (id_origen_dato, datos, ultima_lectura, id_conexion) FROM stdin;
\.


--
-- Data for Name: fos_user_group; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.fos_user_group (id, name, roles) FROM stdin;
\.


--
-- Data for Name: fos_user_user; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.fos_user_user (id, username, username_canonical, email, email_canonical, enabled, salt, password, last_login, confirmation_token, password_requested_at, roles, created_at, updated_at, date_of_birth, firstname, lastname, website, biography, gender, locale, timezone, phone, facebook_uid, facebook_name, facebook_data, twitter_uid, twitter_name, twitter_data, gplus_uid, gplus_name, gplus_data, token, two_step_code, clasificacionuso_id, agencia_id, establecimientoprincipal_id) FROM stdin;
\.


--
-- Data for Name: fos_user_user_group; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.fos_user_user_group (user_id, group_id) FROM stdin;
\.


--
-- Data for Name: fuente_dato; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.fuente_dato (id, establecimiento, contacto, correo, telefono, cargo) FROM stdin;
\.


--
-- Data for Name: fusion_origenes_datos; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.fusion_origenes_datos (id, id_origen_datos, id_origen_datos_fusionado, campos) FROM stdin;
\.


--
-- Data for Name: group_fichatecnica; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.group_fichatecnica (group_id, fichatecnica_id) FROM stdin;
\.


--
-- Data for Name: group_grupoindicadores; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.group_grupoindicadores (group_id, grupoindicadores_id) FROM stdin;
\.


--
-- Data for Name: grupo_establecimiento; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.grupo_establecimiento (id_grupo, codigo_establecimiento) FROM stdin;
\.


--
-- Data for Name: grupo_indicadores; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.grupo_indicadores (id, nombre, updated_at) FROM stdin;
\.


--
-- Data for Name: grupo_indicadores_indicador; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.grupo_indicadores_indicador (id, indicador_id, grupo_indicadores_id, dimension, filtro, filtro_posicion_desde, filtro_posicion_hasta, filtro_elementos, posicion, tipo_grafico, vista, orden) FROM stdin;
\.


--
-- Data for Name: indicador_agencia; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.indicador_agencia (id_agencia, id_indicador) FROM stdin;
\.


--
-- Data for Name: indicador_alertas; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.indicador_alertas (id, id_color_alerta, id_indicador, limite_inferior, limite_superior, comentario) FROM stdin;
\.


--
-- Data for Name: indicador_usuario; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.indicador_usuario (id_usuario, id_indicador) FROM stdin;
\.


--
-- Data for Name: indicador_variablecaptura; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.indicador_variablecaptura (indicador_id, variablecaptura_id) FROM stdin;
\.


--
-- Data for Name: matriz_indicadores_desempeno; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_indicadores_desempeno (id, id_matriz, nombre, orden, creado, actualizado) FROM stdin;
\.


--
-- Data for Name: matriz_indicadores_desempeno_ficha_tecnica; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_indicadores_desempeno_ficha_tecnica (matriz_indicadores_desempeno_id, ficha_tecnica_id) FROM stdin;
\.


--
-- Data for Name: matriz_indicadores_etab; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_indicadores_etab (id, id_ficha_tecnica, id_desempeno, filtros, creado, actualizado) FROM stdin;
\.


--
-- Data for Name: matriz_indicadores_etab_alertas; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_indicadores_etab_alertas (id, matriz_indicador_etab_id, limite_inferior, limite_superior, color, creado, actualizado) FROM stdin;
\.


--
-- Data for Name: matriz_indicadores_relacion; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_indicadores_relacion (id, id_desempeno, nombre, fuente, creado, actualizado) FROM stdin;
\.


--
-- Data for Name: matriz_indicadores_relacion_alertas; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_indicadores_relacion_alertas (id, matriz_indicador_relacion_id, limite_inferior, limite_superior, color, creado, actualizado) FROM stdin;
\.


--
-- Data for Name: matriz_indicadores_usuario; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_indicadores_usuario (id, id_matriz, id_usuario, creado, actualizado) FROM stdin;
\.


--
-- Data for Name: matriz_seguimiento; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_seguimiento (id, id_desempeno, anio, etab, meta, indicador, creado, actualizado) FROM stdin;
\.


--
-- Data for Name: matriz_seguimiento_dato; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_seguimiento_dato (id, id_matriz, mes, planificado, "real", creado, actualizado) FROM stdin;
\.


--
-- Data for Name: matriz_seguimiento_matriz; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.matriz_seguimiento_matriz (id, nombre, descripcion) FROM stdin;
\.


--
-- Data for Name: motor_bd; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.motor_bd (id, nombre, codigo) FROM stdin;
1	MySQL	pdo_mysql
2	PostgreSQL	pdo_pgsql
3	SQLite	pdo_sqlite
5	Oracle	oci8
4	SQL Server	sqlsrv
\.


--
-- Data for Name: origen_datos; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.origen_datos (id, nombre, descripcion, sentencia_sql, archivo_nombre, es_fusionado, es_catalogo, nombre_catalogo, es_pivote, area_costeo, campos_fusionados, ultima_actualizacion, ventana_limite_inferior, ventana_limite_superior, campolecturaincremental_id, tiempo_segundos_ultima_carga, carga_finalizada, error_carga, mensaje_error_carga, valor_corte, formato_valor_corte, acciones_poscarga) FROM stdin;
\.


--
-- Data for Name: origen_datos_fusiones; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.origen_datos_fusiones (id_origen_dato, id_origen_dato_fusionado) FROM stdin;
\.


--
-- Data for Name: origenes_conexiones; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.origenes_conexiones (origendatos_id, conexion_id) FROM stdin;
\.


--
-- Data for Name: periodos; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.periodos (id, descripcion, codigo, sentencia) FROM stdin;
1	Anual	a	\N
2	Mensual	m	\N
3	Trimestral	t	\N
4	Semestral	s	\N
5	Semanal	sm	\N
6	Diario	d	\N
\.


--
-- Data for Name: rangos_alertas_generales; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.rangos_alertas_generales (limite_inferior, limite_superior, color, numero_rango) FROM stdin;
0	59.8999999999999986	#D73925	1
60	79.9000000000000057	#ffa500	2
80	100	#008D4C	3
\.


--
-- Data for Name: regla_transformacion; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.regla_transformacion (id, id_diccionario, regla, limite_inferior, limite_superior, transformacion) FROM stdin;
\.


--
-- Data for Name: responsable_dato; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.responsable_dato (id, establecimiento, contacto, correo, telefono, cargo) FROM stdin;
1	Establecimiento-lugar responsable de dato, ejemplo1	nombre persona contacto ejemplo 1	correo persona contacto ejemplo1	9999-9999	cargo persona contacto ejemplo 1
\.


--
-- Data for Name: responsable_indicador; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.responsable_indicador (id, establecimiento, contacto, correo, telefono, cargo) FROM stdin;
1	Egresos SIMMOW	Egresos SIMMOW	simmow@salud.gob.sv	9999-9999	cargo persona contacto ejemplo 1
\.


--
-- Data for Name: sala_acciones; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.sala_acciones (id, grupo_indicadores_id, usuario_id, acciones, observaciones, responsables, fecha) FROM stdin;
\.


--
-- Data for Name: sala_comentarios; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.sala_comentarios (id, grupo_indicadores_id, usuario_id, comentario, fecha) FROM stdin;
\.


--
-- Data for Name: significado_campo; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.significado_campo (id, descripcion, codigo, uso_en_catalogo, catalogo, nombre_mapa, escala, origen_x, origen_y, uso_costeo, acumulable) FROM stdin;
10	Campo para cálculos	calculo	f	\N	\N	\N	\N	\N	\N	\N
18	Clave primaria	pk	t	\N	\N	\N	\N	\N	\N	\N
19	Clave foránea	fk	t	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: significados_tipos_graficos; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.significados_tipos_graficos (significadocampo_id, tipografico_id) FROM stdin;
\.


--
-- Data for Name: social; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.social (id, sala, creado, token, tiempo_dias, es_permanente) FROM stdin;
\.


--
-- Data for Name: tipo_campo; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.tipo_campo (id, descripcion, codigo) FROM stdin;
1	Número flotante	float
2	Texto	text
3	Fecha	date
4	Cadena de texto	varchar(255)
5	Entero	integer
\.


--
-- Data for Name: tipo_grafico; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.tipo_grafico (id, descripcion, codigo) FROM stdin;
4	Mapa	mapa
1	Columnas	columnas
2	Pastel	pastel
3	Linea	lineas
5	Odómetro	gauge
6	Termómetro	lineargauge
\.


--
-- Data for Name: usuario_grupo_indicadores; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.usuario_grupo_indicadores (grupo_indicadores_id, usuario_id, es_duenio, usuario_asigno_id) FROM stdin;
\.


--
-- Data for Name: usuario_indicadores_favoritos; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.usuario_indicadores_favoritos (id_usuario, id_indicador) FROM stdin;
\.


--
-- Data for Name: variable_dato; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.variable_dato (id, id_fuente_dato, id_responsable_dato, id_origen_datos, nombre, confiabilidad, iniciales, comentario, es_poblacion) FROM stdin;
\.


--
-- Name: acceso_externo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.acceso_externo_id_seq', 21, true);


--
-- Name: agencia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.agencia_id_seq', 4, true);


--
-- Name: alerta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.alerta_id_seq', 6, true);


--
-- Name: area_estandar_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.area_estandar_id_seq', 1, false);


--
-- Name: bitacora_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.bitacora_id_seq', 241382, true);


--
-- Name: boletin_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.boletin_id_seq', 1, false);


--
-- Name: campo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.campo_id_seq', 5685, true);


--
-- Name: clasificacion_nivel_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.clasificacion_nivel_id_seq', 5, true);


--
-- Name: clasificacion_privacidad_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.clasificacion_privacidad_id_seq', 2, true);


--
-- Name: clasificacion_tecnica_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.clasificacion_tecnica_id_seq', 41, true);


--
-- Name: clasificacion_uso_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.clasificacion_uso_id_seq', 20, true);


--
-- Name: codificacion_caracteres_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.codificacion_caracteres_id_seq', 1, false);


--
-- Name: conexion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.conexion_id_seq', 188, true);


--
-- Name: configuracion_pivot_table_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.configuracion_pivot_table_id_seq', 3, true);


--
-- Name: diccionario_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.diccionario_id_seq', 1, false);


--
-- Name: ficha_tecnica_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.ficha_tecnica_id_seq', 432, true);


--
-- Name: fos_user_group_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.fos_user_group_id_seq', 14, true);


--
-- Name: fos_user_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.fos_user_user_id_seq', 3136, true);


--
-- Name: fuente_dato_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.fuente_dato_id_seq', 3, true);


--
-- Name: fusion_origenes_datos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.fusion_origenes_datos_id_seq', 1, false);


--
-- Name: grupo_indicadores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.grupo_indicadores_id_seq', 687, true);


--
-- Name: grupo_indicadores_indicador_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.grupo_indicadores_indicador_id_seq', 5641, true);


--
-- Name: imagen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.imagen_id_seq', 1, false);


--
-- Name: indicador_alertas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.indicador_alertas_id_seq', 134, true);


--
-- Name: matriz_indicadores_desempeno_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.matriz_indicadores_desempeno_id_seq', 37, true);


--
-- Name: matriz_indicadores_etab_alertas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.matriz_indicadores_etab_alertas_id_seq', 1, false);


--
-- Name: matriz_indicadores_etab_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.matriz_indicadores_etab_id_seq', 1, true);


--
-- Name: matriz_indicadores_relacion_alertas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.matriz_indicadores_relacion_alertas_id_seq', 101, true);


--
-- Name: matriz_indicadores_relacion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.matriz_indicadores_relacion_id_seq', 100, true);


--
-- Name: matriz_indicadores_usuario_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.matriz_indicadores_usuario_id_seq', 1, false);


--
-- Name: matriz_seguimiento_dato_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.matriz_seguimiento_dato_id_seq', 144, true);


--
-- Name: matriz_seguimiento_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.matriz_seguimiento_id_seq', 51, true);


--
-- Name: matriz_seguimiento_matriz_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.matriz_seguimiento_matriz_id_seq', 5, true);


--
-- Name: motor_bd_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.motor_bd_id_seq', 5, true);


--
-- Name: origen_datos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.origen_datos_id_seq', 559, true);


--
-- Name: periodos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.periodos_id_seq', 4, true);


--
-- Name: regla_transformacion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.regla_transformacion_id_seq', 3, true);


--
-- Name: responsable_dato_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.responsable_dato_id_seq', 1, true);


--
-- Name: responsable_indicador_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.responsable_indicador_id_seq', 1, true);


--
-- Name: sala_acciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.sala_acciones_id_seq', 73, true);


--
-- Name: sala_comentarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.sala_comentarios_id_seq', 266, true);


--
-- Name: significado_campo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.significado_campo_id_seq', 420, true);


--
-- Name: social_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.social_id_seq', 17, true);


--
-- Name: tipo_campo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.tipo_campo_id_seq', 5, true);


--
-- Name: tipo_grafico_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.tipo_grafico_id_seq', 1, false);


--
-- Name: variable_dato_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.variable_dato_id_seq', 468, true);


--
-- Name: acceso_externo acceso_externo_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.acceso_externo
    ADD CONSTRAINT acceso_externo_pkey PRIMARY KEY (id);


--
-- Name: accesoexterno_grupoindicadores accesoexterno_grupoindicadores_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.accesoexterno_grupoindicadores
    ADD CONSTRAINT accesoexterno_grupoindicadores_pkey PRIMARY KEY (accesoexterno_id, grupoindicadores_id);


--
-- Name: agencia agencia_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.agencia
    ADD CONSTRAINT agencia_pkey PRIMARY KEY (id);


--
-- Name: alerta alerta_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.alerta
    ADD CONSTRAINT alerta_pkey PRIMARY KEY (id);


--
-- Name: bitacora bitacora_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bitacora
    ADD CONSTRAINT bitacora_pkey PRIMARY KEY (id);


--
-- Name: boletin boletin_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.boletin
    ADD CONSTRAINT boletin_pkey PRIMARY KEY (id);


--
-- Name: campo campo_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.campo
    ADD CONSTRAINT campo_pkey PRIMARY KEY (id);


--
-- Name: clasificacion_nivel clasificacion_nivel_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clasificacion_nivel
    ADD CONSTRAINT clasificacion_nivel_pkey PRIMARY KEY (id);


--
-- Name: clasificacion_tecnica clasificacion_tecnica_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clasificacion_tecnica
    ADD CONSTRAINT clasificacion_tecnica_pkey PRIMARY KEY (id);


--
-- Name: clasificacion_uso clasificacion_uso_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clasificacion_uso
    ADD CONSTRAINT clasificacion_uso_pkey PRIMARY KEY (id);


--
-- Name: conexion conexion_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.conexion
    ADD CONSTRAINT conexion_pkey PRIMARY KEY (id);


--
-- Name: configuracion_pivot_table configuracion_pivot_table_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.configuracion_pivot_table
    ADD CONSTRAINT configuracion_pivot_table_pkey PRIMARY KEY (id);


--
-- Name: diccionario diccionario_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.diccionario
    ADD CONSTRAINT diccionario_pkey PRIMARY KEY (id);


--
-- Name: ficha_tecnica_campo ficha_tecnica_campo_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ficha_tecnica_campo
    ADD CONSTRAINT ficha_tecnica_campo_pkey PRIMARY KEY (id_ficha_tecnica, id_campo);


--
-- Name: ficha_tecnica ficha_tecnica_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ficha_tecnica
    ADD CONSTRAINT ficha_tecnica_pkey PRIMARY KEY (id);


--
-- Name: ficha_tecnica_variable_dato ficha_tecnica_variable_dato_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ficha_tecnica_variable_dato
    ADD CONSTRAINT ficha_tecnica_variable_dato_pkey PRIMARY KEY (id_ficha_tecnica, id_variable_dato);


--
-- Name: fichatecnica_clasificaciontecnica fichatecnica_clasificaciontecnica_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fichatecnica_clasificaciontecnica
    ADD CONSTRAINT fichatecnica_clasificaciontecnica_pkey PRIMARY KEY (fichatecnica_id, clasificaciontecnica_id);


--
-- Name: fichatecnica_tiposgraficos fichatecnica_tiposgraficos_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fichatecnica_tiposgraficos
    ADD CONSTRAINT fichatecnica_tiposgraficos_pkey PRIMARY KEY (fichatecnica_id, tipografico_id);


--
-- Name: fos_user_group fos_user_group_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fos_user_group
    ADD CONSTRAINT fos_user_group_pkey PRIMARY KEY (id);


--
-- Name: fos_user_user_group fos_user_user_group_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fos_user_user_group
    ADD CONSTRAINT fos_user_user_group_pkey PRIMARY KEY (user_id, group_id);


--
-- Name: fos_user_user fos_user_user_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fos_user_user
    ADD CONSTRAINT fos_user_user_pkey PRIMARY KEY (id);


--
-- Name: fuente_dato fuente_dato_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fuente_dato
    ADD CONSTRAINT fuente_dato_pkey PRIMARY KEY (id);


--
-- Name: fusion_origenes_datos fusion_origenes_datos_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fusion_origenes_datos
    ADD CONSTRAINT fusion_origenes_datos_pkey PRIMARY KEY (id);


--
-- Name: group_fichatecnica group_fichatecnica_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.group_fichatecnica
    ADD CONSTRAINT group_fichatecnica_pkey PRIMARY KEY (group_id, fichatecnica_id);


--
-- Name: group_grupoindicadores group_grupoindicadores_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.group_grupoindicadores
    ADD CONSTRAINT group_grupoindicadores_pkey PRIMARY KEY (group_id, grupoindicadores_id);


--
-- Name: grupo_indicadores_indicador grupo_indicadores_indicador_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.grupo_indicadores_indicador
    ADD CONSTRAINT grupo_indicadores_indicador_pkey PRIMARY KEY (id);


--
-- Name: grupo_indicadores grupo_indicadores_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.grupo_indicadores
    ADD CONSTRAINT grupo_indicadores_pkey PRIMARY KEY (id);


--
-- Name: indicador_agencia indicador_agencia_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_agencia
    ADD CONSTRAINT indicador_agencia_pkey PRIMARY KEY (id_agencia, id_indicador);


--
-- Name: indicador_alertas indicador_alertas_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_alertas
    ADD CONSTRAINT indicador_alertas_pkey PRIMARY KEY (id);


--
-- Name: agencia_formulario indicador_formulario_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.agencia_formulario
    ADD CONSTRAINT indicador_formulario_pkey PRIMARY KEY (id_agencia, id_formulario);


--
-- Name: indicador_usuario indicador_usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_usuario
    ADD CONSTRAINT indicador_usuario_pkey PRIMARY KEY (id_usuario, id_indicador);


--
-- Name: indicador_variablecaptura indicador_variablecaptura_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_variablecaptura
    ADD CONSTRAINT indicador_variablecaptura_pkey PRIMARY KEY (indicador_id, variablecaptura_id);


--
-- Name: matriz_indicadores_desempeno_ficha_tecnica matriz_indicadores_desempeno_ficha_tecnica_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_desempeno_ficha_tecnica
    ADD CONSTRAINT matriz_indicadores_desempeno_ficha_tecnica_pkey PRIMARY KEY (matriz_indicadores_desempeno_id, ficha_tecnica_id);


--
-- Name: matriz_indicadores_desempeno matriz_indicadores_desempeno_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_desempeno
    ADD CONSTRAINT matriz_indicadores_desempeno_pkey PRIMARY KEY (id);


--
-- Name: matriz_indicadores_etab_alertas matriz_indicadores_etab_alertas_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_etab_alertas
    ADD CONSTRAINT matriz_indicadores_etab_alertas_pkey PRIMARY KEY (id);


--
-- Name: matriz_indicadores_etab matriz_indicadores_etab_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_etab
    ADD CONSTRAINT matriz_indicadores_etab_pkey PRIMARY KEY (id);


--
-- Name: matriz_indicadores_relacion_alertas matriz_indicadores_relacion_alertas_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_relacion_alertas
    ADD CONSTRAINT matriz_indicadores_relacion_alertas_pkey PRIMARY KEY (id);


--
-- Name: matriz_indicadores_relacion matriz_indicadores_relacion_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_relacion
    ADD CONSTRAINT matriz_indicadores_relacion_pkey PRIMARY KEY (id);


--
-- Name: matriz_indicadores_usuario matriz_indicadores_usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_usuario
    ADD CONSTRAINT matriz_indicadores_usuario_pkey PRIMARY KEY (id);


--
-- Name: matriz_seguimiento_dato matriz_seguimiento_dato_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_seguimiento_dato
    ADD CONSTRAINT matriz_seguimiento_dato_pkey PRIMARY KEY (id);


--
-- Name: matriz_seguimiento_matriz matriz_seguimiento_matriz_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_seguimiento_matriz
    ADD CONSTRAINT matriz_seguimiento_matriz_pkey PRIMARY KEY (id);


--
-- Name: matriz_seguimiento matriz_seguimiento_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_seguimiento
    ADD CONSTRAINT matriz_seguimiento_pkey PRIMARY KEY (id);


--
-- Name: motor_bd motor_bd_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.motor_bd
    ADD CONSTRAINT motor_bd_pkey PRIMARY KEY (id);


--
-- Name: origen_datos_fusiones origen_datos_fusiones_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.origen_datos_fusiones
    ADD CONSTRAINT origen_datos_fusiones_pkey PRIMARY KEY (id_origen_dato, id_origen_dato_fusionado);


--
-- Name: origen_datos origen_datos_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.origen_datos
    ADD CONSTRAINT origen_datos_pkey PRIMARY KEY (id);


--
-- Name: origenes_conexiones origenes_conexiones_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.origenes_conexiones
    ADD CONSTRAINT origenes_conexiones_pkey PRIMARY KEY (origendatos_id, conexion_id);


--
-- Name: periodos periodos_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.periodos
    ADD CONSTRAINT periodos_pkey PRIMARY KEY (id);


--
-- Name: rangos_alertas_generales rangos_alertas_generales_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.rangos_alertas_generales
    ADD CONSTRAINT rangos_alertas_generales_pkey PRIMARY KEY (limite_inferior, limite_superior, color);


--
-- Name: regla_transformacion regla_transformacion_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.regla_transformacion
    ADD CONSTRAINT regla_transformacion_pkey PRIMARY KEY (id);


--
-- Name: responsable_dato responsable_dato_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.responsable_dato
    ADD CONSTRAINT responsable_dato_pkey PRIMARY KEY (id);


--
-- Name: responsable_indicador responsable_indicador_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.responsable_indicador
    ADD CONSTRAINT responsable_indicador_pkey PRIMARY KEY (id);


--
-- Name: sala_acciones sala_acciones_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.sala_acciones
    ADD CONSTRAINT sala_acciones_pkey PRIMARY KEY (id);


--
-- Name: sala_comentarios sala_comentarios_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.sala_comentarios
    ADD CONSTRAINT sala_comentarios_pkey PRIMARY KEY (id);


--
-- Name: significado_campo significado_campo_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.significado_campo
    ADD CONSTRAINT significado_campo_pkey PRIMARY KEY (id);


--
-- Name: significados_tipos_graficos significados_tipos_graficos_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.significados_tipos_graficos
    ADD CONSTRAINT significados_tipos_graficos_pkey PRIMARY KEY (significadocampo_id, tipografico_id);


--
-- Name: social social_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.social
    ADD CONSTRAINT social_pkey PRIMARY KEY (id);


--
-- Name: tipo_campo tipo_campo_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tipo_campo
    ADD CONSTRAINT tipo_campo_pkey PRIMARY KEY (id);


--
-- Name: tipo_grafico tipo_grafico_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tipo_grafico
    ADD CONSTRAINT tipo_grafico_pkey PRIMARY KEY (id);


--
-- Name: usuario_grupo_indicadores usuario_grupo_indicadores_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.usuario_grupo_indicadores
    ADD CONSTRAINT usuario_grupo_indicadores_pkey PRIMARY KEY (grupo_indicadores_id, usuario_id);


--
-- Name: usuario_indicadores_favoritos usuario_indicadores_favoritos_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.usuario_indicadores_favoritos
    ADD CONSTRAINT usuario_indicadores_favoritos_pkey PRIMARY KEY (id_usuario, id_indicador);


--
-- Name: variable_dato variable_dato_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.variable_dato
    ADD CONSTRAINT variable_dato_pkey PRIMARY KEY (id);


--
-- Name: alerta_color_idx; Type: INDEX; Schema: public; Owner: admin
--

CREATE UNIQUE INDEX alerta_color_idx ON public.alerta USING btree (color);


--
-- Name: clasificacion_nivel_codigo_idx; Type: INDEX; Schema: public; Owner: admin
--

CREATE UNIQUE INDEX clasificacion_nivel_codigo_idx ON public.clasificacion_nivel USING btree (codigo);


--
-- Name: codigo_idx; Type: INDEX; Schema: public; Owner: admin
--

CREATE UNIQUE INDEX codigo_idx ON public.agencia USING btree (codigo);


--
-- Name: idx_1c386b09f9b351c8; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_1c386b09f9b351c8 ON public.regla_transformacion USING btree (id_diccionario);


--
-- Name: idx_204cdd5f47d487d1; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_204cdd5f47d487d1 ON public.indicador_variablecaptura USING btree (indicador_id);


--
-- Name: idx_204cdd5f4cb84927; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_204cdd5f4cb84927 ON public.indicador_variablecaptura USING btree (variablecaptura_id);


--
-- Name: idx_241b7a3810bf7ab4; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_241b7a3810bf7ab4 ON public.matriz_indicadores_etab USING btree (id_ficha_tecnica);


--
-- Name: idx_241b7a3818f3a2ea; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_241b7a3818f3a2ea ON public.matriz_indicadores_etab USING btree (id_desempeno);


--
-- Name: idx_291737aa5a5f6cfb; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_291737aa5a5f6cfb ON public.campo USING btree (id_significado_campo);


--
-- Name: idx_291737aa8a212db5; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_291737aa8a212db5 ON public.campo USING btree (id_tipo_campo);


--
-- Name: idx_291737aa988f9d4d; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_291737aa988f9d4d ON public.campo USING btree (id_origen_datos);


--
-- Name: idx_291737aaf9b351c8; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_291737aaf9b351c8 ON public.campo USING btree (id_diccionario);


--
-- Name: idx_2dbd6c985e1df3fd; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_2dbd6c985e1df3fd ON public.significados_tipos_graficos USING btree (tipografico_id);


--
-- Name: idx_2dbd6c98ccc1d2cb; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_2dbd6c98ccc1d2cb ON public.significados_tipos_graficos USING btree (significadocampo_id);


--
-- Name: idx_2df8b6e117ba9678; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_2df8b6e117ba9678 ON public.group_grupoindicadores USING btree (grupoindicadores_id);


--
-- Name: idx_2df8b6e1fe54d947; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_2df8b6e1fe54d947 ON public.group_grupoindicadores USING btree (group_id);


--
-- Name: idx_3e6ab33790053a39; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_3e6ab33790053a39 ON public.sala_acciones USING btree (grupo_indicadores_id);


--
-- Name: idx_3e6ab337db38439e; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_3e6ab337db38439e ON public.sala_acciones USING btree (usuario_id);


--
-- Name: idx_4481014b17ba9678; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_4481014b17ba9678 ON public.accesoexterno_grupoindicadores USING btree (grupoindicadores_id);


--
-- Name: idx_4481014b4cc546dc; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_4481014b4cc546dc ON public.accesoexterno_grupoindicadores USING btree (accesoexterno_id);


--
-- Name: idx_44be843b90053a39; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_44be843b90053a39 ON public.sala_comentarios USING btree (grupo_indicadores_id);


--
-- Name: idx_44be843bdb38439e; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_44be843bdb38439e ON public.sala_comentarios USING btree (usuario_id);


--
-- Name: idx_49c5174d300a950e; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_49c5174d300a950e ON public.matriz_indicadores_usuario USING btree (id_matriz);


--
-- Name: idx_49c5174dfcf8192d; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_49c5174dfcf8192d ON public.matriz_indicadores_usuario USING btree (id_usuario);


--
-- Name: idx_4ce25f2e35f182e9; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_4ce25f2e35f182e9 ON public.agencia_formulario USING btree (id_agencia);


--
-- Name: idx_4ce25f2ea7c7ef6a; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_4ce25f2ea7c7ef6a ON public.agencia_formulario USING btree (id_formulario);


--
-- Name: idx_4fa6f4af300a950e; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_4fa6f4af300a950e ON public.matriz_seguimiento_dato USING btree (id_matriz);


--
-- Name: idx_50ee9f00e60c6ca3; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_50ee9f00e60c6ca3 ON public.acceso_externo USING btree (usuariocrea_id);


--
-- Name: idx_53c1aa5b27105691; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_53c1aa5b27105691 ON public.origenes_conexiones USING btree (conexion_id);


--
-- Name: idx_53c1aa5bac1a9efc; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_53c1aa5bac1a9efc ON public.origenes_conexiones USING btree (origendatos_id);


--
-- Name: idx_54dd303a4d2782e4; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_54dd303a4d2782e4 ON public.usuario_indicadores_favoritos USING btree (id_indicador);


--
-- Name: idx_54dd303afcf8192d; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_54dd303afcf8192d ON public.usuario_indicadores_favoritos USING btree (id_usuario);


--
-- Name: idx_5c340014d2782e4; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_5c340014d2782e4 ON public.indicador_usuario USING btree (id_indicador);


--
-- Name: idx_5c34001fcf8192d; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_5c34001fcf8192d ON public.indicador_usuario USING btree (id_usuario);


--
-- Name: idx_6510fe7d8c0e9bd3; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_6510fe7d8c0e9bd3 ON public.boletin USING btree (grupo);


--
-- Name: idx_6510fe7de226041c; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_6510fe7de226041c ON public.boletin USING btree (sala);


--
-- Name: idx_6f9ff171ec88bbac; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_6f9ff171ec88bbac ON public.clasificacion_tecnica USING btree (clasificacionuso_id);


--
-- Name: idx_7161e187e226041c; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_7161e187e226041c ON public.social USING btree (sala);


--
-- Name: idx_847691c125d64570; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_847691c125d64570 ON public.conexion USING btree (id_motor);


--
-- Name: idx_892e27eb1ee9a9a3; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_892e27eb1ee9a9a3 ON public.fichatecnica_clasificaciontecnica USING btree (fichatecnica_id);


--
-- Name: idx_892e27eb3b506ca8; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_892e27eb3b506ca8 ON public.fichatecnica_clasificaciontecnica USING btree (clasificaciontecnica_id);


--
-- Name: idx_8e4520848060454e; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_8e4520848060454e ON public.matriz_indicadores_etab_alertas USING btree (matriz_indicador_etab_id);


--
-- Name: idx_8ecb134c10bf7ab4; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_8ecb134c10bf7ab4 ON public.ficha_tecnica_campo USING btree (id_ficha_tecnica);


--
-- Name: idx_8ecb134cc792769a; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_8ecb134cc792769a ON public.ficha_tecnica_campo USING btree (id_campo);


--
-- Name: idx_9087fef9fcf8192d; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_9087fef9fcf8192d ON public.bitacora USING btree (id_usuario);


--
-- Name: idx_9ab65177300a950e; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_9ab65177300a950e ON public.matriz_indicadores_desempeno USING btree (id_matriz);


--
-- Name: idx_a44fc65f18f3a2ea; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_a44fc65f18f3a2ea ON public.matriz_seguimiento USING btree (id_desempeno);


--
-- Name: idx_ae97abd1a6f796be; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_ae97abd1a6f796be ON public.ficha_tecnica USING btree (agencia_id);


--
-- Name: idx_ae97abd1ad8b6d9d; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_ae97abd1ad8b6d9d ON public.ficha_tecnica USING btree (id_periodo);


--
-- Name: idx_ae97abd1ef8f4f8; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_ae97abd1ef8f4f8 ON public.ficha_tecnica USING btree (id_sala_reporte);


--
-- Name: idx_b068bf6e21cdaa3; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_b068bf6e21cdaa3 ON public.indicador_alertas USING btree (id_color_alerta);


--
-- Name: idx_b068bf6e4d2782e4; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_b068bf6e4d2782e4 ON public.indicador_alertas USING btree (id_indicador);


--
-- Name: idx_b29b98ac18f3a2ea; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_b29b98ac18f3a2ea ON public.matriz_indicadores_relacion USING btree (id_desempeno);


--
-- Name: idx_b3c77447a76ed395; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_b3c77447a76ed395 ON public.fos_user_user_group USING btree (user_id);


--
-- Name: idx_b3c77447fe54d947; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_b3c77447fe54d947 ON public.fos_user_user_group USING btree (group_id);


--
-- Name: idx_bb4ee83fdb60d337; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_bb4ee83fdb60d337 ON public.origen_datos USING btree (campolecturaincremental_id);


--
-- Name: idx_c2cd56cb988f9d4d; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_c2cd56cb988f9d4d ON public.fusion_origenes_datos USING btree (id_origen_datos);


--
-- Name: idx_c2cd56cbc197d3c8; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_c2cd56cbc197d3c8 ON public.fusion_origenes_datos USING btree (id_origen_datos_fusionado);


--
-- Name: idx_c560d7616b19771e; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_c560d7616b19771e ON public.fos_user_user USING btree (establecimientoprincipal_id);


--
-- Name: idx_c560d761a6f796be; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_c560d761a6f796be ON public.fos_user_user USING btree (agencia_id);


--
-- Name: idx_c560d761ec88bbac; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_c560d761ec88bbac ON public.fos_user_user USING btree (clasificacionuso_id);


--
-- Name: idx_cc59af901ee9a9a3; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_cc59af901ee9a9a3 ON public.group_fichatecnica USING btree (fichatecnica_id);


--
-- Name: idx_cc59af90fe54d947; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_cc59af90fe54d947 ON public.group_fichatecnica USING btree (group_id);


--
-- Name: idx_cccadbc535f182e9; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_cccadbc535f182e9 ON public.indicador_agencia USING btree (id_agencia);


--
-- Name: idx_cccadbc54d2782e4; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_cccadbc54d2782e4 ON public.indicador_agencia USING btree (id_indicador);


--
-- Name: idx_cfacc4b452d78117; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_cfacc4b452d78117 ON public.origen_datos_fusiones USING btree (id_origen_dato);


--
-- Name: idx_cfacc4b4cbfb5c9a; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_cfacc4b4cbfb5c9a ON public.origen_datos_fusiones USING btree (id_origen_dato_fusionado);


--
-- Name: idx_e82d7cb410bf7ab4; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_e82d7cb410bf7ab4 ON public.ficha_tecnica_variable_dato USING btree (id_ficha_tecnica);


--
-- Name: idx_e82d7cb48fb3036a; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_e82d7cb48fb3036a ON public.ficha_tecnica_variable_dato USING btree (id_variable_dato);


--
-- Name: idx_ebedf33790053a39; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_ebedf33790053a39 ON public.usuario_grupo_indicadores USING btree (grupo_indicadores_id);


--
-- Name: idx_ebedf337d16df9a7; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_ebedf337d16df9a7 ON public.usuario_grupo_indicadores USING btree (usuario_asigno_id);


--
-- Name: idx_ebedf337db38439e; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_ebedf337db38439e ON public.usuario_grupo_indicadores USING btree (usuario_id);


--
-- Name: idx_f146ebd9ce10eeb1; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_f146ebd9ce10eeb1 ON public.matriz_indicadores_relacion_alertas USING btree (matriz_indicador_relacion_id);


--
-- Name: idx_fa12495ab65dc1d3; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_fa12495ab65dc1d3 ON public.matriz_indicadores_desempeno_ficha_tecnica USING btree (matriz_indicadores_desempeno_id);


--
-- Name: idx_fa12495ac992f95c; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_fa12495ac992f95c ON public.matriz_indicadores_desempeno_ficha_tecnica USING btree (ficha_tecnica_id);


--
-- Name: idx_ffacc63347d487d1; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_ffacc63347d487d1 ON public.grupo_indicadores_indicador USING btree (indicador_id);


--
-- Name: idx_ffacc63390053a39; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX idx_ffacc63390053a39 ON public.grupo_indicadores_indicador USING btree (grupo_indicadores_id);


--
-- Name: uniq_583d1f3e5e237e06; Type: INDEX; Schema: public; Owner: admin
--

CREATE UNIQUE INDEX uniq_583d1f3e5e237e06 ON public.fos_user_group USING btree (name);


--
-- Name: uniq_c560d76192fc23a8; Type: INDEX; Schema: public; Owner: admin
--

CREATE UNIQUE INDEX uniq_c560d76192fc23a8 ON public.fos_user_user USING btree (username_canonical);


--
-- Name: uniq_c560d761a0d96fbf; Type: INDEX; Schema: public; Owner: admin
--

CREATE UNIQUE INDEX uniq_c560d761a0d96fbf ON public.fos_user_user USING btree (email_canonical);


--
-- Name: configuracion_pivot_table configuracion_pivot_table_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.configuracion_pivot_table
    ADD CONSTRAINT configuracion_pivot_table_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.fos_user_user(id);


--
-- Name: fichatecnica_tiposgraficos fichatecnica_tiposgraficos_fichatecnica_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fichatecnica_tiposgraficos
    ADD CONSTRAINT fichatecnica_tiposgraficos_fichatecnica_id_fkey FOREIGN KEY (fichatecnica_id) REFERENCES public.ficha_tecnica(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fichatecnica_tiposgraficos fichatecnica_tiposgraficos_tipografico_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fichatecnica_tiposgraficos
    ADD CONSTRAINT fichatecnica_tiposgraficos_tipografico_id_fkey FOREIGN KEY (tipografico_id) REFERENCES public.tipo_grafico(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: regla_transformacion fk_1c386b09f9b351c8; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.regla_transformacion
    ADD CONSTRAINT fk_1c386b09f9b351c8 FOREIGN KEY (id_diccionario) REFERENCES public.diccionario(id) ON DELETE CASCADE;


--
-- Name: matriz_indicadores_etab fk_241b7a3810bf7ab4; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_etab
    ADD CONSTRAINT fk_241b7a3810bf7ab4 FOREIGN KEY (id_ficha_tecnica) REFERENCES public.ficha_tecnica(id) ON DELETE CASCADE;


--
-- Name: matriz_indicadores_etab fk_241b7a3818f3a2ea; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_etab
    ADD CONSTRAINT fk_241b7a3818f3a2ea FOREIGN KEY (id_desempeno) REFERENCES public.matriz_indicadores_desempeno(id) ON DELETE CASCADE;


--
-- Name: campo fk_291737aa5a5f6cfb; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.campo
    ADD CONSTRAINT fk_291737aa5a5f6cfb FOREIGN KEY (id_significado_campo) REFERENCES public.significado_campo(id);


--
-- Name: campo fk_291737aa8a212db5; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.campo
    ADD CONSTRAINT fk_291737aa8a212db5 FOREIGN KEY (id_tipo_campo) REFERENCES public.tipo_campo(id);


--
-- Name: campo fk_291737aa988f9d4d; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.campo
    ADD CONSTRAINT fk_291737aa988f9d4d FOREIGN KEY (id_origen_datos) REFERENCES public.origen_datos(id) ON DELETE CASCADE;


--
-- Name: campo fk_291737aaf9b351c8; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.campo
    ADD CONSTRAINT fk_291737aaf9b351c8 FOREIGN KEY (id_diccionario) REFERENCES public.diccionario(id);


--
-- Name: significados_tipos_graficos fk_2dbd6c985e1df3fd; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.significados_tipos_graficos
    ADD CONSTRAINT fk_2dbd6c985e1df3fd FOREIGN KEY (tipografico_id) REFERENCES public.tipo_grafico(id) ON DELETE CASCADE;


--
-- Name: significados_tipos_graficos fk_2dbd6c98ccc1d2cb; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.significados_tipos_graficos
    ADD CONSTRAINT fk_2dbd6c98ccc1d2cb FOREIGN KEY (significadocampo_id) REFERENCES public.significado_campo(id) ON DELETE CASCADE;


--
-- Name: group_grupoindicadores fk_2df8b6e117ba9678; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.group_grupoindicadores
    ADD CONSTRAINT fk_2df8b6e117ba9678 FOREIGN KEY (grupoindicadores_id) REFERENCES public.grupo_indicadores(id) ON DELETE CASCADE;


--
-- Name: group_grupoindicadores fk_2df8b6e1fe54d947; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.group_grupoindicadores
    ADD CONSTRAINT fk_2df8b6e1fe54d947 FOREIGN KEY (group_id) REFERENCES public.fos_user_group(id) ON DELETE CASCADE;


--
-- Name: sala_acciones fk_3e6ab33790053a39; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.sala_acciones
    ADD CONSTRAINT fk_3e6ab33790053a39 FOREIGN KEY (grupo_indicadores_id) REFERENCES public.grupo_indicadores(id);


--
-- Name: sala_acciones fk_3e6ab337db38439e; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.sala_acciones
    ADD CONSTRAINT fk_3e6ab337db38439e FOREIGN KEY (usuario_id) REFERENCES public.fos_user_user(id);


--
-- Name: accesoexterno_grupoindicadores fk_4481014b17ba9678; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.accesoexterno_grupoindicadores
    ADD CONSTRAINT fk_4481014b17ba9678 FOREIGN KEY (grupoindicadores_id) REFERENCES public.grupo_indicadores(id) ON DELETE CASCADE;


--
-- Name: accesoexterno_grupoindicadores fk_4481014b4cc546dc; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.accesoexterno_grupoindicadores
    ADD CONSTRAINT fk_4481014b4cc546dc FOREIGN KEY (accesoexterno_id) REFERENCES public.acceso_externo(id) ON DELETE CASCADE;


--
-- Name: sala_comentarios fk_44be843b90053a39; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.sala_comentarios
    ADD CONSTRAINT fk_44be843b90053a39 FOREIGN KEY (grupo_indicadores_id) REFERENCES public.grupo_indicadores(id);


--
-- Name: sala_comentarios fk_44be843bdb38439e; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.sala_comentarios
    ADD CONSTRAINT fk_44be843bdb38439e FOREIGN KEY (usuario_id) REFERENCES public.fos_user_user(id);


--
-- Name: matriz_indicadores_usuario fk_49c5174d300a950e; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_usuario
    ADD CONSTRAINT fk_49c5174d300a950e FOREIGN KEY (id_matriz) REFERENCES public.matriz_seguimiento_matriz(id) ON DELETE CASCADE;


--
-- Name: matriz_indicadores_usuario fk_49c5174dfcf8192d; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_usuario
    ADD CONSTRAINT fk_49c5174dfcf8192d FOREIGN KEY (id_usuario) REFERENCES public.fos_user_user(id) ON DELETE CASCADE;


--
-- Name: agencia_formulario fk_4ce25f2e35f182e9; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.agencia_formulario
    ADD CONSTRAINT fk_4ce25f2e35f182e9 FOREIGN KEY (id_agencia) REFERENCES public.agencia(id);


--
-- Name: matriz_seguimiento_dato fk_4fa6f4af300a950e; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_seguimiento_dato
    ADD CONSTRAINT fk_4fa6f4af300a950e FOREIGN KEY (id_matriz) REFERENCES public.matriz_seguimiento(id);


--
-- Name: acceso_externo fk_50ee9f00e60c6ca3; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.acceso_externo
    ADD CONSTRAINT fk_50ee9f00e60c6ca3 FOREIGN KEY (usuariocrea_id) REFERENCES public.fos_user_user(id);


--
-- Name: origenes_conexiones fk_53c1aa5b27105691; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.origenes_conexiones
    ADD CONSTRAINT fk_53c1aa5b27105691 FOREIGN KEY (conexion_id) REFERENCES public.conexion(id) ON DELETE CASCADE;


--
-- Name: origenes_conexiones fk_53c1aa5bac1a9efc; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.origenes_conexiones
    ADD CONSTRAINT fk_53c1aa5bac1a9efc FOREIGN KEY (origendatos_id) REFERENCES public.origen_datos(id) ON DELETE CASCADE;


--
-- Name: usuario_indicadores_favoritos fk_54dd303a4d2782e4; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.usuario_indicadores_favoritos
    ADD CONSTRAINT fk_54dd303a4d2782e4 FOREIGN KEY (id_indicador) REFERENCES public.ficha_tecnica(id) ON DELETE CASCADE;


--
-- Name: usuario_indicadores_favoritos fk_54dd303afcf8192d; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.usuario_indicadores_favoritos
    ADD CONSTRAINT fk_54dd303afcf8192d FOREIGN KEY (id_usuario) REFERENCES public.fos_user_user(id) ON DELETE CASCADE;


--
-- Name: indicador_usuario fk_5c340014d2782e4; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_usuario
    ADD CONSTRAINT fk_5c340014d2782e4 FOREIGN KEY (id_indicador) REFERENCES public.ficha_tecnica(id);


--
-- Name: indicador_usuario fk_5c34001fcf8192d; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_usuario
    ADD CONSTRAINT fk_5c34001fcf8192d FOREIGN KEY (id_usuario) REFERENCES public.fos_user_user(id);


--
-- Name: boletin fk_6510fe7d8c0e9bd3; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.boletin
    ADD CONSTRAINT fk_6510fe7d8c0e9bd3 FOREIGN KEY (grupo) REFERENCES public.fos_user_group(id);


--
-- Name: boletin fk_6510fe7de226041c; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.boletin
    ADD CONSTRAINT fk_6510fe7de226041c FOREIGN KEY (sala) REFERENCES public.grupo_indicadores(id);


--
-- Name: clasificacion_tecnica fk_6f9ff171ec88bbac; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clasificacion_tecnica
    ADD CONSTRAINT fk_6f9ff171ec88bbac FOREIGN KEY (clasificacionuso_id) REFERENCES public.clasificacion_uso(id);


--
-- Name: social fk_7161e187e226041c; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.social
    ADD CONSTRAINT fk_7161e187e226041c FOREIGN KEY (sala) REFERENCES public.grupo_indicadores(id);


--
-- Name: conexion fk_847691c125d64570; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.conexion
    ADD CONSTRAINT fk_847691c125d64570 FOREIGN KEY (id_motor) REFERENCES public.motor_bd(id);


--
-- Name: fichatecnica_clasificaciontecnica fk_892e27eb1ee9a9a3; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fichatecnica_clasificaciontecnica
    ADD CONSTRAINT fk_892e27eb1ee9a9a3 FOREIGN KEY (fichatecnica_id) REFERENCES public.ficha_tecnica(id) ON DELETE CASCADE;


--
-- Name: fichatecnica_clasificaciontecnica fk_892e27eb3b506ca8; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fichatecnica_clasificaciontecnica
    ADD CONSTRAINT fk_892e27eb3b506ca8 FOREIGN KEY (clasificaciontecnica_id) REFERENCES public.clasificacion_tecnica(id) ON DELETE CASCADE;


--
-- Name: matriz_indicadores_etab_alertas fk_8e4520848060454e; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_etab_alertas
    ADD CONSTRAINT fk_8e4520848060454e FOREIGN KEY (matriz_indicador_etab_id) REFERENCES public.matriz_indicadores_etab(id) ON DELETE CASCADE;


--
-- Name: ficha_tecnica_campo fk_8ecb134c10bf7ab4; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ficha_tecnica_campo
    ADD CONSTRAINT fk_8ecb134c10bf7ab4 FOREIGN KEY (id_ficha_tecnica) REFERENCES public.ficha_tecnica(id);


--
-- Name: ficha_tecnica_campo fk_8ecb134cc792769a; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ficha_tecnica_campo
    ADD CONSTRAINT fk_8ecb134cc792769a FOREIGN KEY (id_campo) REFERENCES public.campo(id);


--
-- Name: bitacora fk_9087fef9fcf8192d; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bitacora
    ADD CONSTRAINT fk_9087fef9fcf8192d FOREIGN KEY (id_usuario) REFERENCES public.fos_user_user(id);


--
-- Name: matriz_indicadores_desempeno fk_9ab65177300a950e; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_desempeno
    ADD CONSTRAINT fk_9ab65177300a950e FOREIGN KEY (id_matriz) REFERENCES public.matriz_seguimiento_matriz(id) ON DELETE CASCADE;


--
-- Name: matriz_seguimiento fk_a44fc65f18f3a2ea; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_seguimiento
    ADD CONSTRAINT fk_a44fc65f18f3a2ea FOREIGN KEY (id_desempeno) REFERENCES public.matriz_indicadores_desempeno(id);


--
-- Name: ficha_tecnica fk_ae97abd1a6f796be; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ficha_tecnica
    ADD CONSTRAINT fk_ae97abd1a6f796be FOREIGN KEY (agencia_id) REFERENCES public.agencia(id);


--
-- Name: ficha_tecnica fk_ae97abd1ad8b6d9d; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ficha_tecnica
    ADD CONSTRAINT fk_ae97abd1ad8b6d9d FOREIGN KEY (id_periodo) REFERENCES public.periodos(id);


--
-- Name: ficha_tecnica fk_ae97abd1ef8f4f8; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ficha_tecnica
    ADD CONSTRAINT fk_ae97abd1ef8f4f8 FOREIGN KEY (id_sala_reporte) REFERENCES public.grupo_indicadores(id);


--
-- Name: indicador_alertas fk_b068bf6e21cdaa3; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_alertas
    ADD CONSTRAINT fk_b068bf6e21cdaa3 FOREIGN KEY (id_color_alerta) REFERENCES public.alerta(id);


--
-- Name: indicador_alertas fk_b068bf6e4d2782e4; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_alertas
    ADD CONSTRAINT fk_b068bf6e4d2782e4 FOREIGN KEY (id_indicador) REFERENCES public.ficha_tecnica(id);


--
-- Name: matriz_indicadores_relacion fk_b29b98ac18f3a2ea; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_relacion
    ADD CONSTRAINT fk_b29b98ac18f3a2ea FOREIGN KEY (id_desempeno) REFERENCES public.matriz_indicadores_desempeno(id) ON DELETE CASCADE;


--
-- Name: fos_user_user_group fk_b3c77447a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fos_user_user_group
    ADD CONSTRAINT fk_b3c77447a76ed395 FOREIGN KEY (user_id) REFERENCES public.fos_user_user(id) ON DELETE CASCADE;


--
-- Name: fos_user_user_group fk_b3c77447fe54d947; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fos_user_user_group
    ADD CONSTRAINT fk_b3c77447fe54d947 FOREIGN KEY (group_id) REFERENCES public.fos_user_group(id) ON DELETE CASCADE;


--
-- Name: origen_datos fk_bb4ee83fdb60d337; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.origen_datos
    ADD CONSTRAINT fk_bb4ee83fdb60d337 FOREIGN KEY (campolecturaincremental_id) REFERENCES public.campo(id);


--
-- Name: fusion_origenes_datos fk_c2cd56cb988f9d4d; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fusion_origenes_datos
    ADD CONSTRAINT fk_c2cd56cb988f9d4d FOREIGN KEY (id_origen_datos) REFERENCES public.origen_datos(id) ON DELETE CASCADE;


--
-- Name: fusion_origenes_datos fk_c2cd56cbc197d3c8; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fusion_origenes_datos
    ADD CONSTRAINT fk_c2cd56cbc197d3c8 FOREIGN KEY (id_origen_datos_fusionado) REFERENCES public.origen_datos(id) ON DELETE CASCADE;


--
-- Name: fos_user_user fk_c560d761a6f796be; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fos_user_user
    ADD CONSTRAINT fk_c560d761a6f796be FOREIGN KEY (agencia_id) REFERENCES public.agencia(id);


--
-- Name: fos_user_user fk_c560d761ec88bbac; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.fos_user_user
    ADD CONSTRAINT fk_c560d761ec88bbac FOREIGN KEY (clasificacionuso_id) REFERENCES public.clasificacion_uso(id);


--
-- Name: group_fichatecnica fk_cc59af901ee9a9a3; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.group_fichatecnica
    ADD CONSTRAINT fk_cc59af901ee9a9a3 FOREIGN KEY (fichatecnica_id) REFERENCES public.ficha_tecnica(id) ON DELETE CASCADE;


--
-- Name: group_fichatecnica fk_cc59af90fe54d947; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.group_fichatecnica
    ADD CONSTRAINT fk_cc59af90fe54d947 FOREIGN KEY (group_id) REFERENCES public.fos_user_group(id) ON DELETE CASCADE;


--
-- Name: indicador_agencia fk_cccadbc535f182e9; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_agencia
    ADD CONSTRAINT fk_cccadbc535f182e9 FOREIGN KEY (id_agencia) REFERENCES public.agencia(id);


--
-- Name: indicador_agencia fk_cccadbc54d2782e4; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.indicador_agencia
    ADD CONSTRAINT fk_cccadbc54d2782e4 FOREIGN KEY (id_indicador) REFERENCES public.ficha_tecnica(id);


--
-- Name: origen_datos_fusiones fk_cfacc4b452d78117; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.origen_datos_fusiones
    ADD CONSTRAINT fk_cfacc4b452d78117 FOREIGN KEY (id_origen_dato) REFERENCES public.origen_datos(id);


--
-- Name: origen_datos_fusiones fk_cfacc4b4cbfb5c9a; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.origen_datos_fusiones
    ADD CONSTRAINT fk_cfacc4b4cbfb5c9a FOREIGN KEY (id_origen_dato_fusionado) REFERENCES public.origen_datos(id);


--
-- Name: ficha_tecnica_variable_dato fk_e82d7cb410bf7ab4; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ficha_tecnica_variable_dato
    ADD CONSTRAINT fk_e82d7cb410bf7ab4 FOREIGN KEY (id_ficha_tecnica) REFERENCES public.ficha_tecnica(id);


--
-- Name: usuario_grupo_indicadores fk_ebedf33790053a39; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.usuario_grupo_indicadores
    ADD CONSTRAINT fk_ebedf33790053a39 FOREIGN KEY (grupo_indicadores_id) REFERENCES public.grupo_indicadores(id);


--
-- Name: usuario_grupo_indicadores fk_ebedf337d16df9a7; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.usuario_grupo_indicadores
    ADD CONSTRAINT fk_ebedf337d16df9a7 FOREIGN KEY (usuario_asigno_id) REFERENCES public.fos_user_user(id);


--
-- Name: usuario_grupo_indicadores fk_ebedf337db38439e; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.usuario_grupo_indicadores
    ADD CONSTRAINT fk_ebedf337db38439e FOREIGN KEY (usuario_id) REFERENCES public.fos_user_user(id);


--
-- Name: matriz_indicadores_relacion_alertas fk_f146ebd9ce10eeb1; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_relacion_alertas
    ADD CONSTRAINT fk_f146ebd9ce10eeb1 FOREIGN KEY (matriz_indicador_relacion_id) REFERENCES public.matriz_indicadores_relacion(id) ON DELETE CASCADE;


--
-- Name: matriz_indicadores_desempeno_ficha_tecnica fk_fa12495ab65dc1d3; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_desempeno_ficha_tecnica
    ADD CONSTRAINT fk_fa12495ab65dc1d3 FOREIGN KEY (matriz_indicadores_desempeno_id) REFERENCES public.matriz_indicadores_desempeno(id) ON DELETE CASCADE;


--
-- Name: matriz_indicadores_desempeno_ficha_tecnica fk_fa12495ac992f95c; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.matriz_indicadores_desempeno_ficha_tecnica
    ADD CONSTRAINT fk_fa12495ac992f95c FOREIGN KEY (ficha_tecnica_id) REFERENCES public.ficha_tecnica(id) ON DELETE CASCADE;


--
-- Name: grupo_indicadores_indicador fk_ffacc63347d487d1; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.grupo_indicadores_indicador
    ADD CONSTRAINT fk_ffacc63347d487d1 FOREIGN KEY (indicador_id) REFERENCES public.ficha_tecnica(id);


--
-- Name: grupo_indicadores_indicador fk_ffacc63390053a39; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.grupo_indicadores_indicador
    ADD CONSTRAINT fk_ffacc63390053a39 FOREIGN KEY (grupo_indicadores_id) REFERENCES public.grupo_indicadores(id);




--
-- PostgreSQL database dump complete
--

