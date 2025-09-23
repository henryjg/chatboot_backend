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
        
        // Asignar siempre el rol Administrador al registrar
        $tra_rol = 'Administrador';
        
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
    public function updateTrabajador(
        $tra_id,
        $tra_dni,
        $tra_nombre,
        $tra_apellido,
        $tra_email,
        $tra_cargo,
        $tra_supervisor,
        $tra_rol,
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
        // if (in_array($tra_cargo, ['Gerente General', 'Gerente Comercial'])) {
        //     $tra_rol = 'Administrador';
        // } elseif ($tra_cargo === 'Asesor Inmobiliario') {
        //     $tra_rol = 'Asesor';
        // } else {
        //     $tra_rol = 'Trabajador';
        // }
        
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
    public function deleteTrabajador($tra_id) {
        $conexion = new Conexion();
        
        // Verificar si el trabajador existe
        $checkQuery = "SELECT COUNT(*) as count, tra_fotourl FROM trabajador WHERE tra_id = '$tra_id'";
        $checkResult = $conexion->ejecutarConsulta($checkQuery);
        $row = $checkResult->fetch_assoc();
        
        if ($row['count'] == 0) {
            response::error('El trabajador no existe');
            return;
        }
        
        // Obtener la URL de la foto para eliminar el archivo
        $tra_fotourl = $row['tra_fotourl'];
        
        // Eliminar el trabajador de la base de datos
        $query = "DELETE FROM trabajador WHERE tra_id = '$tra_id'";
        $result = $conexion->save($query);
        
        if ($result > 0) {
            // Si la eliminación fue exitosa, intentar eliminar la foto del servidor
            if (!empty($tra_fotourl) && $tra_fotourl !== 'uploads/trabajador/avatar.png' && file_exists('./' . $tra_fotourl)) {
                unlink('./' . $tra_fotourl);
            }
            response::success($result, 'Trabajador eliminado correctamente');
        } else {
            response::error('Error al eliminar el trabajador');
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
