<?php
include_once './utils/response.php';
include_once './config/database.php';

class EmpresaController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    // ------------------------------------------------------
    public function getEmpresa() {
        $conexion = new Conexion();
        $query = "SELECT 
                    emp_nombrecorto as nombreCorto,
                    emp_razonsocial as razonSocial,
                    emp_ruc as ruc,
                    emp_gerente as gerente,
                    emp_nosotros as nosotros,
                    emp_mision as mision,
                    emp_vision as vision,
                    emp_valores as valores,
                    emp_politicasprivacidad as politicasPrivacidad,
                    emp_celular as celular,
                    emp_celular2 as celular2,
                    emp_direccion as direccion,
                    emp_email as email,
                    emp_contacto as contacto,
                    emp_slogan as slogan,
                    emp_titulopagina as tituloPagina,
                    emp_metatag as metaTag,
                    emp_facebook as facebook,
                    emp_instragram as instragram,
                    emp_youtube as youtube,
                    emp_pixel as pixel,
                    emp_imagendestacadaUrl as imagendestacadaUrl,
                    emp_terminos as terminos,
                    emp_logo as logo,
                    emp_portada as portada
                FROM empresa 
                WHERE emp_id = '1'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $empresa = $result->fetch_assoc();
            response::success($empresa, 'Consulta de empresa exitosa');
        } else {
            response::error('No se encontró la empresa');
        }
    }

    // ------------------------------------------------------
    public function updateCampoEmpresa($campo,$valor) {
        $nombreCampo = "emp_".$campo;
        $conexion = new Conexion();
        $query = "UPDATE empresa SET ".$nombreCampo." = '$valor'
                WHERE emp_id = 1";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Empresa actualizada correctamente');
        } else {
            response::error('Error al actualizar la empresa');
        }
    }

    // ------------------------------------------------------
    public function insertServicio(
        $ser_descripcion,
        $ser_foto
    ) {
        $conexion = new Conexion();

        $ser_foto_url = $this->subir_archivo($ser_foto);

        $query = "INSERT INTO servicio (
            ser_descripcion,
            ser_foto,
            ser_estado
        ) VALUES (
            '$ser_descripcion',
            '$ser_foto_url',
            'Activo'
        )";

        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Servicio insertado correctamente');
        } else {
            response::error('Error al insertar el servicio');
        }
    }

    // ------------------------------------------------------
    public function getServicio($ser_id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    ser_id as id,
                    ser_descripcion as descripcion,
                    CASE WHEN ser_foto = '' THEN 'uploads/servicio/default.png' ELSE ser_foto END AS foto,
                    ser_estado as estado
                FROM servicio 
                WHERE ser_id = $ser_id";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $servicio = $result->fetch_assoc();
            response::success($servicio, 'Consulta de servicio exitosa');
        } else {
            response::error('No se encontró el servicio');
        }
    }

    // ------------------------------------------------------
    public function getServicios() {
        $conexion = new Conexion();
        $query = "SELECT
                    ser_id as id,
                    ser_descripcion as descripcion,
                    CASE WHEN ser_foto = '' THEN 'uploads/servicio/default.png' ELSE ser_foto END AS foto,
                    ser_estado as estado
                FROM servicio WHERE ser_estado='Activo'";
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

    // ------------------------------------------------------
    public function updateServicio(
        $ser_id,
        $ser_descripcion,
        $ser_foto
    ) {
        $ser_foto_url = $this->subir_archivo($ser_foto);
        $conexion = new Conexion();
        $query = "UPDATE servicio SET 
                    ser_descripcion = '$ser_descripcion',
                    ser_foto = '$ser_foto_url'
                WHERE ser_id = $ser_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Servicio actualizado correctamente');
        } else {
            response::error('Error al actualizar el servicio');
        }
    }

    // ------------------------------------------------------
    public function updateEstado_Servicio($ser_id, $ser_estado) {
        $conexion = new Conexion();
        $query = "UPDATE servicio SET 
                    ser_estado = '$ser_estado'
                WHERE ser_id = $ser_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Estado del servicio actualizado correctamente');
        } else {
            response::error('Error al actualizar el estado del servicio');
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
            $ruta="./uploads/servicio/";
            if (!file_exists($ruta)) {
                mkdir($ruta);
            }

            $nuevo_nombre =  "servicio_".rand(1000000, 9999999);
            $nuevo_nombre_completo = $nuevo_nombre . '.' . $this->detecta_extension(basename($imgFile['name']));
            $uploadfile = $ruta . $nuevo_nombre_completo;
            $ruta_archivo = $ruta . $nuevo_nombre_completo;

            $restriccionLogo = "NOPERMITIDO";

            $permitidos = array("image/bmp", "image/jpg", "image/jpeg", "image/png");
            if (in_array($imgFile['type'], $permitidos)) {
                $restriccionLogo = "PERMITIDO";

                if (move_uploaded_file($imgFile['tmp_name'], $uploadfile)) {
                    switch ($imgFile['type']) {
                        case 'image/bmp':
                            $imagen = imagecreatefrombmp($uploadfile);
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
                        $ancho_original = imagesx($imagen);
                        $alto_original = imagesy($imagen);

                        $lado_corto = min($ancho_original, $alto_original);

                        $x = ($ancho_original - $lado_corto) / 2;
                        $y = ($alto_original - $lado_corto) / 2;

                        $imagen_cuadrada = imagecrop($imagen, ['x' => $x, 'y' => $y, 'width' => $lado_corto, 'height' => $lado_corto]);

                        if ($imagen_cuadrada !== false) {
                            imagejpeg($imagen_cuadrada, $uploadfile);
                            imagedestroy($imagen_cuadrada);
                        }
                        imagedestroy($imagen);
                    }
                }
            }

        } else {
            $ruta_archivo = "";
        }
        return $ruta_archivo;
    }
}
?>
