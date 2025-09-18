<?php
include_once './utils/response.php';
include_once './config/database.php';

class WebController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }
    // ------------------------------------------------------
    public function insert_FormContact(
        $nombre,
        $apellidos,
        $asunto,
        $celular,
        $correo,
        $mensaje
    ) {
        $conexion = new Conexion();
        $fechareg = date("Y-m-d");
        $estado = "Pendiente";

        $query = "INSERT INTO formcontacto (
            contacto_nombre,
            contacto_apellidos,
            contacto_asunto,
            contacto_correo,
            contacto_celular,
            contacto_mensaje,
            contacto_fecharegistro,
            contacto_estado
        ) VALUES (
            '$nombre',
            '$apellidos',
            '$asunto',
            '$correo',
            '$celular',
            '$mensaje',
            '$fechareg',
            '$estado'
        )";

        $result = $conexion->insertar($query);
        if ($result > 0) {
            response::success($result, 'Contacto insertado correctamente');
        } else {
            response::error('Error al insertar el contacto');
        }
    }
    
 // Obtener todos los registros de la tabla formcontacto
 public function get_FormContactos() {
    $conexion = new Conexion();
    $query = "SELECT 
                    contacto_id as id,
                    contacto_nombre as nombre,
                    contacto_apellidos as apellidos ,
                    contacto_asunto as asunto,
                    contacto_correo as correo,
                    contacto_celular as celular,
                    contacto_mensaje as mensaje,
                    contacto_fecharegistro as fecharegistro,
                    contacto_estado as estado
    
                 FROM formcontacto";
    $result = $conexion->ejecutarConsulta($query);

    if ($result->num_rows > 0) {
        $contactos = array();
        $cantidad = $result->num_rows;
        while ($contacto = $result->fetch_assoc()) {
            $contactos[] = $contacto;
        }
        Response::success($contactos, 'Se encontraron ' . $cantidad . ' registros');
    } else {
        Response::error('No se encontraron contactos registrados');
    }
}

// Eliminar contacto por ID
public function delete_FormContact($contacto_id) {
    $conexion = new Conexion();
    $query = "DELETE FROM formcontacto WHERE contacto_id = $contacto_id";
    $result = $conexion->save($query);

    if ($result > 0) {
        response::success($result, 'Contacto eliminado correctamente');
    } else {
        response::error('Error al eliminar el contacto');
    }
}

// Obtener contacto por ID
public function get_FormContactById($contacto_id) {
    $conexion = new Conexion();
    $query = "SELECT contacto_id as id,
                    contacto_nombre as nombre,
                    contacto_apellidos as apellidos ,
                    contacto_asunto as asunto,
                    contacto_correo as correo,
                    contacto_celular as celular,
                    contacto_mensaje as mensaje,
                    contacto_fecharegistro as fecharegistro,
                    contacto_estado as estado 
            FROM formcontacto WHERE contacto_id = $contacto_id";
    $result = $conexion->ejecutarConsulta($query);

    if ($result->num_rows > 0) {
        $contacto = $result->fetch_assoc();
        Response::success($contacto, 'Contacto cargado correctamente');
    } else {
        Response::error('No se encontró el contacto');
    }
}

// Actualizar contacto por ID
public function update_FormContact($contacto_id, $nombre, $apellidos, $asunto, $celular, $correo, $mensaje, $estado) {
    $conexion = new Conexion();
    
    $query = "UPDATE formcontacto SET
                contacto_nombre = '$nombre',
                contacto_apellidos = '$apellidos',
                contacto_asunto = '$asunto',
                contacto_correo = '$correo',
                contacto_celular = '$celular',
                contacto_mensaje = '$mensaje',
                contacto_estado = '$estado'
              WHERE contacto_id = $contacto_id";
    
    $result = $conexion->save($query);

    if ($result > 0) {
        response::success($result, 'Contacto actualizado correctamente');
    } else {
        response::error('Error al actualizar el contacto');
    }
}




}
?>