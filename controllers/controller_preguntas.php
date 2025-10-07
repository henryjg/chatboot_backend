<?php
include_once './utils/response.php';
include_once './config/database.php';

class PreguntasController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    public function getPregunta($id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    pf_if as id,
                    pf_descripcion as descripcion,
                    pf_respuesta as respuesta,
                    pf_infoAdicional as info_adicional,
                    pf_video_url as video_url,
                    pf_imagen_url as imagen_url
                FROM preguntas_frecuentes 
                WHERE pf_if = '$id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $pregunta = $result->fetch_assoc();
            response::success($pregunta, 'Consulta de pregunta exitosa');
        } else {
            response::error('No se encontró la pregunta');
        }
    }

    public function getPreguntas() {
        $conexion = new Conexion();
        $query = "SELECT 
                   pf_if as id,
                   pf_descripcion as descripcion,
                   pf_respuesta as respuesta,
                   pf_infoAdicional as info_adicional,
                   pf_video_url as video_url,
                   pf_imagen_url as imagen_url
                FROM preguntas_frecuentes 
                ORDER BY pf_if ASC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $preguntas = array();
            while ($pregunta = $result->fetch_assoc()) {
                $preguntas[] = $pregunta;
            }
            response::success($preguntas, 'Lista de preguntas obtenida correctamente');
        } else {
            response::error('No se encontraron preguntas registradas');
        }
    }

    public function insertPregunta(
        $pf_descripcion,
        $pf_respuesta,
        $pf_infoAdicional,
        $pf_video_url,
        $pf_imagen_url
    ) {
        $conexion = new Conexion();
        
        // Validar que se proporcionen los campos obligatorios
        if (empty($pf_descripcion) || empty($pf_respuesta)) {
            response::error('La descripción y la respuesta son obligatorias');
            return;
        }
        
        // Insertar la pregunta
        $query = "INSERT INTO preguntas_frecuentes (
                    pf_descripcion, 
                    pf_respuesta,
                    pf_infoAdicional,
                    pf_video_url,
                    pf_imagen_url
                  ) VALUES (
                    '$pf_descripcion', 
                    '$pf_respuesta',
                    '$pf_infoAdicional',
                    '$pf_video_url',
                    '$pf_imagen_url'
                  )";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Pregunta creada correctamente');
        } else {
            response::error('Error al insertar la pregunta');
        }
    }

    public function updatePregunta(
        $pf_id,
        $pf_descripcion,
        $pf_respuesta,
        $pf_infoAdicional,
        $pf_video_url,
        $pf_imagen_url
    ) {
        $conexion = new Conexion();

        // Validar que se proporcionen los campos obligatorios
        if (empty($pf_descripcion) || empty($pf_respuesta)) {
            response::error('La descripción y la respuesta son obligatorias');
            return;
        }

        // Actualizar la pregunta
        $query = "UPDATE preguntas_frecuentes SET 
                    pf_descripcion = '$pf_descripcion',
                    pf_respuesta = '$pf_respuesta',
                    pf_infoAdicional = '$pf_infoAdicional',
                    pf_video_url = '$pf_video_url',
                    pf_imagen_url = '$pf_imagen_url'
                WHERE pf_if = $pf_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Pregunta actualizada correctamente');
        } else {
            response::error('Error al actualizar la pregunta');
        }
    }

    public function deletePregunta($id) {
        $conexion = new Conexion();
        
        // Verificar si la pregunta existe
        $checkQuery = "SELECT COUNT(*) as count FROM preguntas_frecuentes WHERE pf_if = '$id'";
        $checkResult = $conexion->ejecutarConsulta($checkQuery);
        $row = $checkResult->fetch_assoc();
        
        if ($row['count'] == 0) {
            response::error('La pregunta no existe');
            return;
        }
        
        // Eliminar la pregunta
        $query = "DELETE FROM preguntas_frecuentes WHERE pf_if = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Pregunta eliminada correctamente');
        } else {
            response::error('Error al eliminar la pregunta');
        }
    }

    // Función para buscar preguntas por palabra clave
    public function buscarPreguntas($termino) {
        $conexion = new Conexion();
        $query = "SELECT 
                   pf_if as id,
                   pf_descripcion as descripcion,
                   pf_respuesta as respuesta,
                   pf_infoAdicional as info_adicional,
                FROM preguntas_frecuentes 
                WHERE pf_descripcion LIKE '%$termino%' 
                   OR pf_respuesta LIKE '%$termino%'
                   OR pf_infoAdicional LIKE '%$termino%'
                ORDER BY pf_if ASC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $preguntas = array();
            while ($pregunta = $result->fetch_assoc()) {
                $preguntas[] = $pregunta;
            }
            response::success($preguntas, 'Búsqueda de preguntas realizada correctamente');
        } else {
            response::success(array(), 'No se encontraron preguntas con el término buscado');
        }
    }
}
?>
