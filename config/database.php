<?php
include_once 'config.php';
class conexion{
    private $conexion;

    function connectDB()
    {
        $this->conexion = mysqli_connect(SERVER, USER, PASS, DB, DB_PORT);
        if ($this->conexion) {
            //OKey todo
        } else {
            echo 'Ha sucedido un error inexperado en la conexion de la base de datos<br>';
        }
        mysqli_query($this->conexion, "SET NAMES 'utf8'");
        mysqli_set_charset($this->conexion, "utf8");
        return $this->conexion;
    }

    function cerrarConexion() {
        if ($this->conexion !== null) {
            $close = mysqli_close($this->conexion);
            if ($close) {
                return true; // Conexión cerrada correctamente
            } else {
                echo 'Ha ocurrido un error inesperado al cerrar la conexión de la base de datos: ' . mysqli_error($this->conexion);
                return false; // Error al cerrar la conexión
            }
        } else {
          
            return false; // La conexión ya estaba cerrada previamente
        }
    }

    function ejecutarConsulta($consulta)
    {
        $this->connectDB();
        $resultado = mysqli_query($this->conexion, $consulta);
        if (!$resultado) {
            echo 'Ha sucedido un error al ejecutar la consulta: ' . mysqli_error($this->conexion);
            return null;
        }
        return $resultado;
    }

    function insertar($consulta)
    {
        $this->connectDB();
        $resultado = mysqli_query($this->conexion, $consulta);
        if (!$resultado) {
            echo 'Ha sucedido un error al ejecutar la consulta: ' . mysqli_error($this->conexion);
            $res = 0;
        } else {
            $res = mysqli_insert_id($this->conexion);
        }
        return $res;
    }

    public function save($consulta)
    {
        $this->connectDB();
        $resultado = mysqli_query($this->conexion, $consulta);
        if ($resultado) {
            $res = 1;
        } else {
            echo 'Ha sucedido un error al ejecutar la consulta: ' . mysqli_error($this->conexion);
            $res = 0;
        }
        return $res;
    }
}

// Crear una instancia global de la conexión para compatibilidad
$conexionObj = new conexion();
$database = $conexionObj->connectDB();
?>
