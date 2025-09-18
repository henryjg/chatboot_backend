<?php
include_once './utils/response.php';
include_once './config/database.php';

class LandingWebController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    // Obtener información de la landing web
    public function getLandingWeb($landing_id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    lw_nombre_pagina as nombrePagina,
                    lw_nombre_corto as nombreCorto,
                    lw_imagen_destacada_url as url,
                    lw_metatag as metatag,
                    lw_celular1 as celular1,
                    lw_celular2 as celular2,
                    lw_direccion as direccion,
                    lw_email as email,
                    lw_fb as facebook,
                    lw_ig as instagram,
                    lw_yt as youtube,
                    lw_seccion1_titulo as s1Titulo,
                    lw_seccion1_slider as s1Slider,
                    lw_seccion2_titulo as s2Titulo,
                    lw_seccion2_subtitulo as s2Subtitulo,
                    lw_seccion2_descripcion as s2Descripcion,
                    lw_seccion3_titulo as s3Titulo,
                    lw_secion3_loteCant as s3LoteCant,
                    lw_seccion3_loteDimen as s3LoteDimen,
                    lw_seccion3_lotePrecios as s3LotePrecios,
                    lw_seccion4_titulo as s4Titulo,
                    lw_secion4_sub as s4Subtitulo,
                    lw_seccion4_desc as s4Descuento,
                    lw_seccion5_titulo as s5Titulo,
                    lw_seccion5_sub as s5Subtitulo,
                    lw_seccion5_des as s5Descuento,
                    lw_seccion6_ubicacion as s6Ubicacion,
                    lw_seccion6_img as s6Imagen,
                    lw_seccion7_nosotros as s7Nosotros
                FROM landing_web 
                WHERE landing_id = '$landing_id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $landing = $result->fetch_assoc();
            response::success($landing, 'Consulta de landing web exitosa');
        } else {
            response::error('No se encontró la landing web');
        }
    }

    public function getLandingsWeb() {
        $conexion = new Conexion();
        $query = "SELECT 
                    landing_id as id,
                    lw_nombre_pagina as nombrePagina,
                    lw_nombre_corto as nombreCorto,
                    lw_imagen_destacada_url as url,
                    lw_metatag as metatag,
                    lw_celular1 as celular1,
                    lw_celular2 as celular2,
                    lw_direccion as direccion,
                    lw_email as email,
                    lw_fb as facebook,
                    lw_ig as instagram,
                    lw_yt as youtube,
                    lw_seccion1_titulo as s1Titulo,
                    lw_seccion1_slider as s1Slider,
                    lw_seccion2_titulo as s2Titulo,
                    lw_seccion2_subtitulo as s2Subtitulo,
                    lw_seccion2_descripcion as s2Descripcion,
                    lw_seccion3_titulo as s3Titulo,
                    lw_secion3_loteCant as s3LoteCant,
                    lw_seccion3_loteDimen as s3LoteDimen,
                    lw_seccion3_lotePrecios as s3LotePrecios,
                    lw_seccion4_titulo as s4Titulo,
                    lw_secion4_sub as s4Subtitulo,
                    lw_seccion4_desc as s4Descuento,
                    lw_seccion5_titulo as s5Titulo,
                    lw_seccion5_sub as s5Subtitulo,
                    lw_seccion5_des as s5Descuento,
                    lw_seccion6_ubicacion as s6Ubicacion,
                    lw_seccion6_img as s6Imagen,
                    lw_seccion7_nosotros as s7Nosotros
                FROM landing_web";
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

    // Actualizar campos específicos de la landing web
    public function updateCampoLandingWeb($campo, $valor, $landing_id) {
        $nombreCampo = "lw_".$campo;
        $conexion = new Conexion();
        $query = "UPDATE landing_web SET ".$nombreCampo." = '$valor' 
                  WHERE landing_id = '$landing_id'";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Landing web actualizada correctamente');
        } else {
            response::error('Error al actualizar la landing web');
        }
    }

    // Insertar nueva landing web
    public function insertLandingWeb(
        $lw_nombre_pagina, 
        $lw_nombre_corto, 
        $lw_imagen_destacada_url,
        $lw_metatag,
        $lw_celular1, 
        $lw_celular2, 
        $lw_direccion, 
        $lw_email, 
        $lw_fb, 
        $lw_ig, 
        $lw_yt, 
        $lw_seccion1_titulo,
        $lw_seccion1_slider,
        $lw_seccion2_titulo,
        $lw_seccion2_subtitulo,
        $lw_seccion2_descripcion,
        $lw_seccion3_titulo,
        $lw_secion3_loteCant,
        $lw_seccion3_loteDimen,
        $lw_seccion3_lotePrecios,
        $lw_seccion4_titulo,
        $lw_secion4_sub,
        $lw_seccion4_desc,
        $lw_seccion5_titulo,
        $lw_seccion5_sub,
        $lw_seccion5_des,
        $lw_seccion6_ubicacion,
        $lw_seccion6_img,
        $lw_seccion7_nosotros
    ) {
        $conexion = new Conexion();
        $query = "INSERT INTO landing_web (
                    lw_nombre_pagina, 
                    lw_nombre_corto, 
                    lw_imagen_destacada_url,
                    lw_metatag,
                    lw_celular1, 
                    lw_celular2, 
                    lw_direccion, 
                    lw_email, 
                    lw_fb, 
                    lw_ig, 
                    lw_yt, 
                    lw_seccion1_titulo,
                    lw_seccion1_slider,
                    lw_seccion2_titulo,
                    lw_seccion2_subtitulo,
                    lw_seccion2_descripcion,
                    lw_seccion3_titulo,
                    lw_secion3_loteCant,
                    lw_seccion3_loteDimen,
                    lw_seccion3_lotePrecios,
                    lw_seccion4_titulo,
                    lw_secion4_sub,
                    lw_seccion4_desc,
                    lw_seccion5_titulo,
                    lw_seccion5_sub,
                    lw_seccion5_des,
                    lw_seccion6_ubicacion,
                    lw_seccion6_img,
                    lw_seccion7_nosotros
                  ) VALUES (
                    '$lw_nombre_pagina', 
                    '$lw_nombre_corto', 
                    '$lw_imagen_destacada_url',
                    '$lw_metatag',
                    '$lw_celular1', 
                    '$lw_celular2', 
                    '$lw_direccion', 
                    '$lw_email', 
                    '$lw_fb', 
                    '$lw_ig', 
                    '$lw_yt', 
                    '$lw_seccion1_titulo',
                    '$lw_seccion1_slider',
                    '$lw_seccion2_titulo',
                    '$lw_seccion2_subtitulo',
                    '$lw_seccion2_descripcion',
                    '$lw_seccion3_titulo',
                    '$lw_secion3_loteCant',
                    '$lw_seccion3_loteDimen',
                    '$lw_seccion3_lotePrecios',
                    '$lw_seccion4_titulo',
                    '$lw_secion4_sub',
                    '$lw_seccion4_desc',
                    '$lw_seccion5_titulo',
                    '$lw_seccion5_sub',
                    '$lw_seccion5_des',
                    '$lw_seccion6_ubicacion',
                    '$lw_seccion6_img',
                    '$lw_seccion7_nosotros'
                  )";

        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Landing web insertada correctamente');
        } else {
            response::error('Error al insertar la landing web');
        }
    }

    public function deleteLandingWeb($landing_id) {
        $conexion = new Conexion();
        $query = "DELETE FROM landing_web WHERE landing_id = $landing_id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Landing eliminado correctamente');
        } else {
            response::error('Error al eliminar el landing');
        }
    }
}
?>
