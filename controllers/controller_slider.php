<?php
include_once './utils/response.php';
include_once './config/database.php';

class SliderController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    public function getSlider($id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    url_imagen as imagen,
                    slider_nombre as nombre,
                    slider_estado as estado
                FROM slider 
                WHERE slider_id = '$id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $landing = $result->fetch_assoc();
            response::success($landing, 'Consulta de sliders exitosa');
        } else {
            response::error('No se encontró sliders');
        }
    }

    public function getSliders() {
        $conexion = new Conexion();
        $query = "SELECT 
                   slider_id as id,
                   url_imagen as imagen,
                   slider_nombre as nombre,
                   slider_estado as estado
                FROM slider";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $landings = array();
            while ($landing = $result->fetch_assoc()) {
                $landings[] = $landing;
            }
            response::success($landings, 'Lista de slider obtenida correctamente');
        } else {
            response::error('No se encontraron slider registrados');
        }
    }
    public function updateSlider(
        $slider_id,
        $slider_url,
        $slider_nombre,
        $slider_estado
    ) {
        $conexion = new Conexion();
        $query = "UPDATE slider SET 
                    url_imagen = '$slider_url',
                    slider_nombre = '$slider_nombre',
                    slider_estado = '$slider_estado'
                WHERE slider_id = $slider_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Slider actualizado correctamente');
        } else {
            response::error('Error al actualizar el slider');
        }
    }


    public function insertSlider(
        $slider_url,
        $slider_nombre
    ) {
        $conexion = new Conexion();
        
        // Verificar si el nombre del slider ya existe
        // $checkQuery = "SELECT COUNT(*) as count FROM slider WHERE nombreSlider = '$slider_nombre'";
        // $checkResult = $conexion->ejecutarConsulta($checkQuery);
        // $row = $checkResult->fetch_assoc();

        // if ($row['count'] > 0) {
        //     response::error('El nombre del slider ya está registrado');
        //     return;
        // }
        // $url_imagen = $this->subir_archivo($slider_url);
        $query = "INSERT INTO slider (
                    url_imagen, 
                    slider_nombre, 
                    slider_estado
                  ) VALUES (
                    '$slider_url', 
                    '$slider_nombre', 
                    'Activo'
                  )";
                  $result = $conexion->insertar($query);

                  if ($result > 0) {
                      response::success($result, 'Slider insertado correctamente');
                  } else {
                      response::error('Error al insertar el slider');
                  }
              }
          
    public function deleteSlider($id) {
        $conexion = new Conexion();
        $query = "DELETE FROM slider WHERE slider_id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Slider eliminado correctamente');
        } else {
            response::error('Error al eliminar el slider');
        }
    }

    
    public function updateEstado($id,$slider_estado) {
        $conexion = new Conexion();
        $query = "UPDATE slider SET 
                    slider_estado = '$slider_estado'
                WHERE slider_id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Estado actualizado correctamente');
        } else {
            response::error('Error al actualizar el estado');
        }
    }


}
?>
