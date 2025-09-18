<?php
include_once './utils/response.php';
include_once './config/database.php';

class TrabajadorController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    // ------------------------------------------------------
    public function insertTrabajador(
        $tra_dni,
        $tra_nombre,
        $tra_apellido,
        $tra_email,
        $tra_cargo,
        $tra_supervisor,
        $tra_telefono,
        $tra_celular,
        $tra_fotofile,
        $tra_fnacimiento
    ) {
        $conexion = new Conexion();
        $tra_fechareg = date("Y-m-d");
        
        // Usuario y contraseña
        $tra_user = $tra_dni;
        $tra_pass = $this->encriptar($tra_dni); // Hashear la contraseña
        
        // Verificar si el DNI ya existe
        $checkQuery = "SELECT COUNT(*) as count FROM trabajador WHERE tra_dni = '$tra_dni'";
        $checkResult = $conexion->ejecutarConsulta($checkQuery);
        $row = $checkResult->fetch_assoc();
        
        if ($row['count'] > 0) {
            response::error('El DNI ya está registrado');
            return;
        }
        
        // Subir archivo y obtener URL
        $tra_fotourl = $this->subir_archivo($tra_fotofile);
        
        // Asignar rol según el cargo
        if (in_array($tra_cargo, ['Gerente General', 'Gerente Comercial'])) {
            $tra_rol = 'Administrador';
        } elseif ($tra_cargo === 'Asesor Inmobiliario') {
            $tra_rol = 'Asesor';
        } else {
            $tra_rol = 'Trabajador';
        }
        
        // Insertar nuevo trabajador
        $query = "INSERT INTO trabajador (
            tra_dni,
            tra_nombre,
            tra_apellido,
            tra_email,
            tra_cargo,
            tra_supervisor_id,
            tra_telefono,
            tra_celular,
            tra_fotourl,
            tra_eslider,
            tra_liderId,
            tra_rol,
            tra_user,
            tra_pass,
            tra_estado,
            tra_empresaId,
            tra_fechareg,
            tra_fnacimiento
        ) VALUES (
            '$tra_dni',
            '$tra_nombre',
            '$tra_apellido',
            '$tra_email',
            '$tra_cargo',
            '$tra_supervisor',
            '$tra_telefono',
            '$tra_celular',
            '$tra_fotourl',
            '',
            '',
            '$tra_rol',
            '$tra_user',
            '$tra_pass',
            'Activo',
            '1',
            '$tra_fechareg',
            '$tra_fnacimiento'
        )";
        
        $result = $conexion->insertar($query);
        
        if ($result > 0) {
            response::success($result, 'Trabajador insertado correctamente');
        } else {
            response::error('Error al insertar el trabajador');
        }
    }

    // ------------------------------------------------------
    public function getSupervisores() {
        $conexion = new Conexion();
        $query = "SELECT
                    tra_id as id,
                    tra_dni as dni,
                    tra_nombre as nombre,
                    tra_apellido as apellidos,
                    tra_email as correo,
                    tra_cargo as cargo,
                    tra_telefono as telefono,
                    tra_celular as celular,
                    CASE WHEN tra_fotourl = '' THEN 'uploads/trabajador/avatar.png' ELSE tra_fotourl END AS fotoPerfil,
                    tra_rol as rol,
                    tra_estado as estado,
                    tra_fnacimiento as fnacimiento
                FROM trabajador
                WHERE tra_cargo = 'Supervisor' AND tra_estado = 'Activo'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $supervisores = array();
            while ($supervisor = $result->fetch_assoc()) {
                $supervisores[] = $supervisor;
            }
            response::success($supervisores, 'Lista de supervisores obtenida correctamente');
        } else {
            response::error('No se encontraron supervisores registrados');
        }
    }

    // ------------------------------------------------------
    public function getTrabajador($tra_id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    t.tra_id as id,
                    t.tra_dni as dni,
                    t.tra_nombre as nombre, 
                    t.tra_apellido as apellidos,
                    t.tra_email as correo,
                    t.tra_cargo as cargo,
                    t.tra_supervisor_id as supervisor_id,
                    s.tra_nombre as supervisor_nombre,
                    t.tra_telefono as telefono,
                    t.tra_celular as celular,
                    CASE WHEN t.tra_fotourl = '' THEN 'uploads/trabajador/avatar.png' ELSE t.tra_fotourl END AS fotoPerfil,
                    t.tra_eslider as esLider,
                    t.tra_liderId as liderId,
                    t.tra_rol as rol,
                    t.tra_estado as estado,
                    t.tra_fnacimiento as fnacimiento
                FROM trabajador t
                LEFT JOIN trabajador s ON t.tra_supervisor_id = s.tra_id
                WHERE t.tra_id = $tra_id ";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $trabajador = $result->fetch_assoc();
            response::success($trabajador, 'Consulta de trabajador exitosa');
        } else {
            response::error('No se encontró el trabajador');
        }
    }

    // ------------------------------------------------------
    public function getTrabajadores() {
        $conexion = new Conexion();
        $query = "SELECT
                    tra_id as id,
                    tra_dni as dni,
                    tra_nombre as nombre, 
                    tra_apellido as apellidos,
                    tra_email as correo,
                    tra_cargo as cargo,
                    tra_supervisor_id as supervisor_id,
                    tra_telefono as telefono,
                    tra_celular as celular,
                    CASE WHEN tra_fotourl = '' THEN 'uploads/trabajador/avatar.png' ELSE tra_fotourl END AS fotoPerfil,
                    tra_eslider as esLider,
                    tra_liderId as liderId,
                    tra_rol as rol,
                    tra_user as user,
                    tra_estado as estado,
                    tra_empresaId as empresaId,
                    tra_fnacimiento as fnacimiento
                FROM trabajador
                WHERE tra_estado != 'Eliminado'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $trabajadores = array();
            while ($trabajador = $result->fetch_assoc()) {
                $trabajadores[] = $trabajador;
            }
            response::success($trabajadores, 'Lista de trabajadores obtenida correctamente');
        } else {
            response::error('No se encontraron trabajadores registrados');
        }
    }

    // ------------------------------------------------------
     // ------------------------------------------------------
    public function get_Asesores_General($asesorId = null, $supervisorId = null, $inmuebleId = null) {
        $conexion = new Conexion();
        $where = [];

        if ($asesorId) {
            $asesorIdEscaped = mysqli_real_escape_string($conexion->connectDB(), $asesorId);
            $where[] = "t.tra_id = '$asesorIdEscaped'";
        }
        if ($supervisorId) {
            $supervisorIdEscaped = mysqli_real_escape_string($conexion->connectDB(), $supervisorId);
            $where[] = "t.tra_supervisor_id = '$supervisorIdEscaped'";
        }
        if ($inmuebleId) {
            $inmuebleIdEscaped = mysqli_real_escape_string($conexion->connectDB(), $inmuebleId);
            $where[] = "i.inmu_id = '$inmuebleIdEscaped'";
        }

        $query = "SELECT
                    t.tra_id as id,
                    t.tra_dni as dni,
                    t.tra_nombre as nombre, 
                    t.tra_apellido as apellidos,
                    t.tra_email as correo,
                    t.tra_cargo as cargo,
                    t.tra_telefono as telefono,
                    t.tra_celular as celular,
                    CASE WHEN t.tra_fotourl = '' THEN 'uploads/trabajador/avatar.png' ELSE t.tra_fotourl END AS fotoPerfil,
                    t.tra_eslider as esLider,
                    t.tra_liderId as liderId,
                    t.tra_rol as rol,
                    t.tra_user as user,
                    t.tra_estado as estado,
                    t.tra_empresaId as empresaId,
                    t.tra_fnacimiento as fnacimiento,
                    ati.asig_fechainicial as fecha_asignacion,
                    i.inmu_id as idinmueble ,
                    i.inmu_tipobien as tipo_bien,
                    p.propi_nombre as propietario_nombre,
                    CONCAT(i.inmu_tipobien, ' - ', p.propi_nombre) as inmueble_asignado,
                    t.tra_supervisor_id as supervisor_id,
                    (SELECT CONCAT(tra_nombre, ' ', tra_apellido) FROM trabajador WHERE tra_id = t.tra_supervisor_id) as supervisor_nombre,
                    (SELECT tra_celular FROM trabajador WHERE tra_id = t.tra_supervisor_id) as supervisor_celular,
                    c.contrato_id as idcontrato,
                    c.contrato_fechainicio as contrato_fechainicio,
                    c.contrato_fechafin as contrato_fechafin
                FROM trabajador t
                INNER JOIN asignacion_trabajador_inmueble ati ON t.tra_id = ati.asig_trabajadorId
                INNER JOIN inmueble i ON ati.asig_inmuebleId = i.inmu_id
                LEFT JOIN inmueble_propietario ip ON i.inmu_id = ip.inmupro_inmuebleid
                LEFT JOIN propietario p ON ip.inmupro_propietarioid = p.propi_id
                LEFT JOIN contrato c ON c.contrato_inmuebleId = i.inmu_id
                WHERE t.tra_cargo = 'Asesor Inmobiliario' AND ati.asig_estado = 'Activo'";

        if (count($where) > 0) {
            $query .= " AND " . implode(" AND ", $where);
        }
        $query .= " ORDER BY t.tra_id DESC";

        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $asesores = array();
            while ($asesor = $result->fetch_assoc()) {
                $asesores[] = $asesor;
            }
            response::success($asesores, 'Lista de asesores obtenida correctamente');
        } else {
            response::error('No se encontraron asesores registrados');
        }
    }
    // ------------------------------------------------------
    
    // --------------------------------------------------------
    public function getAsesores() {
        $conexion = new Conexion();
        $query = "SELECT
                    tra_id as id,
                    tra_dni as dni,
                    tra_nombre as nombre, 
                    tra_apellido as apellidos,
                    tra_email as correo,
                    tra_cargo as cargo,
                    tra_telefono as telefono,
                    tra_celular as celular,
                    CASE WHEN tra_fotourl = '' THEN 'uploads/trabajador/avatar.png' ELSE tra_fotourl END AS fotoPerfil,
                    tra_eslider as esLider,
                    tra_liderId as liderId,
                    tra_rol as rol,
                    tra_user as user,
                    tra_estado as estado,
                    tra_empresaId as empresaId,
                    tra_fnacimiento as fnacimiento
                FROM trabajador WHERE tra_cargo='Asesor Inmobiliario' and tra_estado='Activo'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $trabajadores = array();
            while ($trabajador = $result->fetch_assoc()) {
                $trabajadores[] = $trabajador;
            }
            response::success($trabajadores, 'Lista de trabajadores obtenida correctamente');
        } else {
            response::error('No se encontraron trabajadores registrados');
        }
    }

    // ------------------------------------------------------
    public function getAsesores_asignado($inmuebleId) {
        $conexion = new Conexion();
        $query = "SELECT    tra_id as id,
                            tra_dni as dni,
                            tra_nombre as nombre, 
                            tra_apellido as apellidos,
                            tra_email as correo,
                            tra_cargo as cargo,
                            tra_telefono as telefono,
                            tra_celular as celular,
                            CASE WHEN tra_fotourl = '' THEN 'uploads/trabajador/avatar.png' ELSE tra_fotourl END AS fotoPerfil,
                            tra_eslider as esLider,
                            tra_liderId as liderId,
                            tra_rol as rol,
                            tra_user as user,
                            tra_estado as estado,
                            tra_empresaId as empresaId,
                            tra_fnacimiento as fnacimiento,
                            asig_inmuebleId as idasignacion,
                            asig_fechaInicial as fechainicial,
                            asig_fechaFinal as fechaFinal,
                            asig_estado as estado,
                            asig_comentario as comentario,
                            asig_tipoAsignacion as tipoAsignacion
                FROM trabajador t
                INNER JOIN asignacion_trabajador_inmueble asig ON asig.asig_trabajadorId = t.tra_id
                INNER JOIN inmueble inmu ON inmu.inmu_id = asig.asig_inmuebleId
                WHERE inmu.inmu_id='".$inmuebleId."' and 
                      t.tra_cargo='Asesor Inmobiliario' and t.tra_estado='Activo'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $trabajadores = array();
            while ($trabajador = $result->fetch_assoc()) {
                $trabajadores[] = $trabajador;
            }
            response::success($trabajadores, 'Lista de trabajadores obtenida correctamente');
        } else {
            response::error('No se encontraron trabajadores registrados');
        }
    }


    // ------------------------------------------------------
    public function Asignar_Asesor(
        $asi_trabajadorId,
        $asi_inmuebleId,
        $asi_tipoAsignacion
    ) {
        $conexion = new Conexion();
        $asi_fechainicio = date("Y-m-d"); // Use the current date for assignment
    
        // Verificar si ya hay 2 asesores asignados
        $checkQuery = "SELECT COUNT(*) as count FROM asignacion_trabajador_inmueble WHERE asig_inmuebleId = '$asi_inmuebleId' and asig_estado='Activo'";
        $checkResult = $conexion->ejecutarConsulta($checkQuery);
        $row = $checkResult->fetch_assoc();
    
        if ($row['count'] >= 2) {
            response::error('El inmueble ya posee dos asesores activos');
            return;
        }
    
        // Insertar nuevo asesor
        $query = "INSERT INTO asignacion_trabajador_inmueble (
            asig_fechainicial,
            asig_trabajadorId,
            asig_inmuebleId,
            asig_tipoAsignacion,
            asig_estado
        ) VALUES (
            '$asi_fechainicio',
            '$asi_trabajadorId',
            '$asi_inmuebleId',
            '$asi_tipoAsignacion',
            'Activo'
        )";
    
        $result = $conexion->insertar($query);
    
        if ($result > 0) {
            response::success($result, 'Asesor asignado correctamente');
        } else {
            response::error('Error al asignar el asesor');
        }
    }
    

    public function deletePropietarioAsignado($idasignacion) {
        $conexion = new Conexion();
        $query = "DELETE FROM asignacion_trabajador_inmueble WHERE asig_id = $idasignacion";
        $result = $conexion->save($query);
        if ($result > 0) {
            response::success($result, 'Se quitado la asignación correctamente');
        } else {
            response::error('Error al eliminar el propietario');
        }
    }


    public function quitar_asignacion($trabajadorId, $inmuebleId, $comentario) {
        $conexion = new Conexion();
        $tra_fechareg = date("Y-m-d"); // Use the current date for finalization
        $query = "UPDATE asignacion_trabajador_inmueble SET 
                    asig_fechaFinal = '$tra_fechareg',
                    asig_estado = 'Inactivo',
                    asig_comentario = '$comentario'
                WHERE asig_trabajadorId = $trabajadorId AND asig_inmuebleId = $inmuebleId";

        $result = $conexion->save($query);
        if ($result > 0) {
            response::success($result, 'Asignación quitada correctamente');
        } else {
            response::error('Error al quitar la asignación');
        }
    }

    // ------------------------------------------------------
    public function eliminarAsesorAsignado($asig_id) {
        $conexion = new Conexion();
        $query = "DELETE FROM asignacion_trabajador_inmueble WHERE asig_id = $asig_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Asesor asignado eliminado correctamente');
        } else {
            response::error('Error al eliminar el asesor asignado');
        }
    }
    
    // ------------------------------------------------------
    public function updateTrabajador(
        $tra_id,
        $tra_dni,
        $tra_nombre,
        $tra_apellido,
        $tra_email,
        $tra_cargo,
        $tra_supervisor,
        $tra_telefono,
        $tra_celular,
        $tra_fnacimiento,
        $tra_fotofile
    ) {
        $conexion = new Conexion();
       // Si se envía una nueva imagen, subirla y usar la nueva URL
        if ($tra_fotofile) {
            $tra_fotourl = $this->subir_archivo($tra_fotofile);
        } else {
            // Si no, obtener la URL actual de la base de datos
            $query_foto = "SELECT tra_fotourl FROM trabajador WHERE tra_id = $tra_id";
            $result_foto = $conexion->ejecutarConsulta($query_foto);
            $row_foto = $result_foto->fetch_assoc();
            $tra_fotourl = $row_foto['tra_fotourl'] ?? '';
        }

        // Asignar rol según el cargo
        if (in_array($tra_cargo, ['Gerente General', 'Gerente Comercial'])) {
            $tra_rol = 'Administrador';
        } elseif ($tra_cargo === 'Asesor Inmobiliario') {
            $tra_rol = 'Asesor';
        } else {
            $tra_rol = 'Trabajador';
        }
        
        // Construir la consulta base
        $query = "UPDATE trabajador SET 
                    tra_dni = '$tra_dni',
                    tra_nombre = '$tra_nombre',
                    tra_apellido = '$tra_apellido',
                    tra_email = '$tra_email',
                    tra_cargo = '$tra_cargo',
                    tra_supervisor_id = '$tra_supervisor',
                    tra_rol = '$tra_rol',
                    tra_telefono = '$tra_telefono',
                    tra_celular = '$tra_celular',
                    tra_fnacimiento = '$tra_fnacimiento',
                    tra_fotourl = '$tra_fotourl'
                WHERE tra_id = $tra_id";
        
        $result = $conexion->save($query);
        
        if ($result > 0) {
            response::success($result, 'Trabajador actualizado correctamente');
        } else {
            response::error('Error al actualizar el trabajador');
        }
    }
    // ------------------------------------------------------
    public function updateEstado($tra_id,$tra_estado) {
        $conexion = new Conexion();
        $query = "UPDATE trabajador SET 
                    tra_estado = '$tra_estado'
                WHERE tra_id = $tra_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Trabajador actualizado correctamente');
        } else {
            response::error('Error al actualizar el trabajador');
        }
    }


    // ------------------------------------------------------
            // Elimina la asignación de un asesor a un inmueble específico
        public function eliminarAsignacionAsesorInmueble($trabajadorId, $inmuebleId) {
            $conexion = new Conexion();
            $query = "DELETE FROM asignacion_trabajador_inmueble WHERE asig_trabajadorId = '$trabajadorId' AND asig_inmuebleId = '$inmuebleId'";
            $result = $conexion->save($query);
            if ($result > 0) {
                response::success($result, 'Asignación de asesor al inmueble eliminada correctamente');
            } else {
                response::error('Error al eliminar la asignación del asesor al inmueble');
            }
        }

    // ---------------------------------------------------------------------
    // public function updatePassword($tra_id,$tra_pass) {
    //     $conexion = new Conexion();
    //     $query = "UPDATE trabajador SET 
    //                 tra_pass = '$tra_pass'
    //             WHERE tra_id = $tra_id";

    //     $result = $conexion->save($query);

    //     if ($result > 0) {
    //         response::success($result, 'Trabajador actualizado correctamente');
    //     } else {
    //         response::error('Error al actualizar el trabajador');
    //     }
    // }

    public function updatePassword($tra_id, $tra_pass) {
        $conexion = new Conexion();
        
        // Validar parámetros
        if (!is_numeric($tra_id)) {
            response::error('El ID del trabajador debe ser numérico');
            return;
        }
        if (empty($tra_pass)) {
            response::error('La contraseña no puede estar vacía');
            return;
        }
        
        // Hashear la contraseña
        $claveEncriptada = $this->encriptar($tra_pass);
        
        // Usar prepared statement
        $query = "UPDATE trabajador SET tra_pass = ? WHERE tra_id = ?";
        $stmt = $conexion->connectDB()->prepare($query);
        if (!$stmt) {
            response::error('Error al preparar la consulta');
            return;
        }
        
        $stmt->bind_param("si", $claveEncriptada, $tra_id);
        $result = $stmt->execute() ? $stmt->affected_rows : 0;
        $stmt->close();
        
        if ($result > 0) {
            response::success($result, 'Contraseña actualizada correctamente');
        } else {
            response::error('Error al actualizar la contraseña o el trabajador no existe');
        }
    }


    // ------------------------------------------------------
    // Actualizar como lider
    public function updateLider($tra_id, $tra_eslider,$tra_liderId) {
        $conexion = new Conexion();
        $query = "UPDATE trabajador SET 
                    tra_eslider = '$tra_eslider',
                    tra_liderId = '$tra_liderId'
                WHERE tra_id = $tra_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Trabajador actualizado correctamente');
        } else {
            response::error('Error al actualizar el trabajador');
        }
    }


    // ------------------------------------------------------
    public function updateEstadoTrabajador($tra_id, $tra_estado) {
        $conexion = new Conexion();
        $query = "UPDATE trabajador SET 
                    tra_estado = '".$tra_estado."'
                WHERE tra_id = '".$tra_id."'";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Estado del trabajador actualizado correctamente');
        } else {
            response::error('Error al actualizar el estado del trabajador');
        }
    }
  
    // ------------------------------------------------------
    // Cambia el estado de un asesor a 'Eliminado' sin borrarlo de la BD
    public function eliminarLogicoAsesor($tra_id) {
        $conexion = new Conexion();
        $query = "UPDATE trabajador SET tra_estado = 'Eliminado' WHERE tra_id = '$tra_id'";
        $result = $conexion->save($query);
        if ($result > 0) {
            response::success($result, 'Asesor eliminado lógicamente');
        } else {
            response::error('Error al eliminar el asesor');
        }
    }
    //---------------------------------------------------------------------------------
    
    public function detecta_extension($mi_extension)
    {
        $ext = explode(".", $mi_extension);
        return end($ext);
    }


    public function subir_archivo($imgFile){
        if($imgFile!=""){
            $ruta="./uploads/trabajador/";
            if (!file_exists($ruta)) {
                mkdir($ruta);
            }

            $nuevo_nombre =  "foto_".rand(1000000, 9999999);
            $nuevo_nombre_completo = $nuevo_nombre . '.' . $this->detecta_extension(basename($imgFile['name']));
            $uploadfile = "./uploads/trabajador/". $nuevo_nombre_completo;
            $ruta_archivo   = "uploads/trabajador/". $nuevo_nombre_completo;

            $restriccionLogo = "NOPERMITIDO";

            //Validamos Tipo --------------------------------------------------------
            $permitidos = array("image/bmp", "image/jpg", "image/jpeg", "image/png");
            if (in_array($imgFile['type'], $permitidos)) {
                $restriccionLogo = "PERMITIDO";

                if (move_uploaded_file($imgFile['tmp_name'], $uploadfile)) {
                    // Abrir la imagen original
                    switch ($imgFile['type']) {
                        case 'image/bmp':
                            $imagen = imagecreatefrombmp($uploadfile); // Función para BMP (debe estar definida)
                            break;
                        case 'image/jpg':
                        case 'image/jpeg':
                            $imagen = imagecreatefromjpeg($uploadfile);
                            break;
                        case 'image/png':
                            $imagen = imagecreatefrompng($uploadfile);
                            break;
                        default:
                            $imagen = false;
                    }

                    if ($imagen !== false) {
                        // Obtener dimensiones de la imagen
                        $ancho_original = imagesx($imagen);
                        $alto_original = imagesy($imagen);

                        // Determinar el lado más corto para el recorte
                        $lado_corto = min($ancho_original, $alto_original);

                        // Coordenadas para recortar centrado
                        $x = ($ancho_original - $lado_corto) / 2;
                        $y = ($alto_original - $lado_corto) / 2;

                        // Crear una imagen cuadrada recortada
                        $imagen_cuadrada = imagecrop($imagen, ['x' => $x, 'y' => $y, 'width' => $lado_corto, 'height' => $lado_corto]);

                        if ($imagen_cuadrada !== false) {
                            // Guardar la imagen recortada
                            imagejpeg($imagen_cuadrada, $uploadfile); // Guardar como JPEG
                            imagedestroy($imagen_cuadrada);
                        }
                        imagedestroy($imagen);
                    }
                }
            }

        }else{
            $ruta_archivo="";
        }
        return $ruta_archivo;
    }

    // Login  --------------------------------------------------------------------
    public function login($user,$pass) {
        $connect  = new conexion();
        $query  ="SELECT  tra_user, tra_pass, tra_estado, tra_id
                  FROM    trabajador   WHERE   tra_user = '".$user."'";
        $result = $connect->ejecutarConsulta($query);

        // Verificar si el usuario existe
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verificar si la clave coincide
            if ($this->verificar_contrasena($pass,$row['tra_pass'])) {
                // Verificar si el acceso está activo
                if ($row['tra_estado'] === 'Activo') {
                    $trabajador = $this->getTrabajador($row['tra_id']);
                    return $trabajador;
                    exit();
                } else {
                    Response::error("Su acceso ha sido suspendido");
                }
            } else {
                Response::error("La clave es incorrecta");
            }
        } else {
            Response::error("El usuario no existe");
        }
    }

    // ------------------------------------------------------
    public function updatePass($tra_id, $password) {
        // ---------------------------------------------------
        $claveEmcriptada = $this->encriptar($password);
        // ---------------------------------------------------
        $conexion = new Conexion();
        $query = "UPDATE trabajador SET 
                    tra_pass = '$claveEmcriptada'
                WHERE tra_id = $tra_id";
        $result = $conexion->save($query);
        // ---------------------------------------------------
        if ($result > 0) {
            response::success($result, 'Contraseña actualizada');
        } else {
            response::error('Error al actualizar contraseña');
        }
    }

    // ------------------------------------------------------
    function encriptar($contrasena){
        return password_hash($contrasena, PASSWORD_DEFAULT);
    }
    
    function verificar_contrasena($contrasena, $hash) {
        return password_verify($contrasena, $hash);
    }

}
?>
