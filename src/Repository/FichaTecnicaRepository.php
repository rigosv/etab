<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use App\Entity\FichaTecnica;
use App\Entity\SignificadoCampo;
use App\Entity\ClasificacionUso;
use App\Entity\ClasificacionTecnica;

class FichaTecnicaRepository extends EntityRepository {

    private function existeTablaIndicador( FichaTecnica $fichaTecnica ){
        $em = $this->getEntityManager();

        //Verificar si existe la tabla
        $existe = true;
        $nombre_indicador = $fichaTecnica->getId();
        try {
            $cons = $em->getConnection('etab-datos')->query("select * from temporales.tmp_ind_$nombre_indicador LIMIT 1");
            //Si no tiene datos hacer que se reconstruya
            if ( count($cons->fetchAll()) == 0){
                $existe = false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            $existe = false;
        }
        return $existe;
    }

    public function crearIndicador(FichaTecnica $fichaTecnica, $dimension = null, $filtros = null) {
        $em = $this->getEntityManager();
        $ahora = new \DateTime('NOW');

        $acumulado = $fichaTecnica->getEsAcumulado();
        $nombre_indicador = $fichaTecnica->getId();
        $formula = strtoupper($fichaTecnica->getFormula());

        $existe = $this->existeTablaIndicador($fichaTecnica);

        if ($fichaTecnica->getUpdatedAt() != '' and $fichaTecnica->getUltimaLectura() != '' and $existe == true) {
            if ($fichaTecnica->getUltimaLectura() < $fichaTecnica->getUpdatedAt()){
                return true;
            }
        }

        $campos = str_replace("'", '', $fichaTecnica->getCamposIndicador());

        $tablas_variables = array();
        $sql = 'DROP TABLE IF EXISTS temporales.tmp_ind_' . $nombre_indicador . '; ';
        // Crear las tablas para cada variable
        foreach ($fichaTecnica->getVariables() as $variable) {
            //Recuperar la información de los campos para crear la tabla
            $origen = $variable->getOrigenDatos();
            $diccionarios = array();

            // Si es pivote crear las tablas para los origenes relacionados
            if ($origen->getEsPivote()) {
                $campos_pivote = explode(",", str_replace("'", '', $origen->getCamposFusionados()));
                $pivote = array();
                $campos_regulares = array();
                $tablas_piv = array();
                foreach ($origen->getFusiones() as $or) {
                    $or_id = $or->getId();
                    $sql .= " CREATE TEMP TABLE IF NOT EXISTS od_$or_id ( ";
                    foreach ($or->getCampos() as $campo) {
                        $tipo = $campo->getTipoCampo()->getCodigo();
                        $sig = $campo->getSignificado()->getCodigo();
                        $sql .= $sig . ' ' . $tipo . ', ';
                        if (in_array($sig, $campos_pivote))
                            $pivote[$sig] = $tipo;
                        else
                            $campos_regulares[$sig] = $tipo;
                    }
                    $sql = trim($sql, ', ');
                    $tablas_piv[] = 'od_' . $or_id;
                    $campos_piv = array_merge($pivote, $campos_regulares);
                    $sql .= ' ); ';
                    $sql .= " INSERT INTO od_$or_id
                    SELECT (jsonb_populate_record(null::od_$or_id, datos)).*
                    FROM origenes.fila_origen_dato_$or_id
                    ;";
                }
            }

            //Crear la estructura de la tabla asociada a la variable
            $tabla = strtolower($variable->getIniciales());
            $sql .= ' CREATE TEMP TABLE IF NOT EXISTS ' . $tabla . '(';

            if ($origen->getEsFusionado()) {
                $significados = explode(",", str_replace("'", '', $origen->getCamposFusionados()));
                //Los tipos de campos sacarlos de uno de los orígenes de datos que ha sido fusionado
                $fusionados = $origen->getFusiones();
                $fusionado = $fusionados[0];
                $tipos = array();
                foreach ($fusionado->getCampos() as $campo) {
                    $tipos[$campo->getSignificado()->getCodigo()] = $campo->getTipoCampo()->getCodigo();
                }
                foreach ($significados as $campo) {
                    $sql .= $campo . ' ' . $tipos[$campo] . ', ';
                }
                $sql .= 'calculo numeric, ';
            } elseif ($origen->getEsPivote()) {
                foreach ($campos_piv as $campo => $tipo)
                    $sql .= $campo . ' ' . $tipo . ', ';
            } else {
                foreach ($origen->getCampos() as $campo) {
                    $sql .= $campo->getSignificado()->getCodigo() . ' ' . $campo->getTipoCampo()->getCodigo() . ', ';
                    if ($campo->getDiccionario() != null)
                        $diccionarios[$campo->getSignificado()->getCodigo()] = $campo->getDiccionario()->getId();
                }
            }
            $sql = trim($sql, ', ') . ');';

            // Recuperar los datos desde los orígenes
            if ($origen->getEsPivote()) {
                $tabla1 = array_shift($tablas_piv);
                $sql .= " INSERT INTO $tabla SELECT " . implode(', ', array_keys($campos_piv)) . " FROM $tabla1 ";
                foreach ($tablas_piv as $t) {
                    $sql .= " FULL OUTER JOIN $t USING (" . implode(', ', array_keys($pivote)) . ') ';
                }
                $sql .='; ';
            } else {
                // Si es fusionado recuperar los orígenes que están contenidos
                $origenes = array();
                if ($origen->getEsFusionado())
                    foreach ($origen->getFusiones() as $of)
                        $origenes[] = $of->getId();
                else
                    $origenes[] = $origen->getId();
                
                
                // Leer de todas las posibles tablas que pueda tener el origen
                $j = 0;
                $sql_origenes = '';
                foreach($origenes as $id_origen){
                    if ($j != 0)
                        $sql_origenes =" UNION ";
                    $sql_origenes .= 
                    "
                        SELECT (jsonb_populate_record(null::$tabla, datos)).*
                        FROM origenes.fila_origen_dato_$id_origen
                    ";
                    $j++;
                }
                
                $sql .= "INSERT INTO $tabla ( $sql_origenes );";

            }
            //Obtener los campos que son calculados
            $campos_calculados = array();
            foreach ($origen->getCamposCalculados() as $campo) {
                $campos_calculados[$campo->getSignificado()->getCodigo()] = str_replace(array('{', '}'), '', $campo->getFormula()) . ' AS ' . $campo->getSignificado()->getCodigo();
            }
            $campos_calculados_nombre = '';

            if (count($campos_calculados) > 0) {
                //Quitar los campos calculados del listado campos del indicador sino da error
                $campos_aux = explode(',', str_replace(' ', '', $campos));
                $campos = implode(',', array_diff($campos_aux, array_keys($campos_calculados)));

                $campos_calculados_nombre = ', ' . implode(', ', array_keys($campos_calculados));
                $campos_calculados = ', ' . implode(', ', $campos_calculados);
            } else
                $campos_calculados = '';
            //Obtener solo los datos que se pueden procesar en el indicador
            $sql .= " DROP TABLE IF EXISTS $tabla" . "_var; ";
            //Obtener el operador de la variable
            $oper_ = explode('{'.$variable->getIniciales().'}', str_replace(' ', '', $formula));
            $tieneOperadores = preg_match('/([A-Z]+)\($/', $oper_[0], $coincidencias, PREG_OFFSET_CAPTURE);
            
            $oper = ($tieneOperadores) ? $coincidencias[1][0] : 'SUM';
            
            if ($oper == 'SUM'){
                $sql .= "SELECT  $campos, $oper(calculo::numeric) AS  $tabla $campos_calculados
                            INTO  TEMP $tabla" . "_var
                            FROM $tabla
                            WHERE  (calculo::numeric) > 0
                            GROUP BY $campos $campos_calculados_nombre                
                                ;";
            } else {
                $sql .= "SELECT  $campos, $oper(calculo::numeric) AS  $tabla $campos_calculados
                INTO  TEMP $tabla" . "_var
                FROM $tabla
                GROUP BY $campos $campos_calculados_nombre
                    HAVING  $oper(calculo::numeric) > 0
                    ;";
            }
            

            //aplicar transformaciones si las hubieran
            foreach ($diccionarios as $campo => $diccionario) {
                $sql .= "
                        UPDATE $tabla" . "_var SET $campo = regla.transformacion
                            FROM regla_transformacion AS regla
                            WHERE $tabla" . "_var.$campo = regla.limite_inferior
                                AND id_diccionario = $diccionario
                    ;";
            }
            $tablas_variables[] = $tabla;
        }

        try {
            $sql .= $this->crearTablaIndicador($fichaTecnica, $tablas_variables);
            $em->getConnection('etab-datos')->exec($sql);
            $fichaTecnica->setUpdatedAt($ahora);
            $em->persist($fichaTecnica);
            $em->flush();
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    public function crearTablaIndicador(FichaTecnica $fichaTecnica, $tablas_variables) {
        $sql = '';
        
        $nombre_indicador = $fichaTecnica->getId();
        $campos = str_replace("'", '', $fichaTecnica->getCamposIndicador());
        $formula = $fichaTecnica->getFormula();

        $denominador = explode('/', $formula);
        $evitar_div_0 = '';
        if (count($denominador) > 1) {
            preg_match('/\{.{1,}\}/', $denominador[1], $variables_d);
            if (count($variables_d) > 0)
                $var_d = strtolower(str_replace(array('{', '}'), array('', ''), array_shift($variables_d)));
            $evitar_div_0 = ' WHERE ' . $var_d . ' is not null';
        }

        $sufijoTablas = 'var';
        $sql .= 'SELECT  ' . $campos . ',' . implode(',', $tablas_variables) .
                " INTO temporales.tmp_ind_" . $nombre_indicador . " FROM  " . array_shift($tablas_variables) . '_' . $sufijoTablas . ' ';
        foreach ($tablas_variables as $tabla) {
            $sql .= " FULL OUTER JOIN " . $tabla . "_" . $sufijoTablas . " USING ($campos) " . $evitar_div_0;
        }

        return $sql;
    }       

    public function totalRegistrosIndicador(FichaTecnica $fichaTecnica){
        $nombre_indicador = $fichaTecnica->getId();
        $tabla_indicador = 'temporales.tmp_ind_' . $nombre_indicador;
        
        //Verifica si la tabla debe ser creada o actualizada
        $this->crearIndicador($fichaTecnica);

        $sql = "SELECT count(*) as total
            FROM $tabla_indicador
                ";

        try {
            $fila = $this->getEntityManager()->getConnection('etab-datos')->executeQuery($sql)->fetch();
            return $fila['total'];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * Devuelve los datos del indicador sin procesar la fórmula
     * esto será utilizado en la tabla dinámica
     */
    public function getDatosIndicador(FichaTecnica $fichaTecnica, $offset = 0 , $limit = 100000000) {
        

        $nombre_indicador = $fichaTecnica->getId();
        $tabla_indicador = 'temporales.tmp_ind_' . $nombre_indicador;

        $campos = array();
        $campos_grp = array();
        $campos_indicador = explode(',', str_replace(' ', '', $fichaTecnica->getCamposIndicador()));
        $rel_catalogos = '';
        $catalogo_x = 66; //código ascci de B
        foreach ($campos_indicador as $c) {
            $significado = $this->getEntityManager()->getRepository(SignificadoCampo::class)
                    ->findOneBy(array('codigo' => $c));
            $catalogo = $significado->getCatalogo();
            if ($catalogo != '') {
                $letra_catalogo = chr($catalogo_x++);
                $rel_catalogos .= " INNER JOIN  $catalogo $letra_catalogo  ON (A.$c::text = $letra_catalogo.id::text) ";
                $campos[] = $letra_catalogo . '.descripcion AS ' . str_replace('id_', '', $c);
                $campos_grp[] = $letra_catalogo . '.descripcion';
            } else {
                $campos[] = 'A.'.$c;
                $campos_grp[] = 'A.'.$c;
            }
        }

        //Recuperar las variables
        $vars = array();
        $variables = array();
        $formula = strtolower($fichaTecnica->getFormula());
        preg_match_all('/\{[a-z0-9\_]{1,}\}/', strtolower($formula), $vars, PREG_SET_ORDER);
        
        foreach ($vars as $var) {
            $oper_ = explode($var[0], $formula);
            $tieneOperadores = preg_match('/([A-Z]+)\($/', $oper_[0], $coincidencias, PREG_OFFSET_CAPTURE);
            
            $oper = ($tieneOperadores) ? $coincidencias[1][0] : 'SUM';
            
            $v = str_replace(array('{', '}'), array('', ''), $var[0]);
            $variables[] = $oper.'('.$v . ') AS __' . $v . '__';
        }

        $campos = implode(', ', $campos);
        $variables = implode(', ', $variables);
        $campos_grp = implode(', ', $campos_grp);
        $sql = "SELECT $campos, $variables
            FROM $tabla_indicador A 
                $rel_catalogos
            GROUP BY $campos_grp
            OFFSET $offset LIMIT $limit
                ";
        
        try {
            return $this->getEntityManager()->getConnection('etab-datos')->executeQuery($sql)->fetchAll();
        } catch (\PDOException $e) {
            return $e->getMessage();
        } catch (\Doctrine\DBAL\DBALException $e) {
            return $e->getMessage();
        }
    }

    public function calcularIndicador(FichaTecnica $fichaTecnica, $dimension, $filtro_registros = null, $ver_sql = false) {
        
        $acumulado = $fichaTecnica->getEsAcumulado();
        $formula = str_replace(' ', '',strtolower($fichaTecnica->getFormula()));

        //Recuperar las variables
        $variables = array();
        preg_match_all('/\{[a-z0-9\_]{1,}\}/', strtolower($formula), $variables, PREG_SET_ORDER);

        $denominador = explode('/', $fichaTecnica->getFormula());
        $evitar_div_0 = '';
        $variables_d = array();
        if (count($denominador) > 1) {
            preg_match('/\{.{1,}\}/', $denominador[1], $variables_d);
            if (count($variables_d) > 0)
                $var_d = strtolower(str_replace(array('{', '}'), array('(', ')'), array_shift($variables_d)));
            $evitar_div_0 = 'AND ' . $var_d . ' > 0';
        }

        // Formar la cadena con las variables para ponerlas en la consulta
        $variables_query = '';
        foreach ($variables as $var) {
            $oper_ = explode($var[0], $formula);
            $tieneOperadores = preg_match('/([A-Za-z]+)\($/', $oper_[0], $coincidencias, PREG_OFFSET_CAPTURE);
            
            $oper = ($tieneOperadores) ? $coincidencias[1][0] : 'SUM';
            
            $v = str_replace(array('{', '}'), array('', ''), $var[0]);
            
            $formula = str_replace($var[0], (($oper=='SUM')?$oper:'').str_replace(array('{','}'), array('(', ')'),$var[0]), $formula);
            
            $variables_query .= " $oper($v) as $v, ";
        }
        $variables_query = trim($variables_query, ', ');

        $nombre_indicador = $fichaTecnica->getId();
        $tabla_indicador = 'temporales.tmp_ind_' . $nombre_indicador;

        //Verificar si es un catálogo
        $rel_catalogo = '';
        $otros_campos = '';
        $grupo_extra = '';
        $dimension_ = $dimension;
        $significado = $this->getEntityManager()->getRepository(SignificadoCampo::class)
                ->findOneBy(array('codigo' => $dimension));
        $catalogo = $significado->getCatalogo();
        if ($catalogo != '') {
            $rel_catalogo = " INNER JOIN  $catalogo  B ON (A.$dimension::text = B.id::text) ";
            $dimension_ = 'A.'.$dimension.', B.descripcion';
            $otros_campos = ' B.id AS id_category, ';
            $grupo_extra = ', B.id ';
        }
        
        $filtros = '';
        if ($filtro_registros != null) {
            foreach ($filtro_registros as $campo => $valor) {
                //Si el filtro es un catálogo, buscar su id correspondiente
                $significado = $this->getEntityManager()->getRepository(SignificadoCampo::class)
                        ->findOneBy(array('codigo' => $campo));
                $catalogo = $significado->getCatalogo();
                $sql_ctl = '';
                if ($catalogo != '') {
                    $sql_ctl = "SELECT id FROM $catalogo WHERE descripcion ='$valor'";
                    $reg = $this->getEntityManager()->getConnection('etab-datos')->executeQuery($sql_ctl)->fetch();
                    $valor = $reg['id'];
                }
                $filtros .= " AND A." . $campo . " = '$valor' ";
            }
        }

        if ($acumulado and $significado->getAcumulable() === true){
            $var_n = array_pop(str_replace(array('{','}'), array('',''), $variables[0]));
            $var_d = array_pop(str_replace(array('{','}'), array('',''), $variables[1]));
            $filtros_ = str_replace('A.', 'AA.', $filtros);
                        
            $formula = str_replace(
                    array('SUM('.$var_n.')', 'SUM('.$var_d.')'), 
                    array("(SELECT SUM(AA.$var_n) FROM $tabla_indicador AA WHERE AA.$dimension::numeric <= A.$dimension::numeric $filtros_)",
                            "(SELECT SUM(AA.$var_d) FROM $tabla_indicador AA WHERE AA.$dimension::numeric <= A.$dimension::numeric $filtros_)"), 
                    $formula
                    );
            $variables_query = str_replace(
                    array('SUM('.$var_n.')', 'SUM('.$var_d.')'), 
                    array("(SELECT SUM(AA.$var_n) FROM $tabla_indicador AA WHERE AA.$dimension::numeric <= A.$dimension::numeric $filtros_)",
                            "(SELECT SUM(AA.$var_d) FROM $tabla_indicador AA WHERE AA.$dimension::numeric <= A.$dimension::numeric $filtros_)"), 
                    $variables_query
                    );
            
            $dimension_ = ($catalogo != '') ? $dimension_ = 'A.'.$dimension.'::numeric, B.descripcion' : $dimension.'::numeric';
        }
        
        $sql = "SELECT $dimension_ AS category, $otros_campos $variables_query, round(($formula)::numeric,2) AS measure
            FROM $tabla_indicador A" . $rel_catalogo;
        $sql .= ' WHERE 1=1 ' . $evitar_div_0 . ' ' . $filtros;
        
        $sql .= "
            GROUP BY ".str_replace('::numeric', '', $dimension_)." $grupo_extra";
        $sql .=  "HAVING (($formula)::numeric) > 0 ";
        $sql .= "ORDER BY $dimension_";

        try {
            if ($ver_sql == true)
                return $sql;
            else {
                return $this->getEntityManager()->getConnection('etab-datos')->executeQuery($sql)->fetchAll();
            }
        } catch (\PDOException $e) {
            return $e->getMessage();
        } catch (\Doctrine\DBAL\DBALException $e) {
            return $e->getMessage();
        }
    }

    public function crearCamposIndicador(FichaTecnica $fichaTecnica) {
        $em = $this->_em;
        //Recuperar las variables
        $variables = $fichaTecnica->getVariables();
        $origen_campos = array();
        $origenDato = array();
        foreach ($variables as $k => $variable) {
            //Obtener la información de los campos de cada origen
            $origenDato[$k] = $variable->getOrigenDatos();
            if ($origenDato[$k]->getEsFusionado()) {
                $significados = explode(',', $origenDato[$k]->getCamposFusionados());
                //Los tipos de campos sacarlos de uno de los orígenes de datos que ha sido fusionado
                $fusionados = $origenDato[$k]->getFusiones();
                $fusionado = $fusionados[0];
                $tipos = array();
                foreach ($fusionado->getAllFields() as $campo) {
                    $tipos[$campo->getSignificado()->getCodigo()] = $campo->getTipoCampo()->getCodigo();
                }
                foreach ($significados as $sig) {
                    $sig_ = trim(str_replace("'", '', $sig));
                    $significado = $em->getRepository(SignificadoCampo::class)->findOneBy(array('codigo' => $sig_));
                    $llave = $significado->getCodigo() . '-' . $tipos[$sig_];
                    $origen_campos[$origenDato[$k]->getId()][$llave]['significado'] = $sig_;
                }
            } elseif ($origenDato[$k]->getEsPivote()) {
                foreach ($origenDato[$k]->getFusiones() as $or) {
                    foreach ($or->getAllFields() as $campo) {
                        //La llave para considerar campo comun será el mismo significado
                        $llave = $campo->getSignificado()->getCodigo();
                        //$llave = $campo->getSignificado()->getId();
                        $origen_campos[$origenDato[$k]->getId()][$llave]['significado'] = $campo->getSignificado()->getCodigo();
                    }
                }
            } else {
                foreach ($origenDato[$k]->getAllFields() as $campo) {
                    //La llave para considerar campo comun será el mismo tipo y significado
                    $llave = $campo->getSignificado()->getCodigo() . '-' . $campo->getTipoCampo()->getCodigo();
                    //$llave = $campo->getSignificado()->getId();
                    $origen_campos[$origenDato[$k]->getId()][$llave]['significado'] = $campo->getSignificado()->getCodigo();
                }
            }

            //Determinar los campos comunes (con igual significado e igual tipo)
            $aux = $origen_campos;
            $campos_comunes = array_shift($aux);
            foreach ($aux as $a) {
                $campos_comunes = array_intersect_key($campos_comunes, $a);
            }
        }
        $aux = array();
        foreach ($campos_comunes as $campo) {
            $aux[$campo['significado']] = $campo['significado'];
        }

        if (isset($aux['calculo'])) {
            unset($aux['calculo']);
        }

        $campos_comunes = implode(", ", $aux);
        if ($fichaTecnica->getCamposIndicador() != '') {
            //Si ya existen los campos sacar el orden que ya ha especificado el usuario
            $act = explode(',', str_replace(' ', '', $fichaTecnica->getCamposIndicador()));
            $campos_comunes = array_intersect($act, $aux);
            //agregar los posibles campos nuevos
            $campos_comunes = array_merge($campos_comunes, array_diff($aux, $act));
            $campos_comunes = implode(", ", $campos_comunes);
        }

        $fichaTecnica->setCamposIndicador($campos_comunes);
        $em->flush();
    }
    
    public function setConfPivoTable($tipoElemento, $idElemento, $idUsuario, $configuracion) {
        $em = $this->getEntityManager();
        
        //Verificar si existe la tabla, sino crearla
        $sql = "CREATE TABLE IF NOT EXISTS configuracion_pivot_table(
                    tipo_elemento     varchar(40),
                    identificador_elemento    varchar(30),
                    configuracion                text,
                    id_usuario               integer
                 )";
        
        $em->getConnection()->executeQuery($sql);
        
        //verificar si existe la configuración
        $sql = "SELECT configuracion 
                    FROM configuracion_pivot_table 
                    WHERE tipo_elemento = '$tipoElemento' 
                        AND identificador_elemento = '$idElemento'
                        AND id_usuario = $idUsuario ";
        $cons = $em->getConnection()->executeQuery($sql);
        
        if ($cons->rowCount() > 0){
            //Actualizar la configuración
            $sql = "UPDATE configuracion_pivot_table SET configuracion = '$configuracion'
                        WHERE tipo_elemento = '$tipoElemento' 
                        AND identificador_elemento = '$idElemento'
                        AND id_usuario = $idUsuario ";
            $em->getConnection()->executeQuery($sql);
        } else {
            //Guardar la configuración
            $sql = "INSERT INTO configuracion_pivot_table(tipo_elemento, identificador_elemento, configuracion, id_usuario)
                        VALUES ('$tipoElemento', '$idElemento', '$configuracion', $idUsuario) ";
            $em->getConnection()->executeQuery($sql);
        }
        //echo $sql;
        
    }
    
    public function getConfPivoTable($tipoElemento, $idElemento, $idUsuario) {
        $em = $this->getEntityManager();
        
        //Recuperar la configuración
        $sql = "SELECT configuracion 
                    FROM Configuracion_pivot_table 
                    WHERE tipo_elemento = '$tipoElemento' 
                        AND identificador_elemento = '$idElemento'
                        AND id_usuario = $idUsuario ";

        return $em->getConnection()->executeQuery($sql)->fetch();
    }

    public function getAnalisisDescriptivo($sqlInicial) {
        $em = $this->getEntityManager();
        //Quitar operadores que no se deben utilizar
        //$partes = split('GROUP BY', $sqlInicial);
        //$sql_inicial = str_replace(array('SUM', 'AVG', 'MAX', 'MIN'), array(''), $partes[0]);
        
        //Recuperar la configuración
        $sql = "SELECT   promedio, desviacion_estandar, quantile[1] AS min, quantile[2] AS cuartil_1,  
                    quantile[3] AS cuartil_2, quantile[4] AS cuartil_3, quantile[5] AS max, quantile[3] AS mediana
                FROM (
                        SELECT avg(measure) AS promedio, stddev_samp(measure) AS desviacion_estandar,
                            quantile(measure) AS quantile 
                            FROM ($sqlInicial) A 
                            WHERE measure is not null
                    ) AA ";
        
        $resp = $em->getConnection('etab-datos')->executeQuery($sql)->fetchAll();
        
        return $resp;
    }

    public function getListadoIndicadores(User $usuario) {
        $em = $this->getEntityManager();
        $clasificacionUso = $em->getRepository(ClasificacionUso::class)->findBy(array(), array('descripcion' => 'ASC'));

        //Luego agregar un método para obtener la clasificacion de uso por defecto del usuario
        if ($usuario->getClasificacionUso()) {
            $clasificacionUsoPorDefecto = $usuario->getClasificacionUso();
        } else {
            $clasificacionUsoPorDefecto = $clasificacionUso[0];
        }
        $categorias = $em->getRepository(ClasificacionTecnica::class)->findBy(array('clasificacionUso' => $clasificacionUsoPorDefecto));

        //Indicadores asignados por usuario
        $usuarioIndicadores = ($usuario->hasRole('ROLE_SUPER_ADMIN')) ?
            $this->findBy(array(), array('nombre' => 'ASC')) :
            $usuario->getIndicadores();
        //Indicadores asignadas al grupo al que pertenece el usuario
        $indicadoresPorGrupo = array();
        foreach ($usuario->getGroups() as $grp) {
            foreach ($grp->getIndicadores() as $indicadores_grupo) {
                $indicadoresPorGrupo[] = $indicadores_grupo;
            }
        }

        $indicadores_por_usuario = array();
        $indicadores_clasificados = array();
        foreach ($usuarioIndicadores as $ind) {
            $indicadores_por_usuario[] = $ind->getId();
        }

        foreach ($indicadoresPorGrupo as $ind) {
            $indicadores_por_usuario[] = $ind->getId();
        }

        $categorias_indicador = array();
        foreach ($categorias as $cat) {
            $categorias_indicador[$cat->getId()]['cat'] = $cat;
            $categorias_indicador[$cat->getId()]['indicadores'] = array();
            $indicadores_por_categoria = $cat->getIndicadores();
            foreach ($indicadores_por_categoria as $ind) {
                if (in_array($ind->getId(), $indicadores_por_usuario)) {
                    $categorias_indicador[$cat->getId()]['indicadores'][] = $ind;
                    $indicadores_clasificados[] = $ind->getId();
                }
            }
        }

        $indicadores_no_clasificados = array();
        foreach ($usuarioIndicadores as $ind) {
            if (!in_array($ind->getId(), $indicadores_clasificados)) {
                $indicadores_no_clasificados[] = $ind;
            }
        }
        foreach ($indicadoresPorGrupo as $ind) {
            if (!in_array($ind->getId(), $indicadores_clasificados)) {
                $indicadores_no_clasificados[] = $ind;
            }
        }
        $resp = array('categorias' => $categorias_indicador,
            'clasficacion_uso' => $clasificacionUso,
            'indicadores_no_clasificados' => $indicadores_no_clasificados);

        return $resp;
    }
}