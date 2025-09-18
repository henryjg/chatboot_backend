<?php
include_once './utils/response.php';
include_once './config/database.php';

class CategoriasController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    // Obtener todas las categorías
    public function getCategorias() {
        $conexion = new Conexion();
        $query = "SELECT id, nombre FROM categorias";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $categorias = array();
            while ($categoria = $result->fetch_assoc()) {
                $categorias[] = $categoria;
            }
            response::success($categorias, 'Lista de categorías obtenida correctamente');
        } else {
            response::error('No se encontraron categorías');
        }
    }

    // Obtener una categoría por ID
    public function getCategoria($id) {
        $conexion = new Conexion();
        $query = "SELECT id, nombre FROM categorias WHERE id = $id";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $categoria = $result->fetch_assoc();
            response::success($categoria, 'Categoría obtenida correctamente');
        } else {
            response::error('No se encontró la categoría');
        }
    }

    // Crear una nueva categoría
    public function insertCategoria($nombre) {
        $conexion = new Conexion();
        $query = "INSERT INTO categorias (nombre) VALUES ('$nombre')";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Categoría creada correctamente');
        } else {
            response::error('Error al crear la categoría');
        }
    }

    // Actualizar una categoría existente
    public function updateCategoria($id, $nombre) {
        $conexion = new Conexion();
        $query = "UPDATE categorias SET nombre = '$nombre' WHERE id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Categoría actualizada correctamente');
        } else {
            response::error('Error al actualizar la categoría');
        }
    }

    // Eliminar una categoría
    public function deleteCategoria($id) {
        $conexion = new Conexion();
        $query = "DELETE FROM categorias WHERE id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Categoría eliminada correctamente');
        } else {
            response::error('Error al eliminar la categoría');
        }
    }
}
?>
