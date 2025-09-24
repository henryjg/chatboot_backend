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
    // Tipo de contenido desconocido
    Response::error('Tipo de contenido desconocido: ' . $tipoContenidoPeticion);
}

// ----------------------
// Importación de Controladores
// ----------------------

$WebController          = new WebController();
$TrabajadorController   = new TrabajadorController();

$SUNAT                  = new apiSunat();
$ControladorUbigeo      = new ControladorUbigeo();
$EmpresaController      = new EmpresaController();
$LandingWeb             = new LandingWebController();
$LandingFotos           = new LandingFotosController();
$ArchivosController     = new ArchivosController();
$SliderController       = new SliderController();
$NoticiasController     = new NoticiasController();
$DocumentosController   = new DocumentosController();
$ServicioController     = new ServicioController();
$CategoriasController   = new CategoriasController();
$CitasController        = new CitasController();
$PagosController        = new PagosController();
$PreguntasController    = new PreguntasController();
// $ControladorGenerado    = new ContratoGeneradoController();

// ----------------------
// Procesamiento de la petición
// ----------------------
if ($metodoPeticion === 'POST') {
    switch ($peticion) {

      
        // ----------------------
        // DESDE PORTAL WEB 
        // ----------------------
        case 'add_LeadContacto':
            $cliente_numerodoc      =  $data['dni'];
            $cliente_nombres        =  $data['nombres'];
            $cliente_apellidos      =  $data['apellidos'];
            $cliente_celular        =  $data['celular'];
            $cliente_comentario     =  $data['asunto'];
            $inmuebleId             =  $data['inmuebleId'];
       
            echo $ClienteController->insertCliente_ComoLead(
                $cliente_numerodoc,
                $cliente_nombres,
                $cliente_apellidos,
                $cliente_celular,
                $cliente_comentario,
                $inmuebleId 
            );
            break;
        case 'add_FormContacto':
            $nombre     =  $data['nombre'];
            $apellidos  =  $data['apellidos'];
            $asunto     =  $data['asunto'];
            $correo     =  $data['correo'];
            $celular    =  $data['celular'];            
            $mensaje    =  $data['mensaje'];

       
            echo $WebController->insert_FormContact(
                $nombre,
                $apellidos,
                $asunto,
                $celular,
                $correo,
                $mensaje
            );
            break;

        case 'getFormContact':
            echo $WebController->get_FormContactos();
            break;

        case 'del_contacto':
            $contacto_id = $data['id'];
            echo $WebController->delete_FormContact($contacto_id);
            break;

        case 'upd_contacto':
            $contacto_id = $data['id'];
            $nombre     =  $data['nombre'];
            $apellidos  =  $data['apellidos'];
            $asunto     =  $data['asunto'];
            $correo     =  $data['correo'];
            $celular    =  $data['celular'];            
            $mensaje    =  $data['mensaje'];
            $estado     = $data['estado'];

            echo $WebController->update_FormContact(
                $contacto_id,
                $nombre,
                $apellidos,
                $asunto,
                $celular,
                $correo,
                $mensaje,
                $estado
            );
            break;

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
        // ------------------------------------------------
                // Landing
        // ------------------------------------------------

        case 'getFotos':
            echo $LandingFotos->getFotosLanding();
            break;

        case 'addFotos':
            $foto_url = cleanfild($data['url']);
            $foto_seccion = cleanfild($data['seccion']);
            $foto_miniatura = cleanfild($data['miniatura']);
            $landing_id = cleanfild($data['id']);
            echo $LandingFotos->insertFotoLanding(
                $foto_url,$foto_seccion,$foto_miniatura,$landing_id
            );
            break;

        case 'deleteFotos':
            $fotos_id = cleanfild($data['id']);
            echo $LandingFotos->deleteFotoLanding($fotos_id);
            break;

        case 'updFotos':
            $fotos_id = cleanfild($data['id']);
            $foto_url = cleanfild($data['url']);
            $foto_seccion = cleanfild($data['seccion']);
            $foto_miniatura = cleanfild($data['miniatura']);
            $landing_id = cleanfild($data['landingId']);
            echo $LandingFotos->updateFotoLanding(
                $fotos_id,$foto_url,$foto_seccion,$foto_miniatura,$landing_id
            );
            break;
// ---------------------------------------------------------------------------------------------------
        case 'getLW':
            echo $LandingWeb->getLandingsWeb();
            break;

        case 'addLW':
        $lw_nombre_pagina = cleanfild($data['nombrePagina']);
        $lw_nombre_corto = cleanfild($data['nombreCorto']);
        $lw_imagen_destacada_url = cleanfild($data['urlDestacada']);
        $lw_metatag = cleanfild($data['metatag']);
        $lw_celular1 = cleanfild($data['celular1']);
        $lw_celular2 = cleanfild($data['celular2']);
        $lw_direccion = cleanfild($data['direccion']);
        $lw_email = cleanfild($data['email']);
        $lw_fb = cleanfild($data['facebook']);
        $lw_ig = cleanfild($data['instagram']);
        $lw_yt = cleanfild($data['youtube']);
        $lw_seccion1_titulo = cleanfild($data['s1Titulo']);
        $lw_seccion1_slider = cleanfild($data['s1Slider']);
        $lw_seccion2_titulo = cleanfild($data['s2Titulo']);
        $lw_seccion2_subtitulo = cleanfild($data['s2Subtitulo']);
        $lw_seccion2_descripcion = cleanfild($data['s2Descripcion']);
        $lw_seccion3_titulo = cleanfild($data['s3Titulo']);
        $lw_secion3_loteCant = cleanfild($data['s3LoteCant']);
        $lw_seccion3_loteDimen = cleanfild($data['s3LoteDime']);
        $lw_seccion3_lotePrecios = cleanfild($data['s3LotePrecios']);
        $lw_seccion4_titulo = cleanfild($data['s4Titulo']);
        $lw_secion4_sub = cleanfild($data['s4Subtitulo']);
        $lw_seccion4_desc = cleanfild($data['s4Descripcion']);
        $lw_seccion5_titulo = cleanfild($data['s5Titulo']);
        $lw_seccion5_sub = cleanfild($data['s5Subtitulo']);
        $lw_seccion5_des = cleanfild($data['s5Descripcion']);
        $lw_seccion6_ubicacion = cleanfild($data['s6Ubicacion']);
        $lw_seccion6_img = cleanfild($data['imagen']);
        $lw_seccion7_nosotros = cleanfild($data['nosotros']);
        echo $LandingWeb->insertLandingWeb(
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
        );
        break;

        case 'deleteLW':
            $landing_id = cleanfild($data['id']);
            echo $LandingWeb->deleteLandingWeb($landing_id);
            break;

        case 'updLW':
            $landing_id = $data['id'];
            $campo     =  $data['Campo'];
            $valor     =  $data['Valor'];

            echo $LandingWeb->updateCampoLandingWeb($landing_id,$campo,$valor);
            break;
       
        // ----------------------
        // wcombo_condicion
        // ----------------------
        // case 'insert_condicion':
        //     $condicion_nombre = $data['nombre']);
        //     echo $ControladorCombo->insertCondicion($condicion_nombre);
        //     break;

        // case 'get_condiciones':
        //     echo $ControladorCombo->getAllCondiciones();
        //     break;

        // case 'delete_condicion':
        //     $condicion_id = $data['id']);
        //     echo $ControladorCombo->deleteCondicion($condicion_id);
        //     break;

        // ----------------------
        // wcombo_operacion
        // ----------------------
        // case 'insert_operacion':
        //     $condicion_id = $data['id']);
        //     $condicion_nombre = $data['nombre']);
        //     echo $ControladorCombo->insertOperacion($condicion_id, $condicion_nombre);
        //     break;

        // case 'get_operaciones':
        //     echo $ControladorCombo->getAllOperaciones();
        //     break;

        // case 'delete_operacion':
        //     $condicion_id = $data['id']);
        //     echo $ControladorCombo->deleteOperacion($condicion_id);
        //     break;

        // ----------------------
        // wcombo_tipobien
        // ----------------------
        // case 'insert_tipobien':
        //     $tbien_nombre = $data['nombre']);
        //     echo $ControladorCombo->insertTipoBien($tbien_nombre);
        //     break;

        // case 'get_tipobienes':
        //     echo $ControladorCombo->getAllTipoBienes();
        //     break;

        // case 'delete_tipobien':
        //     $tbien_id = $data['id']);
        //     echo $ControladorCombo->deleteTipoBien($tbien_id);
        //     break;


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
                $fnacimiento
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
        // Slider
        // ----------------------

        case 'get_slider':
            $id = $data['id'];
            echo $SliderController->getSlider($id);
            break;

        case 'listar_slider':
            echo $SliderController->getSliders();
            break;

        case 'add_slider':
            $url = $data['imagen'] ?? null;
            // if ($url && $url['error'] === UPLOAD_ERR_OK) {
            // } else {
            //     $url = null;
            // }
            $nombre = $data['nombre'];
            echo $SliderController->insertSlider($url, $nombre);
            break;

        case 'upd_slider':
            $id = $data['id'];
            $url = $data['imagen'];
            $nombre = $data['nombre'];
            $estado = $data['estado'];
            echo $SliderController->updateSlider($id, $url, $nombre, $estado);
            break;

        case 'del_slider':
            $id = $data['id'];
            echo $SliderController->deleteSlider($id);
            break;
        
        case 'upd_Estado':
            $id =  $data['id'];
            $estado =  $data['estado'];
            echo $SliderController->updateEstado($id,$estado);
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
// documentos
        case 'list_documentos':
            echo $DocumentosController->getDocumentos();
            break;

        case 'get_documento':
            $id = $data['id'];
            echo $DocumentosController->getDocumento($id);
            break;

        case 'add_documento':
            $documento_nombre = $data['nombre'];
            $documento_descripcion = $data['descripcion'];
            $url_documento = $data['url'];
            $id_cliente = $data['id_cliente'];
            echo $DocumentosController->insertDocumento( $documento_nombre, $documento_descripcion, $url_documento, $id_cliente);
            break;

        case 'upd_documento':
            $id_documento = $data['id'];
            $documento_nombre = $data['nombre'];
            $documento_descripcion = $data['descripcion'];
            $url_documento = $data['url'];
            $id_cliente = $data['id_cliente'];
            echo $DocumentosController->updateDocumento($id_documento, $documento_nombre, $documento_descripcion, $url_documento, $id_cliente);
            break;

        case 'del_documento':
            $id = $data['id'];
            echo $DocumentosController->deleteDocumento($id);
            break;

        case 'get_documento_cliente':
            $cliente_id = $data['cliente_id'];
            echo $DocumentosController->getDocumentosPorCliente($cliente_id);
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
                $horario_id
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
                $horario_id
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
            echo $CitasController->getHorariosDisponibles($fecha);
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

        case 'add_pregunta':
            $descripcion = $data['descripcion'];
            $respuesta = $data['respuesta'];
            $info_adicional = $data['info_adicional'] ?? '';
            echo $PreguntasController->insertPregunta(
                $descripcion,
                $respuesta,
                $info_adicional
            );
            break;

        case 'updatePregunta':
            $id = $data['id'];
            $descripcion = $data['descripcion'];
            $respuesta = $data['respuesta'];
            $info_adicional = $data['info_adicional'] ?? '';
            echo $PreguntasController->updatePregunta(
                $id,
                $descripcion,
                $respuesta,
                $info_adicional
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