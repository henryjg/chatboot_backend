<?php
include_once './controllers/controllers.php';

ob_start();
header('Content-Type: application/json; charset=utf-8');
// Permite solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite solicitudes con los siguientes métodos HTTP: GET, POST, PUT, DELETE, OPTIONS
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Permite que los encabezados personalizados se incluyan en las solicitudes
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Permite que las cookies se incluyan en las solicitudes
header("Access-Control-Allow-Credentials: true");

 
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit(0);
}

//Obteniendo Parámetros de Petición

$metodoPeticion = $_SERVER['REQUEST_METHOD'];
$tipoContenidoPeticion = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : null;

// ----------------------

// Inicializar variables
$data = array();
$peticion = null;

if ($tipoContenidoPeticion === "application/json") {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    $peticion = $data['op'] ?? null;

} else if ($tipoContenidoPeticion && is_string($tipoContenidoPeticion) &&
    (strpos($tipoContenidoPeticion, "multipart/form-data") !== false ||
    strpos($tipoContenidoPeticion, "application/x-www-form-urlencoded") !== false)) {

    $data = array();
    foreach ($_POST as $key => $value) {
        $data[$key] = cleanField($value);
    }
    // Define la acción basada en el parámetro 'op'
    $peticion = $data['op'] ?? null;

} else {
    // Intentar leer desde POST o GET como fallback
    if (!empty($_POST)) {
        foreach ($_POST as $key => $value) {
            $data[$key] = cleanField($value);
        }
        $peticion = $data['op'] ?? null;
    } else if (!empty($_GET)) {
        foreach ($_GET as $key => $value) {
            $data[$key] = cleanField($value);
        }
        $peticion = $data['op'] ?? null;
    } else {
        // Intentar leer como JSON sin Content-Type
        $jsonData = file_get_contents('php://input');
        if ($jsonData) {
            $decoded = json_decode($jsonData, true);
            if ($decoded !== null) {
                $data = $decoded;
                $peticion = $data['op'] ?? null;
            }
        }
    }
    
    // Si aún no tenemos petición, mostrar error
    if ($peticion === null) {
        Response::error('Tipo de contenido desconocido o falta parámetro "op": ' . $tipoContenidoPeticion);
    }
}

// ----------------------
// Importación de Controladores
// ----------------------

$TrabajadorController   = new TrabajadorController();
$SUNAT                  = new apiSunat();
$ControladorUbigeo      = new ControladorUbigeo();
$EmpresaController      = new EmpresaController();
$ArchivosController     = new ArchivosController();
$NoticiasController     = new NoticiasController();
$ServicioController     = new ServicioController();
$CategoriasController   = new CategoriasController();
$CitasController        = new CitasController();
$PreguntasController    = new PreguntasController();
$EspecialidadController = new EspecialidadController();
$PagosController        = new PagosController();
// $ControladorGenerado    = new ContratoGeneradoController();

// ----------------------
// Procesamiento de la petición
// ----------------------
if ($metodoPeticion === 'POST' ) {
    switch ($peticion) {

      
        case 'get_Empresa':
            echo $EmpresaController->getEmpresa();
            break;
        
        case 'upd_campo':
            $campo     =  $data['Campo'];
            $valor     =  $data['Valor'];

            echo $EmpresaController->updateCampoEmpresa($campo,$valor);
            break;

    //categorias
    case 'getCategorias':
        echo $CategoriasController->getCategorias();
        break;

    case 'getCategoria':
        $id = $data['id'];
        echo $CategoriasController->getCategoria($id);
        break;

    case 'addCategoria':
        $nombre = $data['nombre'];
        echo $CategoriasController->insertCategoria($nombre);
        break;

    case 'updCategoria':
        $id = $data['id'];
        $nombre = $data['nombre'];
        echo $CategoriasController->updateCategoria($id, $nombre);
        break;

    case 'deleteCategoria':
        $id = $data['id'];
        echo $CategoriasController->deleteCategoria($id);
        break;

    // servicios
    case 'getServicios':
        echo $ServicioController->getServicios();
        break;

    case 'getServicio':
        $servicio_id = $data['id'];
        echo $ServicioController->getServicio($servicio_id);
        break;

    case 'addServicio':
        $servicio_categoria = $data['categoria_id'];
        $servicio_nombre = $data['nombre']?? '';
        $servicio_descripcion = $data['descripcion']?? '';
        $servicio_beneficios = $data['beneficios']?? '';
        $servicio_precio = $data['precio']?? '';
        $servicio_facilidades = $data['facilidades']?? '';
        $servicio_video1 = $data['video1']?? '';
        $servicio_video2 = $data['video2']?? '';
        $servicio_info_adicional = $data['info_adicional']?? '';
        echo $ServicioController->insertServicio(
            $servicio_categoria,
            $servicio_nombre,
            $servicio_descripcion,
            $servicio_beneficios,
            $servicio_precio,
            $servicio_facilidades,
            $servicio_video1,
            $servicio_video2,
            $servicio_info_adicional
        );
        break;

        case 'updServicio':
        $servicio_id = $data['id'];
        $servicio_categoria = $data['categoria_id'];
        $servicio_nombre = $data['nombre']?? '';
        $servicio_descripcion = $data['descripcion']?? '';
        $servicio_beneficios = $data['beneficios']?? '';
        $servicio_precio = $data['precio']?? '';
        $servicio_facilidades = $data['facilidades']?? '';
        $servicio_video1 = $data['video1']?? '';
        $servicio_video2 = $data['video2']?? '';
        $servicio_info_adicional = $data['info_adicional']?? '';
        echo $ServicioController->updateServicio(
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
        );
        break;

        case 'deleteServicio':
            $servicio_id = $data['id'];
            echo $ServicioController->deleteServicio($servicio_id);
            break;

        case 'deleteServicioConFotos':
            $servicio_id = $data['id'];
            echo $ServicioController->deleteServicioConFotos($servicio_id);
            break;

         case 'add_fotoServicio':
            $idservicio =  $data['idservicio'];
            $file =        $_FILES['file'];
            echo $ServicioController->subirFoto($idservicio, $file);
            break;

        case 'get_fotos_Servicio':
            $idservicio =  $data['id'];
            echo $ServicioController->getFotosServicios($idservicio);
            break;

        case 'del_foto_Servicio':
            $idservicio =  $data['id'];
            echo $ServicioController->deleteFotoServicio($idservicio);
            break;
     
        // ----------------------
        // Utilitarios
        // ----------------------
        case 'get_ubigeo':
            echo $ControladorUbigeo->get_ubigeo();
            break;

        // ----------------------
        // trabajadores
        // ----------------------

        case 'loggin':
            $usuario =  $data['user'];
            $clave =    $data['pass'];
            echo $TrabajadorController->login($usuario, $clave);
            break;
       
        case 'upd_password':
            $id =    $data['id'];
            $pass =  $data['pass'];
            echo $TrabajadorController->updatePass($id,$pass);
            break;
            
        case 'get_trabajadores':
            echo $TrabajadorController->getTrabajadores();
            break;

        case 'del_trabajador':
            $id =  $data['id'];
            echo $TrabajadorController->updateEstadoTrabajador($id,"Inactivo");
            break;

        case 'delete_trabajador_permanente':
            $id =  $data['id'];
            echo $TrabajadorController->deleteTrabajador($id);
            break;

        case 'upd_trabajadorEstado':
            $id =  $data['id'];
            $estado =  $data['estado'];
            echo $TrabajadorController->updateEstadoTrabajador($id,$estado);
            break;
        
        case 'get_trabajador':
            $id =  $data['id'];
            echo $TrabajadorController->getTrabajador($id);
            break;
        
        case 'get_datos_dni':
            $dni =  $data['dni'];
            echo $SUNAT->get_datos_X_DNI($dni);
            break;
        
        case 'get_datos_ruc':
            $ruc =  $data['ruc'];
            echo $SUNAT->get_datos_X_RUC($ruc);
            break;

        case 'add_trabajador':
            $dni = $data['dni'];
            $nombre = $data['nombre'];
            $apellidos = $data['apellidos'];
            $cargo = $data['cargo']?? null;
            $supervisor = $data['supervisor_id'] ?? null;
            $correo = $data['email'];
            $telefono = $data['celular'];
            $celular = $data['celular'];
            $fnacimiento = $data['fechaNacimiento'];
            $fotoPerfil = $_FILES['fotoPerfil'] ?? null;

            if ($fotoPerfil && $fotoPerfil['error'] === UPLOAD_ERR_OK) {
                // File is valid
            } else {
                $fotoPerfil = null;
            }

            $pass = $data['pass'];
            echo $TrabajadorController->insertTrabajador(
                $dni,
                $nombre,
                $apellidos,
                $correo,
                $cargo,
                $supervisor,
                $telefono,
                $celular,
                $fotoPerfil,
                $fnacimiento,
                $pass
            );
            break;
        
        case 'upd_trabajador':
            $id =  $data['id'];
            $dni =  $data['dni'];
            $nombre =  $data['nombre'];
            $apellidos =  $data['apellidos'];
            $cargo =  $data['cargo'];
            $supervisor = $data['supervisor_id'] ?? null;
            $rol =  $data['rol'] ?? null;
            $correo =  $data['correo'];
            $telefono =  $data['telefono'];
            $celular =  $data['celular'];
            $fnacimiento =  $data['fnacimiento'];
            $fotoPerfil = $_FILES['fotoPerfil'] ?? null;

            if ($fotoPerfil && $fotoPerfil['error'] === UPLOAD_ERR_OK) {
                // File is valid
            } else {
                $fotoPerfil = null;
            }
            
            echo $TrabajadorController->updateTrabajador($id,
                $dni,$nombre,$apellidos,$correo,$cargo,$supervisor,
                $rol, $telefono,
                $celular,$fnacimiento,$fotoPerfil
            );
            break;

        case 'deleteTrabajador':
            $id =  $data['id'];
            echo $TrabajadorController->deleteTrabajador($id);
            break;
        
            case 'Subir_fotografia':
                $fotoImg = $_FILES['archivo_foto'] ?? "";
                $ruta = 'uploads/imagenes/';
                echo $ArchivosController->Subir_Imagen($fotoImg, $ruta);
                break;
    
       
            case 'subir_archivo':
                $archivopdf_file = $_FILES['archivopdf'] ?? null;
                    if ($archivopdf_file) {
                        echo $ArchivosController->subir_archivo_pdf($archivopdf_file);
                        } else {
                            Response::error('No se proporcionó un archivo PDF');
                        }
                        break;
       
        // ----------------------
        // Noticias
        // ----------------------

        case 'listar_noticias':
            echo $NoticiasController->getNoticias();
            break;

        case 'getTotalNoticias':
            echo $NoticiasController->getTotalNoticias();
            break;

        case 'get_noticia':
            $id = $data['id'];
            echo $NoticiasController->getNoticia($id);
            break;
        
        case 'add_noticia':
            $titulo = $data['titulo'];
            $subtitulo = $data['subtitulo'] ?? ''; 
            $url_imagenDestacada = $data['url_ImagenDestacada'] ?? '';
            $curpohtml_box1 = $data['curpohtml_box1'] ?? '';
            $url_imagen2 = $data['url_Imagen2'] ?? '';
            $curpohtml_box2 = $data['curpohtml_box2'] ?? '';
            $url_imagen3 = $data['url_Imagen3'] ?? '';
            $url_video = $data['url_video'] ?? '';
            $seoMetatag = $data['seoMetatag'] ?? '';
            $seoDescripcion = $data['seoDescripcion'] ?? '';
            
            echo $NoticiasController->insertNoticia(
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
            );
            break;
                
        case 'upd_noticia':
            $id = $data['id'];
            $titulo = $data['titulo'];
            $subtitulo = $data['subtitulo'] ?? '';
            $url_imagenDestacada = $data['url_ImagenDestacada'] ?? '';
            $curpohtml_box1 = $data['curpohtml_box1'] ?? '';
            $url_imagen2 = $data['url_Imagen2'] ?? '';
            $curpohtml_box2 = $data['curpohtml_box2'] ?? '';
            $url_imagen3 = $data['url_Imagen3'] ?? '';
            $url_video = $data['url_video'] ?? '';
            $seoMetatag = $data['seoMetatag'] ?? '';
            $seoDescripcion = $data['seoDescripcion'] ?? '';
            $estado = $data['estado'] ?? 'Activo';
            
            echo $NoticiasController->updateNoticia(
                $id,
                $titulo,
                $subtitulo,
                $url_imagenDestacada, 
                $curpohtml_box1,
                $url_imagen2,        
                $curpohtml_box2,
                $url_imagen3,        
                $url_video,
                $seoMetatag,
                $seoDescripcion,
                $estado
            );
            break;
        
        case 'del_noticia':
            $id = $data['id'];
            echo $NoticiasController->deleteNoticia($id);
            break;
        
        case 'upd_Estado_noticia':
            $id = $data['id'];
            $estado = $data['estado'];
            echo $NoticiasController->updateEstado($id, $estado);
            break;

       

    // -------------------------------------------------------------------------------------
    // citas
    // -------------------------------------------------------------------------------------

        case 'getCita':
            $id = $data['id'];
            echo $CitasController->getCita($id);
            break;

        case 'getCita_Con_Horario':
            $id = $data['id'];
            echo $CitasController->getCitaConHorario($id);
            break;

        case 'listar_citas':
            echo $CitasController->getCitas();
            break;

        case 'getCitas_Filtro':
            $dia = $data['dia'] ?? null;
            $mes = $data['mes'] ?? null;
            $anio = $data['anio'] ?? null;
            echo $CitasController->getCitas_Filtro($dia, $mes, $anio);
            break;

        case 'updateCita':
            $citas_id = $data['id'];
            $citas_fecha = $data['fecha'];
            $citas_dni = $data['dni'];
            $citas_nombre = $data['nombre'];
            $cita_celular = $data['celular'] ?? '';
            $citas_procedencia = $data['procedencia'] ?? '';
            $citas_descripcion = $data['descripcion'] ?? '';
            $citas_precio = $data['precio'] ?? 0;
            $citas_estado = $data['estado'] ?? '';
            $citas_consultorio = $data['consultorio'] ?? '';
            $cita_preciogeneral = $data['preciogeneral'] ?? 0;
            $cita_preciofinal = $data['preciofinal'] ?? 0;
            $horario_id = $data['horario_id'] ?? null;
            $especialidad_id = $data['especialidad_id'] ?? null;
            $modalidad = $data['modalidad'] ?? null;
            
            echo $CitasController->updateCita(
                $citas_id,
                $citas_fecha,
                $citas_dni,
                $citas_nombre,
                $cita_celular,
                $citas_procedencia,
                $citas_descripcion,
                $citas_precio,
                $citas_estado,
                $citas_consultorio,
                $cita_preciogeneral,
                $cita_preciofinal,
                $horario_id,
                $especialidad_id,
                $modalidad_id
            );
            break;

        case 'add_cita':
            $citas_fecha = $data['fecha'];
            $citas_dni = $data['dni'];
            $citas_nombre = $data['nombre'];
            $cita_celular = $data['celular'] ?? '';
            $citas_procedencia = $data['procedencia'] ?? '';
            $citas_descripcion = $data['descripcion'] ?? '';
            $citas_precio = $data['precio'] ?? 0;
            $citas_consultorio = $data['consultorio'] ?? '';
            $cita_preciogeneral = $data['preciogeneral'] ?? 0;
            $cita_preciofinal = $data['preciofinal'] ?? 0;
            $horario_id = $data['horario_id'];
            $especialidad_id = $data['especialidad_id'];
            $modalidad = $data['modalidad'] ?? '';
            
            echo $CitasController->insertCita(
                $citas_fecha,
                $citas_dni,
                $citas_nombre,
                $cita_celular,
                $citas_procedencia,
                $citas_descripcion,
                $citas_precio,
                $citas_consultorio,
                $cita_preciogeneral,
                $cita_preciofinal,
                $horario_id,
                $especialidad_id,
                $modalidad
            );
            break;

        case 'deleteCita':
            $cita_id = $data['id'];
            echo $CitasController->deleteCita($cita_id);
            break;

        case 'updateEstadoCita':
            $cita_id = $data['id'];
            $citas_estado = $data['estado'];
            echo $CitasController->updateEstado($cita_id, $citas_estado);
            break;

        // Endpoints específicos para el chatbot y disponibilidad de horarios
        case 'getHorariosDisponibles':
            $fecha = $data['fecha'];
            $especialidad_id = $data['especialidad_id'] ?? null;
            echo $CitasController->getHorariosDisponibles($fecha, $especialidad_id);
            break;

        case 'getCitasPorFecha':
            $fecha = $data['fecha'];
            echo $CitasController->getCitasPorFecha($fecha);
            break;

        case 'asignarHorarioCita':
            $cita_id = $data['cita_id'];
            $horario_id = $data['horario_id'];
            echo $CitasController->asignarHorarioCita($cita_id, $horario_id);
            break;

        case 'getCitasPorEspecialidad':
            $especialidad_id = $data['id'];
            echo $CitasController->getCitasPorEspecialidad($especialidad_id);
            break;

        case 'getCitasPorFechaYEspecialidad':
            $fecha = $data['fecha'];
            $especialidad_id = $data['id'];
            echo $CitasController->getCitasPorFechaYEspecialidad($fecha, $especialidad_id);
            break;

        case 'getHorariosPorEspecialidadYFecha':
            $fecha = $data['fecha'];
            $especialidad_id = $data['id'] ?? null;
            echo $CitasController->getHorariosPorEspecialidadYFecha($fecha, $especialidad_id);
            break;

    // -------------------------------------------------------------------------------------
    // pagos
    // -------------------------------------------------------------------------------------

        case 'getPago':
            $id = $data['id'];
            echo $PagosController->getPago($id);
            break;

        case 'listar_pagos':
            echo $PagosController->getPagos();
            break;

        case 'updatePago':
            $pago_id = $data['id'];
            $pago_tipo = $data['tipo'];
            $pago_monto = $data['monto'];
            $pago_comentario = $data['comentario'] ?? '';
            $pago_url = $data['url'] ?? '';
            $pago_citaid = $data['cita_id'];
            echo $PagosController->updatePago(
                $pago_id,
                $pago_tipo,
                $pago_monto,
                $pago_comentario,
                $pago_url,
                $pago_citaid
            );
            break;

        case 'add_pago':
            $pago_tipo = $data['tipo'];
            $pago_monto = $data['monto'];
            $pago_comentario = $data['comentario'] ?? '';
            $pago_url = $data['url'] ?? '';
            $pago_citaid = $data['cita_id'];
            echo $PagosController->insertPago(
                $pago_tipo,
                $pago_monto,
                $pago_comentario,
                $pago_url,
                $pago_citaid
            );
            break;

        case 'deletePago':
            $pago_id = $data['id'];
            echo $PagosController->deletePago($pago_id);
            break;

        case 'getPagosPorCita':
            $cita_id = $data['cita_id'];
            echo $PagosController->getPagosPorCita($cita_id);
            break;

        case 'getTotalPagosPorCita':
            $cita_id = $data['cita_id'];
            echo $PagosController->getTotalPagosPorCita($cita_id);
            break;

        case 'getPagosPorTipo':
            $tipo = $data['tipo'];
            echo $PagosController->getPagosPorTipo($tipo);
            break;

    // -------------------------------------------------------------------------------------
    // preguntas frecuentes
    // -------------------------------------------------------------------------------------

        case 'getPregunta':
            $id = $data['id'];
            echo $PreguntasController->getPregunta($id);
            break;

        case 'listar_preguntas':
            echo $PreguntasController->getPreguntas();
            break;

    // este es el endpoint
        case 'add_pregunta':
            $descripcion = $data['descripcion'];
            $respuesta = $data['respuesta'];
            $info_adicional = $data['info_adicional'] ?? '';
            $pf_video_url = $data['video_url'] ?? '';
            $pf_imagen_url = $data['imagen_url'] ?? '';
            echo $PreguntasController->insertPregunta(
                $descripcion,
                $respuesta,
                $info_adicional,
                $pf_video_url,
                $pf_imagen_url
            );
            break;

        case 'updatePregunta':
            $id = $data['id'];
            $descripcion = $data['descripcion'];
            $respuesta = $data['respuesta'];
            $info_adicional = $data['info_adicional'] ?? '';
            $pf_video_url = $data['video_url'] ?? '';
            $pf_imagen_url = $data['imagen_url'] ?? '';
            echo $PreguntasController->updatePregunta(
                $id,
                $descripcion,
                $respuesta,
                $info_adicional,
                $pf_video_url,
                $pf_imagen_url
            );
            break;

        case 'deletePregunta':
            $id = $data['id'];
            echo $PreguntasController->deletePregunta($id);
            break;

        case 'buscarPreguntas':
            $termino = $data['termino'];
            echo $PreguntasController->buscarPreguntas($termino);
            break;

    // -------------------------------------------------------------------------------------
    // especialidades
    // -------------------------------------------------------------------------------------

        case 'getEspecialidad':
            $id = $data['id'];
            echo $EspecialidadController->getEspecialidad($id);
            break;

        case 'listar_especialidades':
            echo $EspecialidadController->getEspecialidades();
            break;

        case 'add_especialidad':
            $nombre = $data['nombre'];
            $descripcion = $data['descripcion'] ?? '';
            $url = $data['url'] ?? '';
            echo $EspecialidadController->insertEspecialidad(
                $nombre,
                $descripcion,
                $url
            );
            break;

        case 'updateEspecialidad':
            $id = $data['id'];
            $nombre = $data['nombre'];
            $descripcion = $data['descripcion'] ?? '';
            $url = $data['url'] ?? '';
            echo $EspecialidadController->updateEspecialidad(
                $id,
                $nombre,
                $descripcion,
                $url
            );
            break;

        case 'deleteEspecialidad':
            $id = $data['id'];
            echo $EspecialidadController->deleteEspecialidad($id);
            break;

        case 'buscarEspecialidades':
            $termino = $data['termino'];
            echo $EspecialidadController->buscarEspecialidades($termino);
            break;

        default:
            Response::error('Petición no autorizada');
            break;
    }
} else {
    Response::error('Envío de datos inválido');
}


function cleanField($value) {
    if (is_array($value)) {
        return array_map('cleanField', $value);
    } else {
        $value = trim($value);
        // $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
        // Valida si es un número cuando corresponde
        if (is_numeric($value)) {
            return $value + 0; // Devuelve como int o float
        }
        return $value;
    }
}