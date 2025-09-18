
<?php
include_once './utils/response.php';
include_once './config/database.php';

class ServicioController {
	private $database;

	public function __construct() {
		global $database;
		$this->database = $database;
	}

	public function getServicio($id) {
		$conexion = new Conexion();
		$query = "SELECT 
					servicio_id as id,
					servicio_categoria as categoria_id,
					servicio_nombre as nombre,
					servicio_descripcion as descripcion,
					servicio_beneficios as beneficios,
					servicio_precio as precio,
					servicio_facilidades as facilidades,
					servicio_video1 as video1,
					servicio_video2 as video2,
					servicio_info_adicional as info_adicional
				FROM servicio 
				WHERE servicio_id = '$id'";
		$result = $conexion->ejecutarConsulta($query);

		if ($result && $result->num_rows > 0) {
			$servicio = $result->fetch_assoc();
			response::success($servicio, 'Consulta de servicio exitosa');
		} else {
			response::error('No se encontró el servicio');
		}
	}

	public function getServicios() {
		$conexion = new Conexion();
		$query = "SELECT 
					servicio_id as id,
					servicio_categoria as categoria_id,
					servicio_nombre as nombre,
					servicio_descripcion as descripcion,
					servicio_beneficios as beneficios,
					servicio_precio as precio,
					servicio_facilidades as facilidades,
					servicio_video1 as video1,
					servicio_video2 as video2,
					servicio_info_adicional as info_adicional
				FROM servicio";
		$result = $conexion->ejecutarConsulta($query);

		if ($result && $result->num_rows > 0) {
			$servicios = array();
			while ($servicio = $result->fetch_assoc()) {
				$servicios[] = $servicio;
			}
			response::success($servicios, 'Lista de servicios obtenida correctamente');
		} else {
			response::error('No se encontraron servicios registrados');
		}
	}

	public function updateServicio(
		$servicio_id,
		$servicio_categoria,
		$servicio_nombre,
		$servicio_descripcion,
		$servicio_beneficios,
		$servicio_precio,
		$servicio_facilidades,
		$servicio_video1,
		$servicio_video2,
		$servicio_info_adicional
	) {
		$conexion = new Conexion();
		$query = "UPDATE servicio SET 
					servicio_categoria = '$servicio_categoria',
					servicio_nombre = '$servicio_nombre',
					servicio_descripcion = '$servicio_descripcion',
					servicio_beneficios = '$servicio_beneficios',
					servicio_precio = '$servicio_precio',
					servicio_facilidades = '$servicio_facilidades',
					servicio_video1 = '$servicio_video1',
					servicio_video2 = '$servicio_video2',
					servicio_info_adicional = '$servicio_info_adicional'
				WHERE servicio_id = $servicio_id";

		$result = $conexion->save($query);

		if ($result > 0) {
			response::success($result, 'Servicio actualizado correctamente');
		} else {
			response::error('Error al actualizar el servicio');
		}
	}

	public function insertServicio(
		$servicio_categoria,
		$servicio_nombre,
		$servicio_descripcion,
		$servicio_beneficios,
		$servicio_precio,
		$servicio_facilidades,
		$servicio_video1,
		$servicio_video2,
		$servicio_info_adicional
	) {
		$conexion = new Conexion();
		$query = "INSERT INTO servicio (
					servicio_categoria,
					servicio_nombre,
					servicio_descripcion,
					servicio_beneficios,
					servicio_precio,
					servicio_facilidades,
					servicio_video1,
					servicio_video2,
					servicio_info_adicional
				  ) VALUES (
					'$servicio_categoria',
					'$servicio_nombre',
					'$servicio_descripcion',
					'$servicio_beneficios',
					'$servicio_precio',
					'$servicio_facilidades',
					'$servicio_video1',
					'$servicio_video2',
					'$servicio_info_adicional'
				  )";
		$result = $conexion->insertar($query);

		if ($result > 0) {
			response::success($result, 'Servicio insertado correctamente');
		} else {
			response::error('Error al insertar el servicio');
		}
	}

	public function deleteServicio($id) {
		$conexion = new Conexion();
		$query = "DELETE FROM servicio WHERE servicio_id = $id";
		$result = $conexion->save($query);

		if ($result > 0) {
			response::success($result, 'Servicio eliminado correctamente');
		} else {
			response::error('Error al eliminar el servicio');
		}
	}

 // INSERTAR FOTOS SERVICIOS ---------------------------------------------------------
    public function insertFotoServicio($foto_url, $foto_nombre, $foto_orden, $foto_servicioId) {
        $conexion = new Conexion();
        // $foto_fecharegistro = date("Y-m-d");

        $query = "INSERT INTO foto_servicio (
            foto_url,
            foto_nombre,
            foto_orden,
            foto_servicio
        ) VALUES (
            '$foto_url',
            '$foto_nombre',
            '$foto_orden',
            '$foto_servicioId'
        )";

        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Foto insertada correctamente');
        } else {
            response::error('Error al insertar la foto');
        }
    }

 // ------------------------------------------------------
    // Función para almacenar imágenes y generar miniaturas
    public function subirFoto($inmuebleId, $file) {
		$uploadDir = 'uploads/fotos/';
		$allowedTypes = ['jpg', 'jpeg', 'png', 'bmp'];
		$fileName = basename($file['name']);
		$fileSize = $file['size'];
		$fileTmp = $file['tmp_name'];
		$fileType = pathinfo($fileName, PATHINFO_EXTENSION);

		if (!in_array(strtolower($fileType), $allowedTypes)) {
			response::error('Tipo de archivo no permitido');
			return;
		}

		// Generar un nombre de archivo único
		$newFileName = 'servicio' . $inmuebleId . '_' . bin2hex(random_bytes(3)) . '.' . $fileType;
		$uploadFilePath = $uploadDir . $newFileName;

		// Mover el archivo a la carpeta de destino
		if (move_uploaded_file($fileTmp, $uploadFilePath)) {
			// Insertar en la base de datos
			$foto_orden = 1; // Puedes ajustar el orden según tu lógica
			$this->insertFotoServicio($uploadFilePath, $fileName, $foto_orden, $inmuebleId);
		} else {
			response::error('Error al subir el archivo');
		}
	}

	 // ------------------------------------------------------
    public function deleteFotoServicio($foto_id) {
        $conexion = new Conexion();
        $query = "DELETE FROM foto_servicio WHERE foto_id = $foto_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Foto eliminada correctamente');
        } else {
            response::error('Error al eliminar la foto');
        }
    }

    // ------------------------------------------------------
    public function getFotosServicios($servicioId) {
        $conexion = new Conexion();
        $query = "SELECT 
                    foto_id as id,
                    foto_url as url,
                    foto_nombre as nombre,
                    foto_orden as orden,
                    foto_servicio as servicio_id
                FROM foto_servicio
                WHERE foto_servicio = $servicioId";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $fotos = array();
            while ($foto = $result->fetch_assoc()) {
                $fotos[] = $foto;
            }
            response::success($fotos, 'Fotos del servicio obtenidas correctamente');
        } else {
            response::error('No se encontraron fotos para el servicio');
        }
    }

		// Elimina un servicio y todas sus fotos relacionadas
	public function deleteServicioConFotos($servicio_id) {
		$conexion = new Conexion();
		// Eliminar fotos relacionadas
		$queryFotos = "DELETE FROM foto_servicio WHERE foto_servicio = $servicio_id";
		$conexion->save($queryFotos);
		// Eliminar el servicio
		$queryServicio = "DELETE FROM servicio WHERE servicio_id = $servicio_id";
		$result = $conexion->save($queryServicio);

		if ($result > 0) {
			response::success($result, 'Servicio y sus fotos eliminados correctamente');
		} else {
			response::error('Error al eliminar el servicio y sus fotos');
		}
	}

}
?>
