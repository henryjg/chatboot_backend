<?php
include_once './utils/response.php';
include_once './config/database.php';

class ControladorCombo {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    // ------------------------------------------------------
    // Methods for wcombo_condicion
    // ------------------------------------------------------
    public function insertCondicion($condicion_nombre) {
        $conexion = new Conexion();
        $query = "INSERT INTO wcombo_condicion (condicion_nombre) VALUES ('$condicion_nombre')";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Condición insertada correctamente');
        } else {
            response::error('Error al insertar la condición');
        }
    }

    public function getAllCondiciones() {
        $conexion = new Conexion();
        $query = "SELECT condicion_id as id, condicion_nombre as nombre FROM wcombo_condicion";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $condiciones = array();
            while ($row = $result->fetch_assoc()) {
                $condiciones[] = $row;
            }
            response::success($condiciones, 'Consulta de todas las condiciones exitosa');
        } else {
            response::error('No se encontraron condiciones');
        }
    }

    public function deleteCondicion($condicion_id) {
        $conexion = new Conexion();
        $query = "DELETE FROM wcombo_condicion WHERE condicion_id = $condicion_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Condición eliminada correctamente');
        } else {
            response::error('Error al eliminar la condición');
        }
    }

    // ------------------------------------------------------
    // Methods for wcombo_operacion
    // ------------------------------------------------------
    public function insertOperacion($condicion_id, $condicion_nombre) {
        $conexion = new Conexion();
        $query = "INSERT INTO wcombo_operacion (condicion_id, condicion_nombre) VALUES ($condicion_id, '$condicion_nombre')";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Operación insertada correctamente');
        } else {
            response::error('Error al insertar la operación');
        }
    }

    public function getAllOperaciones() {
        $conexion = new Conexion();
        $query = "SELECT condicion_id as id, condicion_nombre as nombre FROM wcombo_operacion";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $operaciones = array();
            while ($row = $result->fetch_assoc()) {
                $operaciones[] = $row;
            }
            response::success($operaciones, 'Consulta de todas las operaciones exitosa');
        } else {
            response::error('No se encontraron operaciones');
        }
    }

    public function deleteOperacion($condicion_id) {
        $conexion = new Conexion();
        $query = "DELETE FROM wcombo_operacion WHERE condicion_id = $condicion_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Operación eliminada correctamente');
        } else {
            response::error('Error al eliminar la operación');
        }
    }

    // ------------------------------------------------------
    // Methods for wcombo_tipobien
    // ------------------------------------------------------
    public function insertTipoBien($tbien_nombre) {
        $conexion = new Conexion();
        $query = "INSERT INTO wcombo_tipobien (tbien_nombre) VALUES ('$tbien_nombre')";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Tipo de bien insertado correctamente');
        } else {
            response::error('Error al insertar el tipo de bien');
        }
    }

    public function getAllTipoBienes() {
        $conexion = new Conexion();
        $query = "SELECT tbien_id as id, tbien_nombre as nombre FROM wcombo_tipobien";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $tipoBienes = array();
            while ($row = $result->fetch_assoc()) {
                $tipoBienes[] = $row;
            }
            response::success($tipoBienes, 'Consulta de todos los tipos de bien exitosa');
        } else {
            response::error('No se encontraron tipos de bien');
        }
    }

    public function deleteTipoBien($tbien_id) {
        $conexion = new Conexion();
        $query = "DELETE FROM wcombo_tipobien WHERE tbien_id = $tbien_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Tipo de bien eliminado correctamente');
        } else {
            response::error('Error al eliminar el tipo de bien');
        }
    }
}
?>
