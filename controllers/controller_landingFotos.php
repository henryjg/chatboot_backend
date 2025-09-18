<?php 
include_once './utils/response.php';
include_once './config/database.php';

class LandingFotosController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    // Obtener todas las fotos de una landing
    public function getFotoLanding($fotos_id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    fotos_id as id,
                    foto_url as url,
                    foto_seccion as seccion,
                    foto_miniatura as miniatura,
                    foto_fechreg as fechreg
                FROM landing_fotos 
                WHERE fotos_id = $fotos_id";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $fotos = array();
            while ($foto = $result->fetch_assoc()) {
                $fotos[] = $foto;
            }
            response::success($fotos, 'Lista de fotos obtenida correctamente');
        } else {
            response::error('No se encontraron fotos para esta landing');
        }
    }

    public function getFotosLanding() {
        $conexion = new Conexion();
        $query = "SELECT 
                    fotos_id as id,
                    foto_url as url,
                    foto_seccion as seccion,
                    foto_miniatura as miniatura,
                    foto_fechreg as fechreg,
                    landing_id as landingId
                FROM landing_fotos";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $landings = array();
            while ($landing = $result->fetch_assoc()) {
                $landings[] = $landing;
            }
            response::success($landings, 'Lista de landings obtenida correctamente');
        } else {
            response::error('No se encontraron landings registrados');
        }
    }

    // Insertar nueva foto
    public function insertFotoLanding($foto_url, $foto_seccion, $foto_miniatura, $landing_id) {
        $conexion = new Conexion();

        $query = "INSERT INTO landing_fotos (
            foto_url,
            foto_seccion,
            foto_miniatura,
            foto_fechreg,
            landing_id
        ) VALUES (
            '$foto_url',
            '$foto_seccion',
            '$foto_miniatura',
            NOW(),
            $landing_id
        )";

        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Foto insertada correctamente');
        } else {
            response::error('Error al insertar la foto');
        }
    }

    // Actualizar foto
    public function updateFotoLanding($fotos_id, $foto_url, $foto_seccion, $foto_miniatura,$landing_id) {
        $conexion = new Conexion();
        $query = "UPDATE landing_fotos SET 
                    foto_url = '$foto_url',
                    foto_seccion = '$foto_seccion',
                    foto_miniatura = '$foto_miniatura',
                    landing_id = '$landing_id'
                WHERE fotos_id = $fotos_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Foto actualizada correctamente');
        } else {
            response::error('Error al actualizar la foto');
        }
    }

    // Eliminar foto
    public function deleteFotoLanding($fotos_id) {
        $conexion = new Conexion();
        $query = "DELETE FROM landing_fotos WHERE fotos_id = $fotos_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Foto eliminada correctamente');
        } else {
            response::error('Error al eliminar la foto');
        }
    }
}
?>
