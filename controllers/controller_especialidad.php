<?php
include_once './utils/response.php';
include_once './config/database.php';

class EspecialidadController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    public function getEspecialidad($id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    espe_id as id,
                    espe_nombre as nombre,
                    espe_descripcion as descripcion,
                    espe_url as url
                FROM especialidad 
                WHERE espe_id = '$id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $especialidad = $result->fetch_assoc();
            response::success($especialidad, 'Consulta de especialidad exitosa');
        } else {
            response::error('No se encontró la especialidad');
        }
    }

    public function getEspecialidades() {
        $conexion = new Conexion();
        $query = "SELECT 
                   espe_id as id,
                   espe_nombre as nombre,
                   espe_descripcion as descripcion,
                   espe_url as url
                FROM especialidad 
                ORDER BY espe_nombre ASC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $especialidades = array();
            while ($especialidad = $result->fetch_assoc()) {
                $especialidades[] = $especialidad;
            }
            response::success($especialidades, 'Lista de especialidades obtenida correctamente');
        } else {
            response::error('No se encontraron especialidades registradas');
        }
    }

    public function insertEspecialidad(
        $espe_nombre,
        $espe_descripcion,
        $espe_url
    ) {
        $conexion = new Conexion();
        
        // Validar que se proporcione el nombre (obligatorio)
        if (empty($espe_nombre)) {
            response::error('El nombre de la especialidad es obligatorio');
            return;
        }
        
        // Verificar si el nombre ya existe
        $checkQuery = "SELECT COUNT(*) as count FROM especialidad WHERE espe_nombre = '$espe_nombre'";
        $checkResult = $conexion->ejecutarConsulta($checkQuery);
        $row = $checkResult->fetch_assoc();
        
        if ($row['count'] > 0) {
            response::error('Ya existe una especialidad con ese nombre');
            return;
        }
        
        // Insertar la especialidad
        $query = "INSERT INTO especialidad (
                    espe_nombre, 
                    espe_descripcion,
                    espe_url
                  ) VALUES (
                    '$espe_nombre', 
                    '$espe_descripcion',
                    '$espe_url'
                  )";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Especialidad creada correctamente');
        } else {
            response::error('Error al insertar la especialidad');
        }
    }

    public function updateEspecialidad(
        $espe_id,
        $espe_nombre,
        $espe_descripcion,
        $espe_url
    ) {
        $conexion = new Conexion();

        // Validar que se proporcione el nombre (obligatorio)
        if (empty($espe_nombre)) {
            response::error('El nombre de la especialidad es obligatorio');
            return;
        }

        // Verificar si el nombre ya existe (excluyendo el registro actual)
        $checkQuery = "SELECT COUNT(*) as count FROM especialidad WHERE espe_nombre = '$espe_nombre' AND espe_id != '$espe_id'";
        $checkResult = $conexion->ejecutarConsulta($checkQuery);
        $row = $checkResult->fetch_assoc();
        
        if ($row['count'] > 0) {
            response::error('Ya existe una especialidad con ese nombre');
            return;
        }

        // Actualizar la especialidad
        $query = "UPDATE especialidad SET 
                    espe_nombre = '$espe_nombre',
                    espe_descripcion = '$espe_descripcion',
                    espe_url = '$espe_url'
                WHERE espe_id = $espe_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Especialidad actualizada correctamente');
        } else {
            response::error('Error al actualizar la especialidad');
        }
    }

    public function deleteEspecialidad($id) {
        $conexion = new Conexion();
        
        // Verificar si la especialidad existe
        $checkQuery = "SELECT COUNT(*) as count FROM especialidad WHERE espe_id = '$id'";
        $checkResult = $conexion->ejecutarConsulta($checkQuery);
        $row = $checkResult->fetch_assoc();
        
        if ($row['count'] == 0) {
            response::error('La especialidad no existe');
            return;
        }
        
        // Verificar si hay citas asociadas a esta especialidad (si la columna existe)
        $checkCitasQuery = "SELECT COUNT(*) as count FROM citas WHERE cita_especialidad_id = '$id'";
        $checkCitasResult = $conexion->ejecutarConsulta($checkCitasQuery);
        
        // Si la consulta falla (columna no existe), permitir la eliminación por ahora
        if ($checkCitasResult === null) {
            // La columna citas_especialidad_id no existe aún en la tabla
            // Continuar con la eliminación
        } else {
            $citasRow = $checkCitasResult->fetch_assoc();
            
            if ($citasRow['count'] > 0) {
                response::error('No se puede eliminar la especialidad porque tiene citas asociadas');
                return;
            }
        }
        
        // Eliminar la especialidad
        $query = "DELETE FROM especialidad WHERE espe_id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Especialidad eliminada correctamente');
        } else {
            response::error('Error al eliminar la especialidad');
        }
    }

    // Función para buscar especialidades por nombre
    public function buscarEspecialidades($termino) {
        $conexion = new Conexion();
        $query = "SELECT 
                   espe_id as id,
                   espe_nombre as nombre,
                   espe_descripcion as descripcion,
                   espe_url as url
                FROM especialidad 
                WHERE espe_nombre LIKE '%$termino%' 
                   OR espe_descripcion LIKE '%$termino%'
                ORDER BY espe_nombre ASC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $especialidades = array();
            while ($especialidad = $result->fetch_assoc()) {
                $especialidades[] = $especialidad;
            }
            response::success($especialidades, 'Búsqueda de especialidades realizada correctamente');
        } else {
            response::success(array(), 'No se encontraron especialidades con el término buscado');
        }
    }
}
?>