<?php
include_once './utils/response.php';
include_once './config/database.php';

class NoticiasController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    // Obtener una noticia por ID
    public function getNoticia($id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    id,
                    titulo,
                    subtitulo,
                    url_ImagenDestacada as url_ImagenDestacada,
                    curpohtml_box1,
                    url_Imagen2 as url_Imagen2,
                    curpohtml_box2,
                    url_Imagen3 as url_Imagen3,
                    url_video,
                    seoMetatag,
                    seoDescripcion,
                    fechaRegistro,
                    estado
                FROM noticias 
                WHERE id = '$id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $noticia = $result->fetch_assoc();
            response::success($noticia, 'Noticia encontrada correctamente');
        } else {
            response::error('No se encontró la noticia');
        }
    }

    // Listar todas las noticias
    public function getNoticias() {
        $conexion = new Conexion();
        $query = "SELECT 
                    id,
                    titulo,
                    subtitulo,
                    url_ImagenDestacada as url_ImagenDestacada,
                    curpohtml_box1,
                    url_Imagen2 as url_Imagen2,
                    curpohtml_box2,
                    url_Imagen3 as url_Imagen3,
                    url_video,
                    seoMetatag,
                    seoDescripcion,
                    fechaRegistro,
                    estado
                FROM noticias";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $noticias = array();
            while ($noticia = $result->fetch_assoc()) {
                $noticias[] = $noticia;
            }
            response::success($noticias, 'Lista de noticias obtenida correctamente');
        } else {
            response::error('No se encontraron noticias registradas');
        }
    }

    // Devuelve el número total de noticias
        public function getTotalNoticias() {
            $conexion = new Conexion();
            $query = "SELECT COUNT(*) as total FROM noticias";
            $result = $conexion->ejecutarConsulta($query);
            if ($result && $row = $result->fetch_assoc()) {
                response::success($row['total'], 'Total de noticias obtenido correctamente');
            } else {
                response::error('No se pudo obtener el total de noticias');
            }
        }

    // Insertar una nueva noticia
    public function insertNoticia(
        $titulo,
        $subtitulo,
        $url_imagenDestacada,
        $curpohtml_box1,
        $url_imagen2,
        $curpohtml_box2,
        $url_imagen3,
        $url_video,
        $seoMetatag,
        $seoDescripcion
    ) {
        $conexion = new Conexion();

        // Subir las imágenes si se proporcionan
        $url_imagenDestacada_path = $url_imagenDestacada;
        $url_imagen2_path = $url_imagen2;
        $url_imagen3_path = $url_imagen3;
        // Validaciones básicas
        if (empty($titulo)) {
            response::error('El título es obligatorio');
            return;
        }
        if (empty($url_imagenDestacada_path) && $url_imagenDestacada) {
            response::error('Error al subir la imagen destacada');
            return;
        }

        $query = "INSERT INTO noticias (
                    titulo, 
                    subtitulo, 
                    url_ImagenDestacada, 
                    curpohtml_box1, 
                    url_Imagen2, 
                    curpohtml_box2, 
                    url_Imagen3, 
                    url_video, 
                    seoMetatag, 
                    seoDescripcion, 
                    fechaRegistro, 
                    estado
                  ) VALUES (
                    '$titulo', 
                    '$subtitulo', 
                    '$url_imagenDestacada_path', 
                    '$curpohtml_box1', 
                    '$url_imagen2_path', 
                    '$curpohtml_box2', 
                    '$url_imagen3_path', 
                    '$url_video', 
                    '$seoMetatag', 
                    '$seoDescripcion', 
                    NOW(), 
                    'Activo'
                  )";

        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Noticia insertada correctamente');
        } else {
            response::error('Error al insertar la noticia');
        }
    }

    // Actualizar una noticia existente
    public function updateNoticia(
        $id,
        $titulo,
        $subtitulo,
        $url_imagenDestacada,  // URL string
        $curpohtml_box1,
        $url_imagen2,         // URL string
        $curpohtml_box2,
        $url_imagen3,         // URL string
        $url_video,
        $seoMetatag,
        $seoDescripcion,
        $estado
    ) {
        $conexion = new Conexion();

        // Para actualizar, si no se envía una nueva URL, mantener la existente
        $query = "SELECT url_ImagenDestacada, url_Imagen2, url_Imagen3 FROM noticias WHERE id = '$id'";
        $result = $conexion->ejecutarConsulta($query);
        $noticia = $result->fetch_assoc();

        // Si se envía una nueva URL, usarla; si no, mantener la existente
        $url_imagenDestacada_path = !empty($url_imagenDestacada) ? $url_imagenDestacada : $noticia['url_ImagenDestacada'];
        $url_imagen2_path = !empty($url_imagen2) ? $url_imagen2 : $noticia['url_Imagen2'];
        $url_imagen3_path = !empty($url_imagen3) ? $url_imagen3 : $noticia['url_Imagen3'];

        $query = "UPDATE noticias SET 
                    titulo = '$titulo',
                    subtitulo = '$subtitulo',
                    url_ImagenDestacada = '$url_imagenDestacada_path',
                    curpohtml_box1 = '$curpohtml_box1',
                    url_Imagen2 = '$url_imagen2_path',
                    curpohtml_box2 = '$curpohtml_box2',
                    url_Imagen3 = '$url_imagen3_path',
                    url_video = '$url_video',
                    seoMetatag = '$seoMetatag',
                    seoDescripcion = '$seoDescripcion',
                    estado = '$estado'
                WHERE id = $id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Noticia actualizada correctamente');
        } else {
            response::error('Error al actualizar la noticia');
        }
    }

    // Eliminar una noticia
    public function deleteNoticia($id) {
        $conexion = new Conexion();
        $query = "DELETE FROM noticias WHERE id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Noticia eliminada correctamente');
        } else {
            response::error('Error al eliminar la noticia');
        }
    }

    // Actualizar el estado de una noticia
    public function updateEstado($id, $estado) {
        $conexion = new Conexion();
        $query = "UPDATE noticias SET 
                    estado = '$estado'
                WHERE id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Estado actualizado correctamente');
        } else {
            response::error('Error al actualizar el estado');
        }
    }

    // Métodos auxiliares para manejar la subida de archivos (igual que en el ejemplo)
    public function detecta_extension($mi_extension) {
        $ext = explode(".", $mi_extension);
        return end($ext);
    }

    public function subir_archivo($imgFile) {
        if ($imgFile && $imgFile['error'] === UPLOAD_ERR_OK) {
            $ruta = "./uploads/noticias/";
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, true);
            }

            $nuevo_nombre = "foto_" . rand(1000000, 9999999);
            $nuevo_nombre_completo = $nuevo_nombre . '.' . $this->detecta_extension(basename($imgFile['name']));
            $uploadfile = $ruta . $nuevo_nombre_completo;
            $ruta_archivo = "uploads/noticias/" . $nuevo_nombre_completo;

            $restriccionLogo = "NOPERMITIDO";

            // Validamos Tipo
            $permitidos = array("image/bmp", "image/jpg", "image/jpeg", "image/png");
            if (in_array($imgFile['type'], $permitidos)) {
                $restriccionLogo = "PERMITIDO";

                if (move_uploaded_file($imgFile['tmp_name'], $uploadfile)) {
                    // Abrir la imagen original
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
                        // Obtener dimensiones de la imagen
                        $ancho_original = imagesx($imagen);
                        $alto_original = imagesy($imagen);

                        // Determinar el lado más corto para el recorte
                        $lado_corto = min($ancho_original, $alto_original);

                        // Coordenadas para recortar centrado
                        $x = ($ancho_original - $lado_corto) / 2;
                        $y = ($alto_original - $lado_corto) / 2;

                        // Crear una imagen cuadrada recortada
                        $imagen_cuadrada = imagecrop($imagen, ['x' => $x, 'y' => $y, 'width' => $lado_corto, 'height' => $lado_corto]);

                        if ($imagen_cuadrada !== false) {
                            // Guardar la imagen recortada
                            imagejpeg($imagen_cuadrada, $uploadfile);
                            imagedestroy($imagen_cuadrada);
                        }
                        imagedestroy($imagen);
                    }
                }
            }
            return $ruta_archivo;
        } else {
            return "";
        }
    }
}
?>