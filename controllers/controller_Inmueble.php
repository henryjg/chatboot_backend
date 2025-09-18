   
<?php
include_once './utils/response.php';
include_once './config/database.php';

class ControladorInmueble {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }
    // Devuelve el número total de inmuebles para panel admin
    public function getTotalInmueblesPanelAdmin() {
        $conexion = new Conexion();
        $query = "SELECT COUNT(*) as total FROM inmueble WHERE 
        inmu_condicioncontrato = 'Proyecto Constructivo' and
        inmu_estado = 'Activo'";
        $result = $conexion->ejecutarConsulta($query);
        if ($result && $row = $result->fetch_assoc()) {
            response::success($row['total'], 'Total de proyectos obtenido correctamente');
        } else {
            response::error('No se pudo obtener el total de proyectos');
        }
    }

    // Devuelve el número total de inmuebles para web
    public function getTotalInmueblesWeb() {
        $conexion = new Conexion();
        $query = "SELECT COUNT(*) as total FROM inmueble WHERE inmu_estado = 'Activo'";
        $result = $conexion->ejecutarConsulta($query);
        if ($result && $row = $result->fetch_assoc()) {
            response::success($row['total'], 'Total de inmuebles (web) obtenido correctamente');
        } else {
            response::error('No se pudo obtener el total de inmuebles');
        }
    }
    // *************************************************************
    // ************ METODOS PARA CARACTERISTICAS INMUELE  ******************
    // *************************************************************
    
    public function insertCaracteristica_Inmueble($idcarac, $idinmueble,$descripcion) {
        $conexion = new Conexion();
        $query = "INSERT INTO inmueble_caracteristicas (ic_descripcion, ic_idinmueble, ic_idcarac)
                   VALUES ('$descripcion','$idinmueble','$idcarac')";
        $result = $conexion->insertar($query);
        if ($result > 0) {
            response::success($result, 'Característica asignada correctamente');
        } else {
            response::error('Error al insertar la característica');
        }
    }

    // ------------------------------------------------------
    public function deleteCaracteristica_Inmueble($idcarac) {
        $conexion = new Conexion();
        $query = "DELETE FROM inmueble_caracteristicas WHERE ic_id = $idcarac";
        $result = $conexion->save($query);
        if ($result > 0) {
            response::success($result, 'Característica eliminada correctamente');
        } else {
            response::error('Error al eliminar la característica');
        }
    }

    // ------------------------------------------------------
    public function getCaracteristicas_Inmueble($idinmueble) {
        $conexion = new Conexion();
        $query = "SELECT ic_id, 
                            ic_descripcion as descripcion,
                            ic_idinmueble as idinmueble,
                            ic_idcarac as idcarac,
                            carac_nombre as caracteristica
                    FROM inmueble_caracteristicas,  caracteristicas
                    WHERE caracteristicas.carac_id = ic_idcarac 
                          and ic_idcarac!=1 
                          and ic_idinmueble='$idinmueble'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $caracteristicas = array();
            while ($caracteristica = $result->fetch_assoc()) {
                $caracteristicas[] = $caracteristica;
            }
            response::success($caracteristicas, 'Lista de características obtenida correctamente');
        } else {
            response::error('No se encontraron características registradas');
        }
    }
    // ------------------------------------------------------
    public function getCaracteristicas_Inmueble_Detalles($idinmueble) {
        $conexion = new Conexion();
        $query = "SELECT ic_id, 
                            ic_descripcion as descripcion,
                            ic_idinmueble as idinmueble,
                            ic_idcarac as idcarac,
                            carac_nombre as caracteristica
                    FROM inmueble_caracteristicas,  caracteristicas
                    WHERE caracteristicas.carac_id = ic_idcarac 
                          and ic_idcarac=1 
                          and ic_idinmueble='$idinmueble'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $caracteristicas = array();
            while ($caracteristica = $result->fetch_assoc()) {
                $caracteristicas[] = $caracteristica;
            }
            response::success($caracteristicas, 'Lista de características obtenida correctamente');
        } else {
            response::error('No se encontraron características registradas');
        }
    }

    // *************************************************************
    // ************ METODOS PARA CARACTERISTICAS  ******************
    // *************************************************************

    public function insertCaracteristica($carac_nombre) {
        $conexion = new Conexion();
        $query = "INSERT INTO caracteristicas (carac_nombre) VALUES ('$carac_nombre')";
        $result = $conexion->insertar($query);
        if ($result > 0) {
            response::success($result, 'Característica insertada correctamente');
        } else {
            response::error('Error al insertar la característica');
        }
    }

    // ------------------------------------------------------
    // public function deleteCaracteristica($carac_id) {
    //     $conexion = new Conexion();
    //     $query = "DELETE FROM caracteristicas WHERE carac_id = $carac_id";
    //     $result = $conexion->save($query);
    //     if ($result > 0) {
    //         response::success($result, 'Característica eliminada correctamente');
    //     } else {
    //         response::error('Error al eliminar la característica');
    //     }
    // }
    
  public function deleteCaracteristica($idcarac, $idinmueble) {
    $conexion = new Conexion();
    $query = "DELETE FROM inmueble_caracteristicas WHERE ic_id = $idcarac AND ic_idinmueble = $idinmueble";
    $result = $conexion->save($query);
    if ($result > 0) {
        response::success($result, 'Característica eliminada correctamente');
    } else {
        response::error('Error al eliminar la característica');
    }
}

    // ------------------------------------------------------
    
    public function getCaracteristicas() {
        $conexion = new Conexion();
        $query = "SELECT carac_id as idcarac, carac_nombre as caracteristica 
                  FROM caracteristicas
                  WHERE carac_id!=1";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $caracteristicas = array();
            while ($caracteristica = $result->fetch_assoc()) {
                $caracteristicas[] = $caracteristica;
            }
            response::success($caracteristicas, 'Lista de características obtenida correctamente');
        } else {
            response::error('No se encontraron características registradas');
        }
    }

    
    // ******************************************************
    // ************ METODOS PARA INMUEBLES ******************
    // ******************************************************

    // ------------------------------------------------------
    public function insertInmueble(
        $inmu_tipobien,
        $inmu_titulo,
        $inmu_descripcion,
        $inmu_areaterreno,
        $inmu_areaconstruida,
        $inmu_condicioncontrato,
        $inmu_partidaregistral,
        $inmu_tipooperacion,
        $inmu_precio,
        $inmu_moneda,
        $inmu_direccion,
        $inmu_ubigeo,
        $inmu_latitud,
        $inmu_long,
        $inmu_video,
        $inmu_categoria,
        $inmu_largo,
        $inmu_ancho,
        $asesor_id,
        $propietario_id
    ) {
        $conexion = new Conexion();
        $inmu_fecharegistro = date("Y-m-d");

        $query = "INSERT INTO inmueble (
            inmu_tipobien,
            inmu_titulo,
            inmu_descripcion,
            inmu_areaterreno,
            inmu_areaconstruida,
            inmu_condicioncontrato,
            inmu_partidaregistral,
            inmu_tipooperacion,
            inmu_precio,
            inmu_moneda,
            inmu_fecharegistro,
            inmu_estado,
            inmu_direccion,
            inmu_ubigeo,
            inmu_latitud,
            inmu_long,
            inmu_video,
            inmu_categoria,
            inmu_largo,
            inmu_ancho
        ) VALUES (
            '$inmu_tipobien',
            '$inmu_titulo',
            '$inmu_descripcion',
            '$inmu_areaterreno',
            '$inmu_areaconstruida',
            '$inmu_condicioncontrato',
            '$inmu_partidaregistral',
            '$inmu_tipooperacion',
            '$inmu_precio',
            '$inmu_moneda',
            '$inmu_fecharegistro',
            'Activo',
            '$inmu_direccion',
            '$inmu_ubigeo',
            '$inmu_latitud',
            '$inmu_long',
            '$inmu_video',
            '$inmu_categoria',
            '$inmu_largo',
            '$inmu_ancho'
        )";

        $inmuebleId = $conexion->insertar($query);

        if ($inmuebleId <= 0) {
            response::error('Error al insertar el inmueble');
            return;
        }

        // Verificar si el asesor existe
        if ($asesor_id) {
            $queryVerificarAsesor = "SELECT COUNT(*) as count FROM trabajador WHERE tra_id = '$asesor_id'";
            $resultVerificarAsesor = $conexion->ejecutarConsulta($queryVerificarAsesor);
            $rowAsesor = $resultVerificarAsesor->fetch_assoc();

            if ($rowAsesor['count'] <= 0) {
                response::error('El asesor no existe');
                return;
            }

            // Asignar el asesor como principal al inmueble
            $queryAsignarAsesor = "INSERT INTO asignacion_trabajador_inmueble (
                asig_fechaInicial,
                asig_trabajadorId,
                asig_inmuebleId,
                asig_estado,
                asig_tipoAsignacion
            ) VALUES (
                CURDATE(),
                '$asesor_id',
                '$inmuebleId',
                'Activo',
                'Principal'
            )";

            $resultAsignarAsesor = $conexion->insertar($queryAsignarAsesor);

            if ($resultAsignarAsesor <= 0) {
                response::error('Error al asignar el asesor como principal al inmueble');
                return;
            }
        }

        // Verificar si el propietario existe y asignarlo
        if ($propietario_id) {
            $queryVerificarPropietario = "SELECT COUNT(*) as count FROM propietario WHERE propi_id = '$propietario_id'";
            $resultVerificarPropietario = $conexion->ejecutarConsulta($queryVerificarPropietario);
            $rowPropietario = $resultVerificarPropietario->fetch_assoc();

            if ($rowPropietario['count'] <= 0) {
                response::error('El propietario no existe');
                return;
            }

            // Asignar el propietario al inmueble
            $queryAsignarPropietario = "INSERT INTO inmueble_propietario (
                inmupro_propietarioid,
                inmupro_inmuebleid
            ) VALUES (
                '$propietario_id',
                '$inmuebleId'
            )";

            $resultAsignarPropietario = $conexion->insertar($queryAsignarPropietario);

            if ($resultAsignarPropietario <= 0) {
                response::error('Error al asignar el propietario al inmueble');
                return;
            }
        }

        $mensaje = 'Inmueble insertado correctamente';
        if ($asesor_id && $propietario_id) {
            $mensaje = 'Inmueble registrado correctamente';
        } elseif ($asesor_id) {
            $mensaje = 'Inmueble registrado correctamente';
        } elseif ($propietario_id) {
            $mensaje = 'Inmueble registrado correctamente';
        }

        response::success($inmuebleId, $mensaje);
    }

    
    public function insertProyecto(
        $inmu_tipobien,
        $inmu_titulo,
        $inmu_descripcion,
        $inmu_areaterreno,
        $inmu_areaconstruida,
        $inmu_tipooperacion,
        $inmu_precio,
        $inmu_moneda,
        $inmu_direccion,
        $inmu_ubigeo,
        $inmu_latitud,
        $inmu_long,
        $inmu_video,
        $inmu_categoria,
        $inmu_largo,
        $inmu_ancho,
        $proyecto_slogan,
        $proyecto_sector1,
        $proyecto_sector2,
        $proyecto_sector3
    ) {
        $conexion = new Conexion();
        $inmu_fecharegistro = date("Y-m-d");

        $query = "INSERT INTO inmueble (
            inmu_tipobien,
            inmu_titulo,
            inmu_descripcion,
            inmu_areaterreno,
            inmu_areaconstruida,
            inmu_condicioncontrato,
            inmu_tipooperacion,
            inmu_precio,
            inmu_moneda,
            inmu_fecharegistro,
            inmu_estado,
            inmu_direccion,
            inmu_ubigeo,
            inmu_latitud,
            inmu_long,
            inmu_video,
            inmu_categoria,
            inmu_largo,
            inmu_ancho,
            proyecto_slogan,
            sector1,
            sector2,
            sector3
        ) VALUES (
            '$inmu_tipobien',
            '$inmu_titulo',
            '$inmu_descripcion',
            '$inmu_areaterreno',
            '$inmu_areaconstruida',
            'Proyecto Constructivo',
            '$inmu_tipooperacion',
            '$inmu_precio',
            '$inmu_moneda',
            '$inmu_fecharegistro',
            'Activo',
            '$inmu_direccion',
            '$inmu_ubigeo',
            '$inmu_latitud',
            '$inmu_long',
            '$inmu_video',
            '$inmu_categoria',
            '$inmu_largo',
            '$inmu_ancho',
            '$proyecto_slogan',
            '$proyecto_sector1',
            '$proyecto_sector2',
            '$proyecto_sector3'
        )";

        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Proyecto insertado correctamente');
        } else {
            response::error('Error al insertar el proyecto');
        }
    }

    public function updateInmueble(
        $inmu_id,
        $inmu_tipobien,
        $inmu_titulo,
        $inmu_descripcion,
        $inmu_areaterreno,
        $inmu_areaconstruida,
        $inmu_condicioncontrato,
        $inmu_partidaregistral,
        $inmu_tipooperacion,
        $inmu_precio,
        $inmu_moneda,
        $inmu_direccion,
        $inmu_ubigeo,
        $inmu_latitud,
        $inmu_long,
        $inmu_video,
        $inmu_categoria,
        $inmu_largo,
        $inmu_ancho
    ) {
        $conexion = new Conexion();
    
        $query = "UPDATE inmueble SET
            inmu_tipobien = '$inmu_tipobien',
            inmu_titulo = '$inmu_titulo',
            inmu_descripcion = '$inmu_descripcion',
            inmu_areaterreno = '$inmu_areaterreno',
            inmu_areaconstruida = '$inmu_areaconstruida',
            inmu_condicioncontrato = '$inmu_condicioncontrato',
            inmu_partidaregistral = '$inmu_partidaregistral',
            inmu_tipooperacion = '$inmu_tipooperacion',
            inmu_precio = '$inmu_precio',
            inmu_moneda = '$inmu_moneda',
            inmu_direccion = '$inmu_direccion',
            inmu_ubigeo = '$inmu_ubigeo',
            inmu_latitud = '$inmu_latitud',
            inmu_long = '$inmu_long',
            inmu_video = '$inmu_video',
            inmu_categoria = '$inmu_categoria',
            inmu_largo = '$inmu_largo',
            inmu_ancho = '$inmu_ancho'
        WHERE inmu_id = '$inmu_id'";
    
        $result = $conexion->save($query);
    
        if ($result > 0) {
            response::success($result, 'Inmueble actualizado correctamente');
        } else {
            response::error('Error al actualizar el inmueble');
        }
    }
    

    public function updateProyecto(
        $inmu_id,
        $inmu_tipobien,
        $inmu_titulo,
        $inmu_descripcion,
        $inmu_areaterreno,
        $inmu_areaconstruida,
        $inmu_condicioncontrato,
        $inmu_tipooperacion,
        $inmu_precio,
        $inmu_moneda,
        $inmu_direccion,
        $inmu_ubigeo,
        $inmu_latitud,
        $inmu_long,
        $inmu_video,
        $inmu_categoria,
        $inmu_largo,
        $inmu_ancho,
        $proyecto_slogan,
        $proyecto_sector1,
        $proyecto_sector2,
        $proyecto_sector3
    ) {
        $conexion = new Conexion();
    
        $query = "UPDATE inmueble SET
            inmu_tipobien = '$inmu_tipobien',
            inmu_titulo = '$inmu_titulo',
            inmu_descripcion = '$inmu_descripcion',
            inmu_areaterreno = '$inmu_areaterreno',
            inmu_areaconstruida = '$inmu_areaconstruida',
            inmu_condicioncontrato = '$inmu_condicioncontrato',
            inmu_tipooperacion = '$inmu_tipooperacion',
            inmu_precio = '$inmu_precio',
            inmu_moneda = '$inmu_moneda',
            inmu_direccion = '$inmu_direccion',
            inmu_ubigeo = '$inmu_ubigeo',
            inmu_latitud = '$inmu_latitud',
            inmu_long = '$inmu_long',
            inmu_video = '$inmu_video',
            inmu_categoria = '$inmu_categoria',
            inmu_largo = '$inmu_largo',
            inmu_ancho = '$inmu_ancho',
            proyecto_slogan = '$proyecto_slogan',
            sector1 = '$proyecto_sector1',
            sector2 = '$proyecto_sector2',
            sector3 = '$proyecto_sector3'
        WHERE inmu_id = '$inmu_id'";
    
        $result = $conexion->save($query);
    
        if ($result > 0) {
            response::success($result, 'Inmueble actualizado correctamente');
        } else {
            response::error('Error al actualizar el inmueble');
        }
    }
    


    // ------------------------------------------------------
    public function getInmueble($inmu_id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    inmu_ubigeo as ubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_partidaregistral as partidaregistral,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video,
                    inmu_categoria as categoria,
                    inmu_largo as largo,
                    inmu_ancho as ancho
                FROM inmueble 
                WHERE inmu_id = $inmu_id";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $inmueble = $result->fetch_assoc();
            response::success($inmueble, 'Consulta de inmueble exitosa');
        } else {
            response::error('No se encontró el inmueble');
        }
        }

        // Obtiene detalles clave de un inmueble: id contrato, id propietario, id asesor y fotos
        public function obtenerInmuebleDatos($inmu_id) {
            $conexion = new Conexion();

            // Obtener id de contrato activo/vigente
            $queryContrato = "SELECT contrato_id FROM contrato WHERE contrato_inmuebleid = '$inmu_id' ORDER BY contrato_fechainicio DESC LIMIT 1";
            $resultContrato = $conexion->ejecutarConsulta($queryContrato);
            $idContrato = null;
            if ($resultContrato && $rowContrato = $resultContrato->fetch_assoc()) {
                $idContrato = $rowContrato['contrato_id'];
            }

            // Obtener id de propietario
                $queryPropietario = "SELECT inmupro_propietarioid FROM inmueble_propietario WHERE inmupro_inmuebleid = '$inmu_id'";
                $resultPropietario = $conexion->ejecutarConsulta($queryPropietario);
                $propietarios = array();
                if ($resultPropietario && $resultPropietario->num_rows > 0) {
                    while ($rowPropietario = $resultPropietario->fetch_assoc()) {
                        $propietarios[] = $rowPropietario['inmupro_propietarioid'];
                    }
                }

            // Obtener id de asesor principal
                $queryAsesor = "SELECT asig_trabajadorid FROM asignacion_trabajador_inmueble WHERE asig_inmuebleid = '$inmu_id' AND asig_tipoAsignacion = 'Principal'";
                $resultAsesor = $conexion->ejecutarConsulta($queryAsesor);
                $asesores = array();
                if ($resultAsesor && $resultAsesor->num_rows > 0) {
                    while ($rowAsesor = $resultAsesor->fetch_assoc()) {
                        $asesores[] = $rowAsesor['asig_trabajadorid'];
                    }
                }

            // Obtener fotos del inmueble
            $queryFotos = "SELECT foto_id, foto_url FROM foto_inmueble WHERE foto_inmuebleId = '$inmu_id'";
            $resultFotos = $conexion->ejecutarConsulta($queryFotos);
            $fotos = array();
            if ($resultFotos && $resultFotos->num_rows > 0) {
                while ($rowFoto = $resultFotos->fetch_assoc()) {
                    $fotos[] = $rowFoto;
                }
            }

                $detalles = [
                    'idContrato' => $idContrato,
                    'propietarios' => $propietarios,
                    'asesores' => $asesores,
                    'fotos' => $fotos
                ];
            response::success($detalles, 'Detalles del inmueble obtenidos correctamente');
    }

    // ------------------------------------------------------
    public function getInmuebles() {
        $conexion = new Conexion();
        $query = "SELECT
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    inmu_ubigeo as ubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_partidaregistral as partidaregistral,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video,
                    (SELECT p.propi_id FROM propietario p 
                     INNER JOIN inmueble_propietario ip ON p.propi_id = ip.inmupro_propietarioid 
                     WHERE ip.inmupro_inmuebleid = inmueble.inmu_id LIMIT 1) as idpropietario,
                    (SELECT p.propi_nombre FROM propietario p 
                     INNER JOIN inmueble_propietario ip ON p.propi_id = ip.inmupro_propietarioid 
                     WHERE ip.inmupro_inmuebleid = inmueble.inmu_id LIMIT 1) as propietario,
                    (SELECT p.propi_celular FROM propietario p 
                     INNER JOIN inmueble_propietario ip ON p.propi_id = ip.inmupro_propietarioid 
                     WHERE ip.inmupro_inmuebleid = inmueble.inmu_id LIMIT 1) as proietariocelular,
                    -- Datos del contrato activo/vigente
                    (SELECT c.contrato_id FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as idcontrato,
                    (SELECT c.contrato_numerocontrato FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as numerocontrato,
                    (SELECT c.contrato_tipo FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as tipocontrato,
                    (SELECT c.contrato_fechainicio FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_fechainicio,
                    (SELECT c.contrato_fechafin FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_fechafin,
                    (SELECT c.contrato_mesesvigencia FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_mesesvigencia,
                    (SELECT c.contrato_comision FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_comision,
                    (SELECT c.contrato_estado FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as estadocontrato,
                    (SELECT c.contrato_mensaje FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as mensajecontrato,
                    (SELECT c.contrato_pdf FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_pdf
                FROM inmueble 
                WHERE 
                    inmu_condicioncontrato != 'Proyecto Constructivo' 
                    AND inmu_estado IN ('Activo', 'Inactivo')
                ORDER BY inmu_fecharegistro DESC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $inmuebles = array();
            while ($inmueble = $result->fetch_assoc()) {
                $inmuebles[] = $inmueble;
            }
            response::success($inmuebles, 'Lista de inmuebles obtenida correctamente');
        } else {
            response::error('No se encontraron inmuebles registrados');
        }
    }

     // ------------------------------------------------------
    // Devuelve inmuebles filtrados por estado (Activo, Inactivo, Eliminado)
    public function getInmueblesPorEstado($estado) {
        $conexion = new Conexion();
        $estadoEscaped = mysqli_real_escape_string($conexion->connectDB(), $estado);
        $query = "SELECT
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    inmu_ubigeo as ubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_partidaregistral as partidaregistral,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video,
                    (SELECT p.propi_id FROM propietario p 
                     INNER JOIN inmueble_propietario ip ON p.propi_id = ip.inmupro_propietarioid 
                     WHERE ip.inmupro_inmuebleid = inmueble.inmu_id LIMIT 1) as idpropietario,
                    (SELECT p.propi_nombre FROM propietario p 
                     INNER JOIN inmueble_propietario ip ON p.propi_id = ip.inmupro_propietarioid 
                     WHERE ip.inmupro_inmuebleid = inmueble.inmu_id LIMIT 1) as propietario,
                    (SELECT p.propi_celular FROM propietario p 
                     INNER JOIN inmueble_propietario ip ON p.propi_id = ip.inmupro_propietarioid 
                     WHERE ip.inmupro_inmuebleid = inmueble.inmu_id LIMIT 1) as proietariocelular,
                    (SELECT c.contrato_id FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as idcontrato,
                    (SELECT c.contrato_numerocontrato FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as numerocontrato,
                    (SELECT c.contrato_tipo FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as tipocontrato,
                    (SELECT c.contrato_fechainicio FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_fechainicio,
                    (SELECT c.contrato_fechafin FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_fechafin,
                    (SELECT c.contrato_mesesvigencia FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_mesesvigencia,
                    (SELECT c.contrato_comision FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_comision,
                    (SELECT c.contrato_estado FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as estadocontrato,
                    (SELECT c.contrato_mensaje FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as mensajecontrato,
                    (SELECT c.contrato_pdf FROM contrato c 
                     WHERE c.contrato_inmuebleId = inmueble.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_pdf
                FROM inmueble 
                WHERE inmu_condicioncontrato != 'Proyecto Constructivo' 
                  AND inmu_estado = '" . $estadoEscaped . "' 
                ORDER BY inmu_fecharegistro DESC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $inmuebles = array();
            while ($inmueble = $result->fetch_assoc()) {
                $inmuebles[] = $inmueble;
            }
            response::success($inmuebles, 'Lista de inmuebles obtenida correctamente');
        } else {
            response::error('No se encontraron inmuebles registrados');
        }
    }
    // ------------------------------------------------------
    public function getInmuebles_web() {
     $conexion = new Conexion();
        $query = "SELECT
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    inmu_ubigeo as ubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_partidaregistral as partidaregistral,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video,
                    propi_id  as idpropietario,
                    propi_nombre as propietario,
                    propi_celular as proietariocelular
                FROM inmueble 
								LEFT JOIN inmueble_propietario inpro ON inpro.inmupro_inmuebleid = inmueble.inmu_id
								LEFT JOIN propietario pro ON pro.propi_id = inpro.inmupro_propietarioid

                WHERE 
                    inmu_condicioncontrato != 'Proyecto Constructivo' 
                    and
                    inmu_estado='Activo'
                    ORDER BY inmu_fecharegistro DESC
                    ";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $inmuebles = array();
            while ($inmueble = $result->fetch_assoc()) {
                $inmuebles[] = $inmueble;
            }
            response::success($inmuebles, 'Lista de inmuebles obtenida correctamente');
        } else {
            response::error('No se encontraron inmuebles registrados');
        }
    }
    public function getProyectos() {
        $conexion = new Conexion();
        $query = "SELECT
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    inmu_ubigeo as ubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video,
                    proyecto_slogan as slogan,
                    sector1,
                    sector2,
                    sector3
                FROM inmueble 
                WHERE 
                    inmu_condicioncontrato != 'Proyecto Inmobiliario' and
                    inmu_estado='Activo'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $inmuebles = array();
            while ($inmueble = $result->fetch_assoc()) {
                $inmuebles[] = $inmueble;
            }
            response::success($inmuebles, 'Lista de inmuebles obtenida correctamente');
        } else {
            response::error('No se encontraron inmuebles registrados');
        }
    }

    // ------------------------------------------------------
    public function getProyecto($inmu_id) {
        $conexion = new Conexion();
        $query = "SELECT 
        inmu_id as id,
        inmu_tipobien as tipobien,
        inmu_titulo as titulo,
        inmu_descripcion as descripcion,
        inmu_areaterreno as areaterreno,
        inmu_areaconstruida as areaconstruida,
        inmu_ubigeo as ubigeo,
        inmu_condicioncontrato as condicioncontrato,
        inmu_tipooperacion as tipooperacion,
        inmu_precio as precio,
        inmu_moneda as moneda,
        inmu_percen_comision as percen_comision,
        inmu_direccion as direccion,
        inmu_fecharegistro as fecharegistro,
        inmu_estado as estado,
        inmu_latitud as latitud,
        inmu_long as longitud,
        inmu_video as video,
        proyecto_slogan as slogan,
        sector1,
        sector2,
        sector3
    FROM inmueble 
    WHERE 
       inmu_id = $inmu_id";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $inmueble = $result->fetch_assoc();
            response::success($inmueble, 'Consulta de inmueble exitosa');
        } else {
            response::error('No se encontró el inmueble');
        }
    }

    // ------------------------------------------------------
    public function get_Lista_Inmueble_web() {
        $conexion = new Conexion();
        $query = "SELECT
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    (SELECT concat( d.distrito, ' - ', pro.provincia, ' - ', dep.departamento) 
                    FROM ubdistrito d
                    INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                    INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                    WHERE d.idDist = inmueble.inmu_ubigeo) as ubigeo,
                    (SELECT d.idDist 
                    FROM ubdistrito d
                    INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                    INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                    WHERE d.idDist = inmueble.inmu_ubigeo) as idubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_partidaregistral as partidaregistral,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video,
                    inmu_categoria as categoria,
                    inmu_largo as largo,
                    inmu_ancho as ancho
                FROM 
                    inmueble
                WHERE 
                    inmu_estado = 'Activo'";
        $result = $conexion->ejecutarConsulta($query);
    
        if ($result && $result->num_rows > 0) {
            $inmuebles = array();
            while ($inmueble = $result->fetch_assoc()) {
                // Obtener fotos del inmueble
                $fotos = $this->getFotosInmueble_array($inmueble['id']);
                if (count($fotos) >= 5) {
                    $inmueble['fotos'] = $fotos;
                    // for ($i = 1; $i <= 2; $i++) {
                    $inmuebles[] = $inmueble;
                    // }
                }
            }
            
            response::success($inmuebles, 'Consulta de inmuebles exitosa');
        } else {
            response::error('No se encontró el inmueble');
        }
    }   


    public function get_Lista_Inmueble_web_filters($filters = []) {
        $conexion = new Conexion();
    
        // Base query
        $query = "SELECT
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    (SELECT CONCAT(d.distrito, ' - ', pro.provincia, ' - ', dep.departamento) 
                     FROM ubdistrito d
                     INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                     INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                     WHERE d.idDist = inmueble.inmu_ubigeo) AS ubigeo,
                     (SELECT d.idDist 
                    FROM ubdistrito d
                    INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                    INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                    WHERE d.idDist = inmueble.inmu_ubigeo) as idubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_partidaregistral as partidaregistral,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video,
                    inmu_categoria as categoria
                 FROM 
                    inmueble
                 WHERE 
                    inmu_estado = 'Activo'";
    
        // Adding dynamic filters
        $conditions = [];
    
        if (!empty($filters['inmu_tipobien'])) {
            $conditions[] = "inmu_tipobien = '" . mysqli_real_escape_string($conexion->connectDB(), $filters['inmu_tipobien']) . "'";
        }
    
        // if (!empty($filters['inmu_ubigeo'])) {
        //     $conditions[] = "inmu_ubigeo = '" . mysqli_real_escape_string($conexion->connectDB(), $filters['inmu_ubigeo']) . "'";
        // }
    
        if (!empty($filters['inmu_condicioncontrato'])) {
            $conditions[] = "inmu_condicioncontrato = '" . mysqli_real_escape_string($conexion->connectDB(), $filters['inmu_condicioncontrato']) . "'";
        }
    
        if (!empty($filters['inmu_tipooperacion'])) {
            $conditions[] = "inmu_tipooperacion = '" . mysqli_real_escape_string($conexion->connectDB(), $filters['inmu_tipooperacion']) . "'";
        }
    
        // Append conditions to query if any
        if (count($conditions) > 0) {
            $query .= ' AND ' . implode(' AND ', $conditions);
        }
    
        $result = $conexion->ejecutarConsulta($query);
    
        if ($result && $result->num_rows > 0) {
            $inmuebles = array();
            while ($inmueble = $result->fetch_assoc()) {
                // Obtener fotos del inmueble
                $fotos = $this->getFotosInmueble_array($inmueble['id']);
                if (count($fotos) >= 5) {
                    $inmueble['fotos'] = $fotos;
                    $inmuebles[] = $inmueble;
                }
            }
    
            response::success($inmuebles, 'Consulta de inmuebles exitosa');
        } else {
            response::error('No se encontró el inmueble');
        }
    }
    

    // ------------------------------------------------------
    public function get_Lista_Inmueble_paneladmin() {
        $conexion = new Conexion();
        $query = "SELECT
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    (SELECT concat( d.distrito, ' - ', pro.provincia, ' - ', dep.departamento) 
                    FROM ubdistrito d
                    INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                    INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                    WHERE d.idDist = inmueble.inmu_ubigeo) as ubigeo,
                    (SELECT d.idDist 
                    FROM ubdistrito d
                    INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                    INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                    WHERE d.idDist = inmueble.inmu_ubigeo) as idubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_partidaregistral as partidaregistral,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video
                FROM 
                    inmueble
                WHERE 
                    inmu_condicioncontrato = 'Proyecto Constructivo' and
                    inmu_estado = 'Activo'";
        $result = $conexion->ejecutarConsulta($query);
    
        if ($result && $result->num_rows > 0) {
            $inmuebles = array();
            while ($inmueble = $result->fetch_assoc()) {
                // Obtener fotos del inmueble
                $fotos = $this->getFotosInmueble_array($inmueble['id']);
                $inmueble['fotos'] = $fotos;
                $inmuebles[] = $inmueble;
            }
            
            response::success($inmuebles, 'Consulta de inmuebles exitosa');
        } else {
            response::error('No se encontró el inmueble');
        }
    }   

    public function get_Lista_Proyecto_paneladmin() {
        $conexion = new Conexion();
        $query = "SELECT
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    (SELECT concat( d.distrito, ' - ', pro.provincia, ' - ', dep.departamento) 
                    FROM ubdistrito d
                    INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                    INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                    WHERE d.idDist = inmueble.inmu_ubigeo) as ubigeo,
                    (SELECT d.idDist 
                    FROM ubdistrito d
                    INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                    INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                    WHERE d.idDist = inmueble.inmu_ubigeo) as idubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_partidaregistral as partidaregistral,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video,
                    proyecto_slogan as slogan,
                    sector1 as sector1,
                    sector2 as sector2,
                    sector3 as sector3
                FROM 
                    inmueble
                WHERE 
                    inmu_condicioncontrato = 'Proyecto Inmobiliario'";
        $result = $conexion->ejecutarConsulta($query);
    
        if ($result && $result->num_rows > 0) {
            $inmuebles = array();
            while ($inmueble = $result->fetch_assoc()) {
                // Obtener fotos del inmueble
                $fotos = $this->getFotosInmueble_array($inmueble['id']);
                $inmueble['fotos'] = $fotos;
                $inmuebles[] = $inmueble;
            }
            
            response::success($inmuebles, 'Consulta de inmuebles exitosa');
        } else {
            response::error('No se encontró el inmueble');
        }
    }   
    //--------------------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------
    //- PARA DETALLE WEB ---------------------------------------------
    //--------------------------------------------------------------------------------------------
    public function getInmueble_web($inmu_id) {
        $conexion = new Conexion();
        $query = "SELECT
                    inmu_id as id,
                    inmu_tipobien as tipobien,
                    inmu_titulo as titulo,
                    inmu_descripcion as descripcion,
                    inmu_areaterreno as areaterreno,
                    inmu_areaconstruida as areaconstruida,
                    (SELECT concat( d.distrito, ' - ', pro.provincia, ' - ', dep.departamento) 
                    FROM ubdistrito d
                    INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                    INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                    WHERE d.idDist = inmueble.inmu_ubigeo) as ubigeo,
                    (SELECT d.idDist 
                    FROM ubdistrito d
                    INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                    INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                    WHERE d.idDist = inmueble.inmu_ubigeo) as idubigeo,
                    inmu_condicioncontrato as condicioncontrato,
                    inmu_partidaregistral as partidaregistral,
                    inmu_tipooperacion as tipooperacion,
                    inmu_precio as precio,
                    inmu_moneda as moneda,
                    inmu_percen_comision as percen_comision,
                    inmu_direccion as direccion,
                    inmu_fecharegistro as fecharegistro,
                    inmu_estado as estado,
                    inmu_latitud as latitud,
                    inmu_long as longitud,
                    inmu_video as video,
                    inmu_categoria as categoria,
                    inmu_largo as largo,
                    inmu_ancho as ancho,
                      -- Subconsulta: número de propietarios
                        (SELECT COUNT(*) FROM inmueble_propietario WHERE inmupro_inmuebleid = inmueble.inmu_id) as num_propietarios,
                        -- Subconsulta: número de contratos activos/vigentes
                        (SELECT COUNT(*) FROM contrato WHERE contrato_inmuebleId = inmueble.inmu_id AND contrato_estado IN ('Activo', 'Vigente')) as num_contratos,
                        -- Subconsulta: número de asesores principales
                        (SELECT COUNT(*) FROM asignacion_trabajador_inmueble WHERE asig_inmuebleId = inmueble.inmu_id AND asig_estado = 'Activo' AND asig_tipoAsignacion = 'Principal') as num_asesores
                FROM 
                    inmueble
                WHERE 
                    inmu_estado = 'Activo' 
                    AND inmu_id = $inmu_id";
        $result = $conexion->ejecutarConsulta($query);
    
        if ($result && $result->num_rows > 0) {
            $inmueble = $result->fetch_assoc();
            // Obtener características del inmueble
            $inmueble['caracteristicas'] = $this->getCaracteristicasInmueble_array($inmu_id);
            // Obtener fotos del inmueble
            $inmueble['fotos'] = $this->getFotosInmueble_array($inmu_id);
            
            response::success($inmueble, 'Consulta de inmueble exitosa');
        } else {
            response::error('No se encontró el inmueble');
        }
    }    
    private function getCaracteristicasInmueble_array($idinmueble) {
        $conexion = new Conexion();
        $query = "SELECT ic_id, 
                            ic_descripcion as descripcion,
                            ic_idinmueble as idinmueble,
                            ic_idcarac as idcarac,
                            carac_nombre as caracteristica
                    FROM inmueble_caracteristicas,  caracteristicas
                    WHERE caracteristicas.carac_id = ic_idcarac and ic_idinmueble='$idinmueble'";
        $result = $conexion->ejecutarConsulta($query);
    
        $caracteristicas = array();
        if ($result && $result->num_rows > 0) {
            while ($caracteristica = $result->fetch_assoc()) {
                $caracteristicas[] = $caracteristica;
            }
        }
        return $caracteristicas;
    }
    
    private function getFotosInmueble_array($inmuebleId) {
        $conexion = new Conexion();
        $query = "SELECT 
                    foto_id as id,
                    foto_url as url,
                    foto_principal as principal,
                    foto_miniatura as miniatura,
                    foto_inmuebleId as inmuebleId
                FROM foto_inmueble 
                WHERE foto_inmuebleId = $inmuebleId";
        $result = $conexion->ejecutarConsulta($query);
    
        $fotos = array();
        if ($result && $result->num_rows > 0) {
            while ($foto = $result->fetch_assoc()) {
                $fotos[] = $foto;
            }
        }
        return $fotos;
    }
    //--------------------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------    

    public function delete_Inmueble($id) {
        $conexion = new Conexion();
        
        // Delete related records in foto_inmueble if they exist
        $query = "DELETE FROM foto_inmueble WHERE foto_inmuebleId = $id";
        $result1 = $conexion->save($query);
        
        // Delete related records in inmueble_caracteristicas if they exist
        $query = "DELETE FROM inmueble_caracteristicas WHERE ic_idinmueble = $id";
        $result2 = $conexion->save($query);
        
        // Delete the inmueble record if it exists
        $query = "DELETE FROM inmueble WHERE inmu_id = $id";
        $result3 = $conexion->save($query);
        
        if ($result3 > 0) {
            response::success($result3, 'Inmueble y sus características eliminados correctamente');
        } else {
            response::error('Error al eliminar el inmueble y sus características');
        }
    }
     
    // -------------------------------------------------------------------------------------
        public function eliminarLogicoInmueble($inmu_id) {
        $conexion = new Conexion();
        $query = "UPDATE inmueble SET inmu_estado = 'Eliminado' WHERE inmu_id = '$inmu_id'";
        $result = $conexion->save($query);
        if ($result > 0) {
            response::success($result, 'Inmueble eliminado lógicamente');
        } else {
            response::error('Error al eliminar el inmueble');
        }
    }

    // ------------------------------------------------------
    public function get_Inmueble_ConFotos($inmu_id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    i.inmu_id as id,
                    i.inmu_tipobien as tipobien,
                    i.inmu_titulo as titulo,
                    i.inmu_descripcion as descripcion,
                    i.inmu_areaterreno as areaterreno,
                    i.inmu_areaconstruida as areaconstruida,
                    i.inmu_ubigeo as ubigeo,
                    i.inmu_condicioncontrato as condicioncontrato,
                    i.inmu_partidaregistral as partidaregistral,
                    i.inmu_tipooperacion as tipooperacion,
                    i.inmu_precio as precio,
                    i.inmu_moneda as moneda,
                    i.inmu_percen_comision as percen_comision,
                    i.inmu_direccion as direccion,
                    i.inmu_fecharegistro as fecharegistro,
                    i.inmu_estado as estado,
                    i.inmu_latitud as latitud,
                    i.inmu_long as longitud,
                    i.inmu_video as video,
                    i.inmu_categoria as categoria,
                    i.inmu_largo as largo,
                    i.inmu_ancho as ancho
                FROM inmueble i
                LEFT JOIN cliente c ON i.inmu_propietarioId = c.cliente_id
                LEFT JOIN propietario p ON i.inmu_propietarioId = p.propietario_id
                WHERE i.inmu_id = $inmu_id";
        $result = $conexion->ejecutarConsulta($query);
    
        if ($result && $result->num_rows > 0) {
            $inmueble = $result->fetch_assoc();
            
            $fotos_query = "SELECT 
                              foto_id as id,
                              foto_url as url,
                              foto_principal as principal,
                              foto_miniatura as miniatura,
                              foto_fecharegistro as fecharegistro,
                              foto_inmuebleId as inmuebleId
                            FROM fotoinmueble 
                            WHERE foto_inmuebleId = $inmu_id";
            $fotos_result = $conexion->ejecutarConsulta($fotos_query);
            
            if ($fotos_result && $fotos_result->num_rows > 0) {
                $fotos = array();
                while ($foto = $fotos_result->fetch_assoc()) {
                    $fotos[] = $foto;
                }
                $inmueble['fotos'] = $fotos;
            } else {
                $inmueble['fotos'] = [];
            }
    
            response::success($inmueble, 'Consulta de inmueble exitosa');
        } else {
            response::error('No se encontró el inmueble');
        }
    }

    // ------------------------------------------------------
    
    // ------------------------------------------------------
    // public function add_asesor($inmu_id, $inmu_precio) {
    //     $conexion = new Conexion();
    //     $query = "UPDATE inmueble SET 
    //                 inmu_precio = '$inmu_precio'
    //             WHERE inmu_id = $inmu_id";

    //     $result = $conexion->save($query);

    //     if ($result > 0) {
    //         response::success($result, 'Precio del inmueble actualizado correctamente');
    //     } else {
    //         response::error('Error al actualizar el precio del inmueble');
    //     }
    // }
        

    // ------------------------------------------------------
    public function updateInmueblePrecio($inmu_id, $inmu_precio) {
        $conexion = new Conexion();
        $query = "UPDATE inmueble SET 
                    inmu_precio = '$inmu_precio'
                WHERE inmu_id = $inmu_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Precio del inmueble actualizado correctamente');
        } else {
            response::error('Error al actualizar el precio del inmueble');
        }
    }

    // ------------------------------------------------------
    public function update_video($inmu_id, $inmu_urlvideo) {
        $conexion = new Conexion();
        $query = "UPDATE inmueble SET 
                    inmu_video = '$inmu_urlvideo'
                WHERE inmu_id = $inmu_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Se actualizó correctamente el VIDEO');
        } else {
            response::error('Error al actualizar el precio del inmueble');
        }
    }

    // ------------------------------------------------------
    public function updateInmuebleEstado($inmu_id, $inmu_estado) {
        $conexion = new Conexion();
        $query = "UPDATE inmueble SET 
                    inmu_estado = '$inmu_estado'
                WHERE inmu_id = $inmu_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Estado del inmueble actualizado correctamente');
        } else {
            response::error('Error al actualizar el estado del inmueble');
        }
    }

    // ------------------------------------------------------
    

    // ------------------------------------------------------
    public function deleteFotoInmueble($foto_id) {
        $conexion = new Conexion();
        $query = "DELETE FROM foto_inmueble WHERE foto_id = $foto_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Foto eliminada correctamente');
        } else {
            response::error('Error al eliminar la foto');
        }
    }

    // ------------------------------------------------------
    public function getFotosInmueble($inmuebleId) {
        $conexion = new Conexion();
        $query = "SELECT 
                    foto_id as id,
                    foto_url as url,
                    foto_principal as principal,
                    foto_miniatura as miniatura,
                    foto_inmuebleId as inmuebleId
                FROM foto_inmueble 
                WHERE foto_inmuebleId = $inmuebleId";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $fotos = array();
            while ($foto = $result->fetch_assoc()) {
                $fotos[] = $foto;
            }
            response::success($fotos, 'Fotos del inmueble obtenidas correctamente');
        } else {
            response::error('No se encontraron fotos para el inmueble');
        }
    }

    // ------------------------------------------------------
    public function updateFotoPrincipal($foto_id, $foto_inmuebleId) {
        $conexion = new Conexion();
        
        // Poner todas las fotos del inmueble a 'NO' en foto_principal
        $query = "UPDATE fotoinmueble SET foto_principal = 'NO' WHERE foto_inmuebleId = $foto_inmuebleId";
        $conexion->save($query);

        // Actualizar la foto seleccionada a 'SI'
        $query = "UPDATE fotoinmueble SET foto_principal = 'SI' WHERE foto_id = $foto_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Foto principal actualizada correctamente');
        } else {
            response::error('Error al actualizar la foto principal');
        }
    }

    // ------------------------------------------------------
    // Función para almacenar imágenes y generar miniaturas
    public function subirFoto($inmuebleId, $file) {
        $uploadDir = 'uploads/fotos/';
        $miniatureDir = 'uploads/fotos/miniaturas/';
        $allowedTypes = ['jpg', 'jpeg', 'png', 'bmp'];
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

        if (!in_array(strtolower($fileType), $allowedTypes)) {
            response::error('Tipo de archivo no permitido');
            return;
        }

        // Generar un nombre de archivo único
        $newFileName = 'inmueble' . $inmuebleId . '_' . bin2hex(random_bytes(3)) . '.' . $fileType;
        $uploadFilePath = $uploadDir . $newFileName;

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($fileTmp, $uploadFilePath)) {
            // Generar miniatura
            $miniaturePath = $miniatureDir . $newFileName;
            $this->generarMiniatura($uploadFilePath, $miniaturePath, 600);
            // ---------------------------- 
            // Insertar en la base de datos
            $this->insertFotoInmueble($uploadFilePath, $miniaturePath, $inmuebleId);
        } else {
            response::error('Error al subir el archivo');
        }
    }
    // INSERTAR FOTO INMUEBLE ---------------------------------------------------------
    public function insertFotoInmueble($foto_url, $foto_miniatura, $foto_inmuebleId) {
        $conexion = new Conexion();
        $foto_fecharegistro = date("Y-m-d");

        $query = "INSERT INTO foto_inmueble (
            foto_url,
            foto_principal,
            foto_miniatura,
            foto_fecharegistro,
            foto_inmuebleId
        ) VALUES (
            '$foto_url',
            'NO',
            '$foto_miniatura',
            '$foto_fecharegistro',
            '$foto_inmuebleId'
        )";

        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Foto insertada correctamente');
        } else {
            response::error('Error al insertar la foto');
        }
    }

    // ------------------------------------------------------
    // Función para generar miniaturas
    private function generarMiniatura($filePath, $miniaturePath, $width) {
        list($originalWidth, $originalHeight) = getimagesize($filePath);
        $height = ($width / $originalWidth) * $originalHeight;

        $miniature = imagecreatetruecolor($width, $height);
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

        switch (strtolower($fileType)) {
            case 'jpg':
            case 'jpeg':
                $source = imagecreatefromjpeg($filePath);
                break;
            case 'png':
                $source = imagecreatefrompng($filePath);
                break;
            case 'bmp':
                $source = imagecreatefrombmp($filePath);
                break;
            default:
                return false;
        }

        imagecopyresampled($miniature, $source, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
        imagejpeg($miniature, $miniaturePath, 90);

        imagedestroy($miniature);
        imagedestroy($source);
    }

      // ------------------------------------------------------
      public function getInmueblesPorAsesor($tra_id) {
        $conexion = new Conexion();
        $query = "SELECT
                    i.inmu_id as id,
                    i.inmu_tipobien as tipobien,
                    i.inmu_titulo as titulo,
                    i.inmu_descripcion as descripcion,
                    i.inmu_areaterreno as areaterreno,
                    i.inmu_areaconstruida as areaconstruida,
                    (SELECT CONCAT(d.distrito, ' - ', pro.provincia, ' - ', dep.departamento) 
                     FROM ubdistrito d
                     INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                     INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                     WHERE d.idDist = i.inmu_ubigeo) as ubigeo,
                    i.inmu_condicioncontrato as condicioncontrato,
                    i.inmu_partidaregistral as partidaregistral,
                    i.inmu_tipooperacion as tipooperacion,
                    i.inmu_precio as precio,
                    i.inmu_moneda as moneda,
                    i.inmu_percen_comision as percen_comision,
                    i.inmu_direccion as direccion,
                    i.inmu_fecharegistro as fecharegistro,
                    i.inmu_estado as estado,
                    i.inmu_latitud as latitud,
                    i.inmu_long as longitud,
                    i.inmu_video as video,
                    i.inmu_categoria as categoria,
                    i.inmu_largo as largo,
                    i.inmu_ancho as ancho,
                    -- Datos del propietario
                    (SELECT p.propi_id FROM propietario p 
                     INNER JOIN inmueble_propietario ip ON p.propi_id = ip.inmupro_propietarioid 
                     WHERE ip.inmupro_inmuebleid = i.inmu_id LIMIT 1) as idpropietario,
                    (SELECT p.propi_nombre FROM propietario p 
                     INNER JOIN inmueble_propietario ip ON p.propi_id = ip.inmupro_propietarioid 
                     WHERE ip.inmupro_inmuebleid = i.inmu_id LIMIT 1) as propietario,
                    (SELECT p.propi_celular FROM propietario p 
                     INNER JOIN inmueble_propietario ip ON p.propi_id = ip.inmupro_propietarioid 
                     WHERE ip.inmupro_inmuebleid = i.inmu_id LIMIT 1) as proietariocelular,
                    -- Datos del contrato activo/vigente
                    (SELECT c.contrato_id FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as idcontrato,
                    (SELECT c.contrato_numerocontrato FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as numerocontrato,
                    (SELECT c.contrato_tipo FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as tipocontrato,
                    (SELECT c.contrato_fechainicio FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_fechainicio,
                    (SELECT c.contrato_fechafin FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_fechafin,
                    (SELECT c.contrato_mesesvigencia FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_mesesvigencia,
                    (SELECT c.contrato_comision FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_comision,
                    (SELECT c.contrato_estado FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as estadocontrato,
                    (SELECT c.contrato_mensaje FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as mensajecontrato,
                    (SELECT c.contrato_pdf FROM contrato c 
                     WHERE c.contrato_inmuebleId = i.inmu_id 
                     AND c.contrato_estado IN ('Activo', 'Vigente') 
                     ORDER BY c.contrato_fechainicio DESC LIMIT 1) as contrato_pdf
                FROM inmueble i
                INNER JOIN asignacion_trabajador_inmueble asig ON asig.asig_inmuebleId = i.inmu_id
                WHERE asig.asig_trabajadorId = '$tra_id'
                    AND asig.asig_estado = 'Activo'
                    AND i.inmu_estado = 'Activo'
                ORDER BY i.inmu_fecharegistro DESC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $inmuebles = array();
            while ($inmueble = $result->fetch_assoc()) {
                // Obtener fotos del inmueble
                $inmueble['fotos'] = $this->getFotosInmueble_array($inmueble['id']);
                $inmuebles[] = $inmueble;
            }
            response::success($inmuebles, 'Lista de inmuebles asignados obtenida correctamente');
        } else {
            response::error('No se encontraron inmuebles asignados para este asesor');
        }
    }
    
// ----------------------obtener listado de cada atributo--------------------------------------------------
        public function getTiposOperacion() {
        $conexion = new Conexion();
        $query = "SELECT inmu_tipooperacion
                FROM inmueble
                GROUP BY inmu_tipooperacion
                ORDER BY inmu_tipooperacion ASC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $tiposOperacion = [];
            while ($row = $result->fetch_assoc()) {
                $tiposOperacion[] = $row['inmu_tipooperacion'];
            }
            response::success($tiposOperacion, 'Lista de tipos de operación obtenida correctamente');
        } else {
            response::error('No se encontraron tipos de operación');
        }
    }


        public function getTiposBien() {
        $conexion = new Conexion();
        $query = "SELECT inmu_tipobien
                FROM inmueble
                GROUP BY inmu_tipobien
                ORDER BY inmu_tipobien ASC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $tiposBien = [];
            while ($row = $result->fetch_assoc()) {
                $tiposBien[] = $row['inmu_tipobien'];
            }
            response::success($tiposBien, 'Lista de tipos de bien obtenida correctamente');
        } else {
            response::error('No se encontraron tipos de bien');
        }
    }


        public function getCondicionesContrato() {
        $conexion = new Conexion();
        $query = "SELECT inmu_condicioncontrato
                FROM inmueble
                GROUP BY inmu_condicioncontrato
                ORDER BY inmu_condicioncontrato ASC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $condicionesContrato = [];
            while ($row = $result->fetch_assoc()) {
                $condicionesContrato[] = $row['inmu_condicioncontrato'];
            }
            response::success($condicionesContrato, 'Lista de condiciones de contrato obtenida correctamente');
        } else {
            response::error('No se encontraron condiciones de contrato');
        }
    }

    public function get_TiposBien() {
        $conexion = new Conexion();
        $query = "SELECT DISTINCT inmu_tipobien FROM inmueble";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $tiposBien = [];
            while ($row = $result->fetch_assoc()) {
                $tiposBien[] = $row['inmu_tipobien'];
            }
            response::success($tiposBien, 'Lista de tipos de bien obtenida correctamente');
        } else {
            response::error('No se encontraron tipos de bien');
        }
    }   

    public function get_TiposOperacion() {
        $conexion = new Conexion();
        $query = "SELECT DISTINCT inmu_tipooperacion FROM inmueble";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $tiposOperacion = [];
            while ($row = $result->fetch_assoc()) {
                $tiposOperacion[] = $row['inmu_tipooperacion'];
            }
            response::success($tiposOperacion, 'Lista de tipos de operación obtenida correctamente');
        } else {
            response::error('No se encontraron tipos de operación');
        }
    }  

    public function get_TiposCondicion() {
            $conexion = new Conexion();
            $query = "SELECT DISTINCT inmu_condicioncontrato FROM inmueble";
            $result = $conexion->ejecutarConsulta($query);

            if ($result && $result->num_rows > 0) {
                $tiposCondicion = [];
                while ($row = $result->fetch_assoc()) {
                    $tiposCondicion[] = $row['inmu_condicioncontrato'];
                }
                response::success($tiposCondicion, 'Lista de tipos de condición obtenida correctamente');
            } else {
                response::error('No se encontraron tipos de condición');
            }
        }

    public function getListados() {
    $conexion = new Conexion();

    // Consultar tipos de bien
    $queryTiposBien = "SELECT DISTINCT inmu_tipobien FROM inmueble";
    $resultTiposBien = $conexion->ejecutarConsulta($queryTiposBien);
    $tiposBien = [];
    if ($resultTiposBien && $resultTiposBien->num_rows > 0) {
        while ($row = $resultTiposBien->fetch_assoc()) {
            $tiposBien[] = $row['inmu_tipobien'];
        }
    }

    // Consultar tipos de operación
    $queryTiposOperacion = "SELECT DISTINCT inmu_tipooperacion FROM inmueble";
    $resultTiposOperacion = $conexion->ejecutarConsulta($queryTiposOperacion);
    $tiposOperacion = [];
    if ($resultTiposOperacion && $resultTiposOperacion->num_rows > 0) {
        while ($row = $resultTiposOperacion->fetch_assoc()) {
            $tiposOperacion[] = $row['inmu_tipooperacion'];
        }
    }

    // Consultar tipos de condición
    $queryTiposCondicion = "SELECT DISTINCT inmu_condicioncontrato FROM inmueble";
    $resultTiposCondicion = $conexion->ejecutarConsulta($queryTiposCondicion);
    $tiposCondicion = [];
    if ($resultTiposCondicion && $resultTiposCondicion->num_rows > 0) {
        while ($row = $resultTiposCondicion->fetch_assoc()) {
            $tiposCondicion[] = $row['inmu_condicioncontrato'];
        }
    }

    // Respuesta consolidada
    $listados = [
        'tiposBien' => $tiposBien,
        'tiposOperacion' => $tiposOperacion,
        'tiposCondicion' => $tiposCondicion
    ];

    response::success($listados, 'Listados obtenidos correctamente');
}

public function getUbigeoPiura() {
    $conexion = new Conexion();
    $query = "SELECT DISTINCT CONCAT(pro.provincia, '-', d.distrito) AS ubigeo
              FROM inmueble i
              INNER JOIN ubdistrito d ON d.idDist = i.inmu_ubigeo
              INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
              INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
              WHERE dep.departamento = 'PIURA'
              ORDER BY pro.provincia ASC, d.distrito ASC";

    $result = $conexion->ejecutarConsulta($query);

    if ($result && $result->num_rows > 0) {
        $ubigeos = [];
        while ($row = $result->fetch_assoc()) {
            $ubigeos[] = $row['ubigeo'];
        }
        response::success($ubigeos, 'Lista de ubigeos de Piura obtenida correctamente');
    } else {
        response::error('No se encontraron ubigeos para Piura');
    }
}

public function getUbigeoPiura_existentes() {
    $conexion = new Conexion();
    $query = "SELECT distinct d.idDist as Id,
            CONCAT(pro.provincia, ' - ', d.distrito) AS Ubigeo
            FROM ubdistrito d
                INNER JOIN ubprovincia pro ON pro.idProv = d.idProv
                INNER JOIN ubdepartamento dep ON dep.idDepa = pro.idDepa
                INNER JOIN inmueble inm ON inm.inmu_ubigeo = d.idDist
                ORDER BY pro.provincia ASC, d.distrito ASC";
    $result = $conexion->ejecutarConsulta($query);

    if ($result && $result->num_rows > 0) {
        $ubigeos = [];
        while ($row = $result->fetch_assoc()) {
            $ubigeos[] = $row;
        }
        response::success($ubigeos, 'Lista de ubigeos de Piura obtenida correctamente');
    } else {
        response::error('No se encontraron ubigeos para Piura');
    }
}

    // ------------------------------------------------------

    public function crearInmueblePropietarioYContrato(
    $inmu_titulo,
    $propietario_id,
    $asesor_id
) {
    $conexion = new Conexion();
    $inmu_fecharegistro = date("Y-m-d");

    // Paso 1: Crear el inmueble
    $queryInmueble = "INSERT INTO inmueble (
        inmu_titulo,
        inmu_fecharegistro,
        inmu_estado
    ) VALUES (
        '$inmu_titulo',
        '$inmu_fecharegistro',
        'Inactivo'
    )";

    $inmuebleId = $conexion->insertar($queryInmueble);

    if ($inmuebleId <= 0) {
        response::error('Error al crear el inmueble');
        return;
    }

    // Paso 2: Verificar si el propietario existe
    if (!$propietario_id) {
        response::error('Debe proporcionar un ID de propietario válido');
        return;
    }

    $queryVerificarPropietario = "SELECT COUNT(*) as count FROM propietario WHERE propi_id = '$propietario_id'";
    $resultVerificarPropietario = $conexion->ejecutarConsulta($queryVerificarPropietario);
    $rowPropietario = $resultVerificarPropietario->fetch_assoc();

    if ($rowPropietario['count'] <= 0) {
        response::error('El propietario no existe');
        return;
    }

    // Paso 3: Relacionar el propietario con el inmueble
    $queryRelacionarPropietario = "INSERT INTO inmueble_propietario (inmupro_propietarioid, inmupro_inmuebleid) VALUES ('$propietario_id', '$inmuebleId')";
    $resultRelacionarPropietario = $conexion->insertar($queryRelacionarPropietario);

    if ($resultRelacionarPropietario <= 0) {
        response::error('Error al relacionar el propietario con el inmueble');
        return;
    }

    // Paso 4: Verificar si el asesor existe
    if ($asesor_id) {
        $queryVerificarAsesor = "SELECT COUNT(*) as count FROM trabajador WHERE tra_id = '$asesor_id'";
        $resultVerificarAsesor = $conexion->ejecutarConsulta($queryVerificarAsesor);
        $rowAsesor = $resultVerificarAsesor->fetch_assoc();

        if ($rowAsesor['count'] <= 0) {
            response::error('El asesor no existe');
            return;
        }

    }

    response::success([
        'inmuebleId' => $inmuebleId,
        'propietarioId' => $propietario_id,
        'asesorId' => $asesor_id
    ], 'Inmueble, propietario creados correctamente.');
}

}
