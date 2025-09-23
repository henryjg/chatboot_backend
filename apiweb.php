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

// ----------------------
// Procesamiento de la petición
// ----------------------
if ($metodoPeticion === 'POST') {
    switch ($peticion) {

        case 'get_OperacionesCompletasFiltrado':
            $anio = isset($data['anio']) ? $data['anio'] : null;
            $mes = isset($data['mes']) ? $data['mes'] : null;
            $dia = isset($data['dia']) ? $data['dia'] : null;
            $asesorNombre = isset($data['asesorNombre']) ? $data['asesorNombre'] : null;
            $supervisorNombre = isset($data['supervisorNombre']) ? $data['supervisorNombre'] : null;
            $propietarioNombre = isset($data['propietarioNombre']) ? $data['propietarioNombre'] : null;
            $inmuebleNombre = isset($data['inmuebleNombre']) ? $data['inmuebleNombre'] : null;
            $Operaciones->get_OperacionesCompletasFiltrado(
                $anio,
                $mes,
                $dia,
                $asesorNombre,
                $supervisorNombre,
                $propietarioNombre,
                $inmuebleNombre
            );
            break;

        case 'get_AsesoresConOperaciones':
            $Operaciones->get_AsesoresConOperaciones();
            break;

        case 'get_SupervisoresConOperaciones':
            $Operaciones->get_SupervisoresConOperaciones();
            break;

        case 'get_PropietariosConOperaciones':
            $Operaciones->get_PropietariosConOperaciones();
            break;

        case 'get_InmueblesConOperaciones':
            $Operaciones->get_InmueblesConOperaciones();
            break;

        case 'get_AniosConOperaciones':
            $Operaciones->get_AniosConOperaciones();
            break;

        case 'get_OperacionesPorContrato':
            $idcontrato = $data['idcontrato'];
            $Operaciones->get_OperacionesPorContrato($idcontrato);
            break;

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

       case 'get_asesores_general':
            $asesorId = $data['asesorId'] ?? null;
            $supervisorId = $data['supervisorId'] ?? null;
            $inmuebleId = $data['inmuebleId'] ?? null;
            echo $TrabajadorController->get_Asesores_General($asesorId, $supervisorId, $inmuebleId);
            break;
            

        case 'get_asesores':
            echo $TrabajadorController->getAsesores();
            break;

        case 'get_asesores_asignado':
            $inmuebleId     =  $data['inmuebleId'];
            echo $TrabajadorController->getAsesores_asignado($inmuebleId);
            break;

        case 'delete_asesorAsignado':
            $asig_id   =  $data['id'];
            echo $TrabajadorController->eliminarAsesorAsignado($asig_id);
            break;            

        case 'delete_asesor':
            $trabajadorId = $data['trabajadorId'];
            $inmuebleId = $data['inmuebleId'];
            $comentario = $data['comentario'];
            echo $TrabajadorController->quitar_asignacion($trabajadorId, $inmuebleId, $comentario);
            break; 

        case 'asignar_Asesor':
            $trabajadorId = $data['trabajadorId'];
            $inmuebleId = $data['inmuebleId'];
            $tipoAsignacion = $data['tipoAsignacion'];
            echo $TrabajadorController->Asignar_Asesor($trabajadorId, $inmuebleId, $tipoAsignacion);
            break;

        case 'del_trabajador':
            $id =  $data['id'];
            echo $TrabajadorController->updateEstadoTrabajador($id,"Inactivo");
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
            $cargo = $data['cargo'];
            $supervisor = $data['supervisor_id'] ?? null;
            $correo = $data['correo'];
            $telefono = $data['telefono'];
            $celular = $data['celular'];
            $fnacimiento = $data['fnacimiento'];
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
                $dni,$nombre,$apellidos,$correo,$cargo,$supervisor,$telefono,
                $celular,$fnacimiento,$fotoPerfil
            );
            break;

        case 'get_supervisores':
            echo $TrabajadorController->getSupervisores();
            break;

        case 'eliminarAsignacionAsesorInmueble':
            $trabajadorId = $data['trabajador_id'];
            $inmuebleId = $data['inmueble_id'];
            echo $TrabajadorController->eliminarAsignacionAsesorInmueble($trabajadorId, $inmuebleId);
            break;

        case 'eliminarLogicoAsesor':
            $id =  $data['id'];
            echo $TrabajadorController->eliminarLogicoAsesor($id);
            break;

        // ----------------------
        // Características GENERAL DE INMUEBLE
        // ----------------------
        case 'add_caracteristica':
            $nombre =  $data['nombre'];
            echo $InmuebleController->insertCaracteristica($nombre);
            break;

        case 'del_caracteristica':
            $id =  $data['id'];
            $id_ca =  $data['id_caracteristica'];
            echo $InmuebleController->deleteCaracteristica($id,$id_ca);
            break;

        case 'get_caracteristicas':
            echo $InmuebleController->getCaracteristicas();
            break;

      
            // ----------------------
        // Características GENERAL DE INMUEBLE
        // ----------------------

        case 'add_caracteristicaInmueble':
            $descripcion  =  $data['descripcion'];
            $idcarac    =  $data['idcarac'];
            $idinmueble =  $data['idinmueble'];
            echo $InmuebleController->insertCaracteristica_Inmueble($idcarac, $idinmueble,$descripcion);
            break;

        // case 'del_caracteristicaInmueble':
        //     $idcar = $data['id'] ?? null;
        //     if ($idcar) {
        //         echo $InmuebleController->deleteCaracteristica($idcar);
        //     } else {
        //         Response::error('ID no proporcionado para eliminar característica');
        //     }
        //     break;

        case 'del_caracteristicaInmueble':
            $idcarac = $data['idcarac'];
            $idinmueble = $data['id_inmueble'];
            echo $InmuebleController->deleteCaracteristica($idcarac, $idinmueble);
            break;

        case 'get_caracteristicaInmueble':
            $idinmueble =  $data['idinmueble'];
            echo $InmuebleController->getCaracteristicas_Inmueble($idinmueble);
            break;
        case 'get_caracteristicaInmueble_detalles':
            $idinmueble =  $data['idinmueble'];
            echo $InmuebleController->getCaracteristicas_Inmueble_Detalles($idinmueble);
            break;
    

        //- ----- RELACION --------------
        

         // ----------------------
        // Inmuebles
        // ----------------------
        case 'get_lista_inmuebles':
            echo $InmuebleController->getInmuebles();
            break;

        case 'getInmueblesPorEstado':
            $estado = $data['estado'];
            echo $InmuebleController->getInmueblesPorEstado($estado);
            break;

        case 'getInmuebles_web':
            echo $InmuebleController->getInmuebles_web();
            break;

        case 'get_inmueble':
            $id =  $data['id'];
            echo $InmuebleController->getInmueble($id);
            break;

        case 'get_proyecto':
            $id =  $data['id'];
            echo $InmuebleController->getProyecto($id);
            break;

        case 'getinmueble_asesor':
            $id = $data['id'];
            echo $InmuebleController->getInmueblesPorAsesor($id);
            break;

        case 'get_full_info_inmueble':
            $id =  $data['id'];
            echo $InmuebleController->getInmueble_web($id);
            break;

        case 'obtenerInmuebleDatos':
            $inmu_id = $data['id'];
            echo $InmuebleController->obtenerInmuebleDatos($inmu_id);
            break;

        case 'get_lista_inmueble_filtro':
            $filters = [
                'inmu_tipobien' => $data['tipobien'] ?? null,
                // 'inmu_ubigeo' => $data['ubigeo'] ?? null,
                'inmu_condicioncontrato' => $data['condicioncontrato'] ?? null,
                'inmu_tipooperacion' => $data['tipooperacion'] ?? null,
            ];
            echo $InmuebleController->get_Lista_Inmueble_web_filters($filters);
            break;

        case 'get_lista_inmueble_foto':
            echo $InmuebleController->get_Lista_Inmueble_web();
            break;

        case 'get_Lista_Inmueble_paneladmin':
            echo $InmuebleController->get_Lista_Inmueble_paneladmin();
            break;
    
        case 'del_inmueble':
            try {
                $id = $data['id'] ?? null;
                if (!$id) {
                    throw new Exception('ID no proporcionado');
                }
                echo $InmuebleController->delete_Inmueble($id);
            } catch (Exception $e) {
                Response::error('Error al eliminar inmueble: ' . $e->getMessage());
            }
            break;

        case 'eliminarLogicoInmueble':
            $inmu_id = $data['id'];
            echo $InmuebleController->eliminarLogicoInmueble($inmu_id);
            break;

        case 'add_inmueble':
            $tipobien =         $data['tipobien'];
            $titulo =           $data['titulo'];
            $descripcion =      $data['descripcion'];
            $areaterreno =      $data['areaterreno'];
            $areaconstruida =   $data['areaconstruida'];
            $condicioncontrato =$data['condicioncontrato'];
            $partidaregistral = $data['partidaregistral'];
            $tipooperacion =    $data['tipooperacion'];
            $precio =           $data['precio'];
            $moneda =           $data['moneda'];
            $ubigeo =           $data['ubigeo'];
            $direccion =        $data['direccion'];
            $latitud =          $data['latitud'];
            $long =             $data['longitud'];
            $video =            $data['video'];
            $categoria =         $data['categoria'];
            $largo =           $data['largo'];
            $ancho =           $data['ancho'];
            $idasesor =         $data['asesorId'];
            $propietario_id =  $data['propietarioId'];

            echo $InmuebleController->insertInmueble(
                $tipobien,
                $titulo,
                $descripcion,
                $areaterreno,
                $areaconstruida,
                $condicioncontrato,
                $partidaregistral,
                $tipooperacion,
                $precio,
                $moneda,
                $direccion,
                $ubigeo,
                $latitud,
                $long,
                $video,
                $categoria,
                $largo,
                $ancho,
                $idasesor,
                $propietario_id
              );
            break;

        case 'crearInmueblePropietarioYContrato':
            $inmu_titulo = $data['titulo'];
            $propietario_id = $data['propietarioId'];
            $asesor_id = $data['asesorId'];

            echo $InmuebleController->crearInmueblePropietarioYContrato(
                $inmu_titulo,
                $propietario_id,
                $asesor_id
            );
            break;
            
        case 'add_proyecto':
            $tipobien =         $data['tipobien'];
            $titulo =           $data['titulo'];
            $descripcion =      $data['descripcion'];
            $areaterreno =      $data['areaterreno'];
            $areaconstruida =   $data['areaconstruida'];
            $condicioncontrato =$data['condicioncontrato'];
            $tipooperacion =    $data['tipooperacion'];
            $precio =           $data['precio'];
            $moneda =           $data['moneda'];
            $ubigeo =           $data['ubigeo'];
            $direccion =        $data['direccion'];
            $latitud =          $data['latitud'];
            $long =             $data['longitud'];
            $video =            $data['video'];
            $categoria =         $data['categoria'];
            $largo =           $data['largo'];
            $ancho =           $data['ancho'];
            $slogan =           $data['slogan'] ?? null;
            $sector1 =         $data['sector1'] ?? null;
            $sector2 =         $data['sector2'] ?? null;
            $sector3 =         $data['sector3'] ?? null;

            echo $InmuebleController->insertProyecto(
                $tipobien,
                $titulo,
                $descripcion,
                $areaterreno,
                $areaconstruida,
                $tipooperacion,
                $precio,
                $moneda,
                $direccion,
                $ubigeo,
                $latitud,
                $long,
                $video,
                $categoria,
                $largo,
                $ancho,
                $slogan,
                $sector1,
                $sector2,
                $sector3
              );
            break;
        
        case 'upd_inmueble':
            $inmu_id =          $data['id'];
            $tipobien =         $data['tipobien'];
            $titulo =           $data['titulo'];
            $descripcion =      $data['descripcion'];
            $areaterreno =      $data['areaterreno'];
            $areaconstruida =   $data['areaconstruida'];
            $condicioncontrato =$data['condicioncontrato'];
            $partidaregistral = $data['partidaregistral'];
            $tipooperacion =    $data['tipooperacion'];
            $precio =           $data['precio'];
            $moneda =           $data['moneda'];
            
            $ubigeo =           $data['ubigeo'];
            $direccion =        $data['direccion'];
            $latitud =          $data['latitud'];
            $long =             $data['longitud'];
            $video =            $data['video'];
            $categoria =         $data['categoria'];
            $largo =           $data['largo'];
            $ancho =           $data['ancho'];

            echo $InmuebleController->updateInmueble(
                $inmu_id,
                $tipobien,
                $titulo,
                $descripcion,
                $areaterreno,
                $areaconstruida,
                $condicioncontrato,
                $partidaregistral,
                $tipooperacion,
                $precio,
                $moneda,
                $direccion,
                $ubigeo,
                $latitud,
                $long,
                $video,
                $categoria,
                $largo,
                $ancho
                );
            break;
        
            case 'upd_proyecto':
                $inmu_id =          $data['id'];
                $tipobien =         $data['tipobien'];
                $titulo =           $data['titulo'];
                $descripcion =      $data['descripcion'];
                $areaterreno =      $data['areaterreno'];
                $areaconstruida =   $data['areaconstruida'];
                $condicioncontrato =$data['condicioncontrato'];
                $tipooperacion =    $data['tipooperacion'];
                $precio =           $data['precio'];
                $moneda =           $data['moneda'];
                $ubigeo =           $data['ubigeo'];
                $direccion =        $data['direccion'];
                $latitud =          $data['latitud'];
                $long =             $data['longitud'];
                $video =            $data['video'];
                $categoria =         $data['categoria'];
                $largo =           $data['largo'];
                $ancho =           $data['ancho'];
                $slogan =           $data['slogan'];
                $sector1 =         $data['sector1'];
                $sector2 =         $data['sector2'];
                $sector3 =         $data['sector3'];

                echo $InmuebleController->updateProyecto(
                    $inmu_id,
                    $tipobien,
                    $titulo,
                    $descripcion,
                    $areaterreno,
                    $areaconstruida,
                    $condicioncontrato,
                    $tipooperacion,
                    $precio,
                    $moneda,
                    $direccion,
                    $ubigeo,
                    $latitud,
                    $long,
                    $video,
                    $categoria,
                    $largo,
                    $ancho,
                    $slogan,
                    $sector1,
                    $sector2,
                    $sector3
                    );
                break;    
        
        case 'get_proyectos':
            echo $InmuebleController->getProyectos();
            break;

        case 'get_Lista_proyectos_paneladmin':
            echo $InmuebleController->get_Lista_Proyecto_paneladmin();
            break;

        case 'add_updPrecio':
            $id =      $data['id'];
            $precio =  $data['precio'];
            echo $InmuebleController->updateInmueblePrecio($id, $precio);
            break;
            
        case 'add_updEstado':
            $id =      $data['id'];
            $estado =  $data['estado'];
            echo $InmuebleController->updateInmuebleEstado($id, $estado);
            break;
        
        case 'add_fotoInmueble':
            $idinmueble =  $data['idinmueble'];
            $file =        $_FILES['file'];
            echo $InmuebleController->subirFoto($idinmueble, $file);
            break;

        case 'get_fotos_Inmueble':
            $idinmueble =  $data['idinmueble'];
            echo $InmuebleController->getFotosInmueble($idinmueble);
            break;
        case 'del_foto_Inmueble':
            $idinmueble =  $data['idinmueble'];
            echo $InmuebleController->deleteFotoInmueble($idinmueble);
            break;

        case 'getTiposOperacion':
            echo $InmuebleController->getTiposOperacion();
            break;

        case 'getTiposBien':
            echo $InmuebleController->getTiposBien();
            break;

        case 'get_TiposBien':
            echo $InmuebleController->get_TiposBien();
            break;

        case 'get_TiposOperacion':
            echo $InmuebleController->get_TiposOperacion();
            break;

        case 'get_TiposCondicion':
            echo $InmuebleController->get_TiposCondicion();
            break;

        case 'getListados':
            echo $InmuebleController->getListados();
            break;

        case 'getUbigeoPiura':
            echo $InmuebleController->getUbigeoPiura();
            break;

        case 'getUbigeo_existentes':
            echo $InmuebleController->getUbigeoPiura_existentes();
            break;
        
        case 'getCondicionesContrato':
            echo $InmuebleController->getCondicionesContrato();
            break;

        case 'getTotalProyectos':
            echo $InmuebleController->getTotalInmueblesPanelAdmin();
            break;

        case 'getTotalInmuebles':
            echo $InmuebleController->getTotalInmueblesWeb();
            break;

        // ----------------------
        // Clientes
        // ----------------------
        case 'get_lista_clientes':
            echo $ClienteController->getClientes();
            break;

        case 'listaclientes':
            echo $ClienteController->get_Lista_Clientes();
            break;
        
        case 'get_lista_cliente_asesor':
            echo $ClienteController->get_Lista_Clientes_Asesor();
            break;

        case 'asignarAsesorAClienteLead':
            $clienteId = $data['clienteId'];
            $inmuebleId = $data['inmuebleId'];
            echo $ClienteController->asignarAsesorAClientePorInmueble($clienteId, $inmuebleId);
            break;

        case 'lista_estado':
             $anio = isset($data['anio']) ? $data['anio'] : null;
            $mes = isset($data['mes']) ? $data['mes'] : null;
            $dia = isset($data['dia']) ? $data['dia'] : null;
            $asesorNombre = isset($data['asesorNombre']) ? $data['asesorNombre'] : null;
            $supervisorNombre = isset($data['supervisorNombre']) ? $data['supervisorNombre'] : null;
            $propietarioNombre = isset($data['propietarioNombre']) ? $data['propietarioNombre'] : null;
            $inmuebleNombre = isset($data['inmuebleNombre']) ? $data['inmuebleNombre'] : null;
            $Operaciones->get_OperacionesCompletasFiltrado(
                $anio,
                $mes,
                $dia,
                $asesorNombre,
                $supervisorNombre,
                $propietarioNombre,
                $inmuebleNombre
            );
            break;

        case 'get_cliente':
            $id =  $data['id'];
            echo $ClienteController->getCliente($id);
            break;
        
        case 'add_cliente':
            $tipodocumento =        $data['tipodocumento'];
            $numerodoc =            $data['numdoc'];
            $nombres =              $data['nombres'];
            $apellidos =            $data['apellidos'];
            $direccion =            $data['direccion'];
            $empresaRUC =           $data['empresaRUC'];
            $razonsocial =          $data['razonsocial'];
            $email =                $data['email'];
            $telefono =             $data['telefono'];
            $celular =              $data['celular'];
            $comentario =           $data['comentario'];
            $estado =               $data['estado'];
            $fuenteingresos =       $data['fuenteingresos'];
            $ingresomensual =       $data['ingresomensual'];
            $ahorrodisponible =     $data['ahorrodisponible'];
            $historialcrediticio =  $data['historialcrediticio'];
            $procedencia =        $data['procedencia'];
            $leadEstado = $data['leadEstado'] ?? 'Lead'; // Valor predeterminado
            $fecharegistro = $data['fecharegistro'] ?? date('Y-m-d'); // Valor predeterminado
            $inmuebleinteres = $data['inmuebleinteres'] ?? ''; // Valor predeterminado

        

            echo $ClienteController->addCliente(
                 $tipodocumento,
                 $numerodoc,
                 $nombres,
                 $apellidos,
                 $direccion,
                 $empresaRUC,
                 $razonsocial,
                 $email,
                 $telefono,
                 $celular,
                 $comentario,
                 $fuenteingresos,
                 $ingresomensual,
                 $ahorrodisponible,
                 $historialcrediticio,
                 $procedencia,
                 $inmuebleinteres,
                );
            break;

         case 'add_cliente_lead':
            $tipodocumento =        $data['tipodocumento'];
            $numerodoc =            $data['numdoc'];
            $nombres =              $data['nombres'];
            $apellidos =            $data['apellidos'];
            $direccion =            $data['direccion'];
            $empresaRUC =           $data['empresaRUC'];
            $razonsocial =          $data['razonsocial'];
            $email =                $data['email'];
            $telefono =             $data['telefono'];
            $celular =              $data['celular'];
            $comentario =           $data['comentario'];
            $estado =               $data['estado'];
            $fuenteingresos =       $data['fuenteingresos'];
            $ingresomensual =       $data['ingresomensual'];
            $ahorrodisponible =     $data['ahorrodisponible'];
            $historialcrediticio =  $data['historialcrediticio'];
            $procedencia =        $data['procedencia'];
            // $leadEstado = $data['leadEstado'] ?? 'Lead'; // Valor predeterminado
            $fecharegistro = $data['fecharegistro'] ?? date('Y-m-d'); // Valor predeterminado
            $inmuebleinteres = $data['inmuebleinteres'] ?? ''; // Valor predeterminado
            $idinmueble = $data['idinmueble'];

        

            echo $ClienteController->addCliente_lead(
                 $tipodocumento,
                 $numerodoc,
                 $nombres,
                 $apellidos,
                 $direccion,
                 $empresaRUC,
                 $razonsocial,
                 $email,
                 $telefono,
                 $celular,
                 $comentario,
                 $fuenteingresos,
                 $ingresomensual,
                 $ahorrodisponible,
                 $historialcrediticio,
                 $procedencia,
                 $inmuebleinteres,
                 $idinmueble
                );
            break;

    
        case 'add_cliente_asesor':
            $tipodocumento =        $data['tipodocumento'];
            $numerodoc =            $data['numdoc'];
            $nombres =              $data['nombres'];
            $apellidos =            $data['apellidos'];
            $direccion =            $data['direccion'];
            $empresaRUC =           $data['empresaRUC'];
            $razonsocial =          $data['razonsocial'];
            $email =                $data['email'];
            $telefono =             $data['telefono'];
            $celular =              $data['celular'];
            $comentario =           $data['comentario'];
            $estado =               $data['estado'];
            $fuenteingresos =       $data['fuenteingresos'];
            $ingresomensual =       $data['ingresomensual'];
            $ahorrodisponible =     $data['ahorrodisponible'];
            $historialcrediticio =  $data['historialcrediticio'];
            $procedencia =        $data['procedencia'];
            $leadEstado = $data['leadEstado'] ?? 'Lead'; // Valor predeterminado
            $fecharegistro = $data['fecharegistro'] ?? date('Y-m-d'); // Valor predeterminado
            $inmuebleinteres = $data['inmuebleinteres'] ?? ''; // Valor predeterminado

        

            echo $ClienteController->addCliente(
                 $tipodocumento,
                 $numerodoc,
                 $nombres,
                 $apellidos,
                 $direccion,
                 $empresaRUC,
                 $razonsocial,
                 $email,
                 $telefono,
                 $celular,
                 $comentario,
                 $fuenteingresos,
                 $ingresomensual,
                 $ahorrodisponible,
                 $historialcrediticio,
                 $procedencia,
                    $inmuebleinteres,
                
                );
            break;
        
        case 'upd_cliente':
            $id =                   $data['id'];
            $tipodocumento =        $data['tipodocumento'];
            $nombres =              $data['nombres'];
            $apellidos =            $data['apellidos'];
            $numerodoc =            $data['numdoc'];
            $empresaRUC =           $data['empresaRUC'];
            $direccionfiscal =      $data['direccionfiscal'];
            $email =                $data['email'];
            $telefono =             $data['telefono'];
            $celular =              $data['celular'];
            $comentario =           $data['comentario'];
            $fuenteingresos =       $data['fuenteingresos'];
            $comentario =           $data['comentario'];
            $ingresomensual =       $data['ingresomensual'];
            $ahorrodisponible =     $data['ahorrodisponible'];
            $historialcrediticio =  $data['historialcrediticio'];
            $estado =               $data['estado'];
            $leadEstado =           $data['leadEstado'];
            $fecharegistro =       $data['fecharegistro'];
            $inmuebles  =           $data['inmuebles'];
            $procedencia =        $data['procedencia'];
            if ($tipodocumento=="DNI") {
                $personacontacto = $data['personacontacto'];
            } else {
                $personacontacto = $data['razonsocial'];
            }

            echo $ClienteController->updateCliente(
                $id,
                $tipodocumento,
                $numerodoc,
                $nombres,
                $apellidos,
                $direccion,
                $empresaRUC,
                $razonsocial,
                $email,
                $telefono,
                $celular,
                $comentario,
                $estado,
                $fuenteingresos,
                $ingresomensual,
                $ahorrodisponible,
                $historialcrediticio,
                $procedencia,
                $leadEstado,
                $inmuebles,
                $fecharegistro
            );
            break;
        
        // case 'upd_ClienteEstado':
        //     $id = $data['id'];
        //     $estado = $data['estado'];
        //     $mensaje = $data['mensaje'] ?? 'Estado actualizado';
        //     $asesorId = $data['asesorId'] ?? null; // Optional field for the advisor ID
        //     $fechaAccion = date("Y-m-d H:i:s");
        
        //     $updateResult = $ClienteController->updateClienteEstado($id, $estado);
        //     if ($updateResult) {
        //         $ClienteController->insertMensaje($mensaje, 'Estado actualizado', $fechaAccion, $asesorId, $id);
        //     }
        //     echo $updateResult;
        //     break;

        case 'upd_ClienteEstado':
            $id = $data['id'];
            $estado = $data['estado'];;
            echo $ClienteController->updateClienteEstado($id, $estado);
            break;
        
        case 'upd_ClienteEstadoLead':
            $id = $data['id'];
            $estadolead = $data['estado'];
            echo $ClienteController->updateClienteEstadoLead($id, $estadolead);
            break;

        case 'del_Cliente':
            $id =      $data['id'];
            echo $ClienteController->updateClienteEstado($id, "Inactivo");
            break;
        
        case 'eliminar_cliente':
            $id = $data['id'];
            echo $ClienteController->deleteCliente($id);
            break;
        
        case 'get_mensajes_cliente':
            $cliente_id = $data['clienteId'];
            echo $ClienteController->getMensajesByCliente($cliente_id);
            break;

        case 'add_mensaje':
            $mensaje_mensaje = $data['mensaje'];
            $mensaje_accion = $data['accion'];
            $mensaje_estado = $data['estado']; 
            $mensaje_fechaaccion = $data['fechaaccion'];
            $mensaje_asesorId = $data['asesorId'] ?? null;
            $mensaje_clienteId = $data['clienteId'];
        
            echo $ClienteController->insertMensaje(
                $mensaje_mensaje,
                $mensaje_accion,
                $mensaje_estado,
                $mensaje_fechaaccion,
                $mensaje_asesorId,
                $mensaje_clienteId
            );
            break;

        case 'updateMensajeEstado':
            $mensaje_id = $data['id'];
            $nuevo_estado = $data['estado'];
            echo $ClienteController->updateMensajeEstado($mensaje_id, $nuevo_estado);
            break;

        case 'upd_mensaje':
            $mensaje_id = $data['id'];
            $mensaje_mensaje = $data['mensaje'];
            $mensaje_accion = $data['accion'];
            $mensaje_estado = $data['estado'];
            $mensaje_fechaaccion = $data['fechaaccion'];
            $mensaje_asesorId = $data['asesorId'];
        
            echo $ClienteController->updateMensaje(
                $mensaje_id,
                $mensaje_mensaje,
                $mensaje_accion,
                $mensaje_estado,
                $mensaje_fechaaccion,
                $mensaje_asesorId
            );
            break;

        case 'delete_mensaje':
            $id = $data['id'];
            echo $ClienteController->deleteMensaje($id);
            break;

        case 'add_mensaje_cliente':
            $cliente_id = $data['clienteId'];
            $mensaje_mensaje = $data['mensaje'];
            $mensaje_accion = $data['accion'];
            $mensaje_fechaaccion = $data['fechaaccion'];
            $mensaje_asesorId = $data['asesorId'] ?? null;
            $estado = $data['estado'];

            echo $ClienteController->addMensajeForCliente(
                $cliente_id,
                $mensaje_mensaje,
                $mensaje_accion,
                $mensaje_fechaaccion,
                $mensaje_asesorId,
                   $estado
            );
            break;

        case 'getLeadsByAsesor':
            $asesorId = $data['asesorId'];
            echo $ClienteController->getLeadsByAsesor($asesorId);
            break;

        // ----------------------
        // contratos
        // ----------------------

        case 'get_contratos':
            echo $ContratoController->getContratos();
            break;

        case 'get_contrato':
            $id =  $data['id'];
            echo $ContratoController->getContrato($id);
            break;
        
        case 'add_contrato':
            $pdf =                  $data['pdf'];
            $tipo =                 $data['tipo'];
            $fechainicio =          $data['fechainicio'];
            $fechafin =             $data['fechafin'];
            $mesesvigencia =        $data['mesesvigencia'];
            $comision =             $data['comision'];
            $propietarioId =        $data['propietarioId'];
            $contrato_inmuebleId =  $data['inmuebleId'];

            echo $ContratoController->insertContrato(
                $pdf,
                $tipo,
                $fechainicio,
                $fechafin,
                $mesesvigencia,
                $comision,
                $propietarioId,
                $contrato_inmuebleId);
            break;
        
        case 'upd_contrato':
            $id = $data['id'] ?? null;
            $numerocontrato = $data['numerocontrato'] ?? null;
            $tipo = $data['tipo'] ?? null;
            $pdf = $data['pdf'] ?? null;
            $fechainicio = $data['fechainicio'] ?? null;
            $fechafin = $data['fechafin'] ?? null;
            $mesesvigencia = $data['mesesvigencia'] ?? null;
            $comision = $data['comision'] ?? null;
            $estado = $data['estado'] ?? null;
            $propietarioId = $data['propietarioId'] ?? null;
            $contrato_inmuebleId = $data['inmuebleId'] ?? null; // Cambiar a inmuebleId

            // Validar que contrato_inmuebleId (inmuebleId) esté presente
            if (!$contrato_inmuebleId) {
                Response::error('El campo inmuebleId es obligatorio.');
                break;
            }

            echo $ContratoController->updateContrato(
                $id,
                $numerocontrato,
                $tipo,
                $pdf,
                $fechainicio,
                $fechafin,
                $mesesvigencia,
                $comision,
                $estado,
                $propietarioId,
                $contrato_inmuebleId
            );
            break;

        case 'del_contrato':
            $id =      $data['id'];
            echo $ContratoController->deleteContrato($id);
            break;
        
        case 'updateContratoPdf':
            $contrato_id =  $data['id'];
            $contrato_pdf = $data['pdf'];
            echo $ContratoController->updateContratoPdf($contrato_id, $contrato_pdf);
            break;

        case 'updateContratoVigencia':
            $contrato_id =              $data['id'];
            $contrato_fechafin =        $data['fechafin'];
            $contrato_mesesvigencia =   $data['mesesvigencia'];
            echo $ContratoController->updateContratoVigencia($contrato_id, $contrato_fechafin, $contrato_mesesvigencia);
            break;

        case 'get_contratos_by_inmueble':
            $inmuebleId = $data['inmuebleId'];
            echo $ContratoController->getContratosByInmueble($inmuebleId);
            break;

        case 'del_contratos_by_inmueble':
            $inmuebleId = $data['inmuebleId'];
            echo $ContratoController->deleteContratosByInmueble($inmuebleId);
            break;

        case 'del_contrato_inm_prop':
            $inmuebleId = $data['inmuebleId'];
            $propietarioId = $data['propietarioId'];
            echo $ContratoController->deleteContratosByInmuebleAndPropietario($inmuebleId, $propietarioId);
            break;

        case 'CancelarContrato':
            $contrato_id = $data['contratoId'];
            $contrato_estado = $data['estado'];
            $contrato_mensaje = $data['mensaje'] ?? '';
            echo $ContratoController->updateContratoEstado($contrato_id, $contrato_estado, $contrato_mensaje);
            break;

        // case 'get_historial_contrato_estado':
        //     $contrato_id = $data['contrato_id'];
        //     echo $ContratoController->getHistorialContratoEstado($contrato_id);
        //     break;

        case 'insertarContratoGenerado':
            $tipo = $data['tipo'];
            $idContrato = $data['idContrato'];

            // Validar que el tipo sea "Venta" o "Alquiler"
            if (!$tipo || !in_array($tipo, ['Venta', 'Alquiler'])) {
                response::error('Tipo de operación inválido. Debe ser "Venta" o "Alquiler".');
                exit;
            }

            if (!$idContrato) {
                response::error('Falta el ID del contrato.');
                exit;
            }

            $cabecera = $data['cabecera'];
            $parrafo1a = $data['parrafo1a'];
            $parrafo1b = $data['parrafo1b'];
            $parrafo2 = $data['parrafo2'];
            $parrafo3 = $data['parrafo3'];
            $pie = $data['pie'];
            $firmas = $data['firmas'];

    echo $ContratoController->insertarContratoGenerado(
        $tipo,
        $cabecera,
        $parrafo1a,
        $parrafo1b,
        $parrafo2,
        $parrafo3,
        $pie,
        $firmas,
        $idContrato
    );
    break;

        case 'getContratoGenerado':
            $contratoGeneradoId = $data['contratoGeneradoId'];
            echo $ContratoController->getContratoGenerado($contratoGeneradoId);
            break;

        case 'getContratoGeneradoIdPorContrato':
            $contratoId = $data['contratoId'];
            echo $ContratoController->getContratoGeneradoIdPorContrato($contratoId);
            break;

        case 'actualizarContrato':
            $contratoGeneradoId = $data['contratoGeneradoId'] ?? null;
            $tipo = $data['tipo'];
            $cabecera = $data['cabecera'] ?? null;
            $parrafo1a = $data['parrafo1a'] ?? null;
            $parrafo1b = $data['parrafo1b'] ?? null;
            $parrafo2 = $data['parrafo2'] ?? null;
            $parrafo3 = $data['parrafo3'] ?? null;
            $pie = $data['pie'] ?? null;
            $firmas = $data['firmas'] ?? null;

            echo $ContratoController->actualizarContrato(
                $contratoGeneradoId,
                $tipo,
                $cabecera,
                $parrafo1a,
                $parrafo1b,
                $parrafo2,
                $parrafo3,
                $pie,
                $firmas
            );
            break;

        case 'generarContratoPDF':
            $contratoGeneradoId = $data['contratoGeneradoId'];
            echo $ContratoController->generarContratoPDF($contratoGeneradoId);
            break;

    // ======================================================================
    // CRUD CONTRATO_MODELO
    // ======================================================================
    
    case 'get_contrato_modelos':
        echo $ContratoController->getContratosModelo();
        break;

    case 'get_contrato_modelo':
        $id = $data['id'];
        echo $ContratoController->getContratoModelo($id);
        break;

    case 'get_contrato_modelo_por_tipo':
        $tipo = $data['tipo'];
        echo $ContratoController->getContratoModeloPorTipo($tipo);
        break;

    case 'add_contrato_modelo':
        $conge_tipo = $data['tipo'];
        $conge_cabecera = $data['cabecera'] ?? '';
        $conge_parrafo1_a = $data['parrafo1a'] ?? '';
        $conge_parrafo1_b = $data['parrafo1b'] ?? '';
        $conge_parrafo2 = $data['parrafo2'] ?? '';
        $conge_parrafo3 = $data['parrafo3'] ?? '';
        $conge_pie = $data['pie'] ?? '';
        $conge_firmas = $data['firmas'] ?? '';

        echo $ContratoController->insertContratoModelo(
            $conge_tipo,
            $conge_cabecera,
            $conge_parrafo1_a,
            $conge_parrafo1_b,
            $conge_parrafo2,
            $conge_parrafo3,
            $conge_pie,
            $conge_firmas
        );
        break;

    case 'upd_contrato_modelo':
        $conge_id = $data['id'];
        $conge_tipo = $data['tipo'];
        $conge_cabecera = $data['cabecera'] ?? '';
        $conge_parrafo1_a = $data['parrafo1a'] ?? '';
        $conge_parrafo1_b = $data['parrafo1b'] ?? '';
        $conge_parrafo2 = $data['parrafo2'] ?? '';
        $conge_parrafo3 = $data['parrafo3'] ?? '';
        $conge_pie = $data['pie'] ?? '';
        $conge_firmas = $data['firmas'] ?? '';

        echo $ContratoController->updateContratoModelo(
            $conge_id,
            $conge_tipo,
            $conge_cabecera,
            $conge_parrafo1_a,
            $conge_parrafo1_b,
            $conge_parrafo2,
            $conge_parrafo3,
            $conge_pie,
            $conge_firmas
        );
        break;

    case 'deleteContratoGenerado':
        $conge_id = $data['idcontrato'];
        echo $ControladorGenerado->deleteContratoGenerado($conge_id);
        break;

    // case 'upd_contrato_modelo_parcial':
    //     $conge_id = $data['id'];
    //     $campos = [];
        
    //     // Campos permitidos para actualización parcial
    //     $camposPermitidos = [
    //         'conge_tipo' => 'tipo',
    //         'conge_cabecera' => 'cabecera',
    //         'conge_parrafo1_a' => 'parrafo1a',
    //         'conge_parrafo1_b' => 'parrafo1b',
    //         'conge_parrafo2' => 'parrafo2',
    //         'conge_parrafo3' => 'parrafo3',
    //         'conge_pie' => 'pie',
    //         'conge_firmas' => 'firmas'
    //     ];

    //     foreach ($camposPermitidos as $campoDb => $campoApi) {
    //         if (isset($data[$campoApi])) {
    //             $campos[$campoDb] = $data[$campoApi];
    //         }
    //     }

    //     echo $ContratoController->updateContratoModeloParcial($conge_id, $campos);
    //     break;

    case 'del_contrato_modelo':
        $id = $data['id'];
        echo $ContratoController->deleteContratoModelo($id);
        break;

    // case 'duplicar_contrato_modelo':
    //     $conge_id_origen = $data['id_origen'];
    //     $nuevo_tipo = $data['nuevo_tipo'];
    //     echo $ContratoController->duplicarContratoModelo($conge_id_origen, $nuevo_tipo);
    //     break;

    // case 'getPlantillaModelo':
    //     $plantilla = $ContratoController->getPlantillaModelo();
    //     echo json_encode($plantilla);
    //     break;

    // case 'generarContratoDinamico':
    //     $contratoId = $data['contratoId'];
    //     echo $ContratoController->generarContratoDinamico($contratoId);
    //     break;

    case 'generarContratoDinamico':
    $contratoId = $data['contratoId'];
    echo json_encode($ContratoController->generarContratoDinamico($contratoId));
    break;

    // ----------------------
    // Operaciones
    // ----------------------

    case 'get_OperacionesCompletas':
        echo $Operaciones->get_OperacionesCompletas();
        break;

    case 'get_operaciones':
        echo $Operaciones->get_Operaciones();
        break;

    case 'get_operacion':
        $id = $data['id'];
        echo $Operaciones->get_OperacionById($id);
        break;

    case 'add_operacion':
        $ClienteId =           $data['clienteId'];
        $ClienteNombre = $data['clienteNombre'];
        $Asesor1Id = $data['asesor1Id'];
        $Asesor1Nombre = $data['asesor1Nombre'];
        $Asesor2Id = $data['asesor2Id'];
        $Asesor2Nombre = $data['asesor2Nombre'];
        $Supervisor1Nombre = $data['supervisor1Nombre'];
        $Supervisor2Nombre = $data['supervisor2Nombre'];
        $MontoTotal = $data['montoTotal'];
        $ComisionGanada = $data['comisionGanada'];
        $ComisionGM = $data['comisionGM'];
        $ComisionAsesores = $data['comisionAsesores'];
        $Adjunto1 = $data['adjunto1'] ?? null;
        $Adjunto2 = $data['adjunto2'] ?? null;
        $Adjunto3 = $data['adjunto3']?? null;
        $PorcentajeComision =  $data['porcentajeComision'];
        $FechaRegistro = $data['fechaRegistro'];
        $ContratoId = $data['contratoId'];
        $Estado = $data['estado'];
        $comentario = $data['comentario'];
        $op_comision_supervisor = $data['comisionSupervisor'];

        echo $Operaciones->insert_Operacion(
            $ClienteId,
            $ClienteNombre,
            $Asesor1Id,
            $Asesor1Nombre,
            $Asesor2Id,
            $Asesor2Nombre,
            $Supervisor1Nombre,
            $Supervisor2Nombre,
            $MontoTotal,
            $PorcentajeComision,
            $ComisionGanada,
            $ComisionGM,
            $ComisionAsesores,
            $FechaRegistro,
            $ContratoId,
            $Estado,
            $comentario,
            $Adjunto1,
            $Adjunto2,
            $Adjunto3,
            $op_comision_supervisor
        );
        break;

    case 'upd_operacion':
        $id = $data['id'];
        $ClienteId = $data['clienteId'];
        $ClienteNombre = $data['clienteNombre'];
        $Asesor1Id = $data['asesor1Id'];
        $Asesor1Nombre = $data['asesor1Nombre'];
        $Asesor2Id = $data['asesor2Id'];
        $Asesor2Nombre = $data['asesor2Nombre'];
        $Supervisor1Nombre = $data['supervisor1Nombre'];
        $Supervisor2Nombre = $data['supervisor2Nombre'];
        $MontoTotal = $data['montoTotal'];
        $PorcentajeComision = $data['porcentajeComision'];
        $ComisionGanada = $data['comisionGanada'];
        $ComisionGM = $data['comisionGM'];
        $ComisionAsesores = $data['comisionAsesores'];
        $FechaRegistro = $data['fechaRegistro'];
        $ContratoId = $data['contratoId'];
        $Estado = $data['estado'];
        $Comentario = $data['comentario'];
        $Adjunto1 = $data['adjunto1'] ?? null;
        $Adjunto2 = $data['adjunto2'] ?? null;
        $Adjunto3 = $data['adjunto3'] ?? null;
        $op_comision_supervisor = $data['comisionSupervisor'];

        echo $Operaciones->update_Operacion(
            $id,
            $ClienteId,
            $ClienteNombre,
            $Asesor1Id,
            $Asesor1Nombre,
            $Asesor2Id,
            $Asesor2Nombre,
            $Supervisor1Nombre,
            $Supervisor2Nombre,
            $MontoTotal,
            $PorcentajeComision,
            $ComisionGanada,
            $ComisionGM,
            $ComisionAsesores,
            $FechaRegistro,
            $ContratoId,
            $Estado,
            $Comentario,
            $Adjunto1,
            $Adjunto2,
            $Adjunto3,
            $op_comision_supervisor
        );
        break;

    case 'del_operacion':
        $id = $data['idOperacion'];
        echo $Operaciones->delete_Operacion($id);
        break;

    case 'get_operacion_detalles':
        $id = $data['id'];
        echo $Operaciones->get_OperacionDetalles($id);
        break;

    case 'get_DatosPorInmueble':
        $inmuebleId = $data['id'];
        echo $Operaciones->get_DatosPorInmueble($inmuebleId);
        break;

    case 'get_OperacionPorCliente':
        $clienteId = $data['id'];
        echo $Operaciones->get_OperacionPorCliente($clienteId);
        break;

    case 'get_OperacionesPorAsesor':
        $asesorId = $data['id_asesor'];
        echo $Operaciones->get_OperacionesPorAsesor($asesorId);
        break;
        // ----------------------
        // Propietario
        // ----------------------
        case 'get_ListarPropietarios':
            echo $PropietarioController->getPropietarios();
            break;
        
        case 'get_ListarPropietarios_inmueble':
            $inmuble_id =   $data['inmuebleId'];
            echo $PropietarioController->getPropietario_inmueble($inmuble_id);
            break;
    

        case 'get_propietario':
            $id =  $data['id'];
            echo $PropietarioController->getPropietario($id);
            break;
         
        case 'add_propietario':
            $inmuebleId =   $data['inmuebleId'];
            $tipodoc =      $data['tipodoc'];
            $numdoc =       $data['numdoc'];
            $nombre =       $data['nombre'];
            $celular =      $data['celular'];
            $direccion =    $data['direccion'];
            $email =        $data['email'];
            echo $PropietarioController->insertPropietario(
                $tipodoc,
                $numdoc,
                $nombre,
                $celular,
                $direccion,
                $email,
                $inmuebleId);
            break;

        case 'add_propietario_sin_inmueble':
            $tipodoc =      $data['tipodoc'];
            $numdoc =       $data['numdoc'];
            $nombre =       $data['nombre'];
            $celular =      $data['celular'];
            $direccion =    $data['direccion'];
            $email =        $data['email'];
            $inmuebleId =   $data['inmuebleId'] ?? null; // Opcional
            echo $PropietarioController->insertPropietarioSinInmueble(
                $tipodoc,
                $numdoc,
                $nombre,
                $celular,
                $direccion,
                $email,
                $inmuebleId);
            break;
            
        case 'add_asignarPropietario':
            $inmuebleId =    $data['inmuebleId'];
            $idpropietario = $data['idpropietario'];
            echo $PropietarioController->relacionar_conImueble(
                $idpropietario,
                $inmuebleId);
            break;
    
        case 'del_propietarioasignado':
            $id =      $data['idasignacion'];
            echo $PropietarioController->deletePropietarioAsignado($id);
            break;

        case 'upd_propietario':
            $id =           $data['id'];
            $nombre =       $data['nombre'];
            $apellido =     $data['apellido'];
            $dni =          $data['dni'];
            $direccion =    $data['direccion'];
            $email =        $data['email'];
            echo $PropietarioController->updatePropietario(
                $id,
                $tipodoc,
                $numdoc,
                $nombre,
                $celular,
                $direccion,
                $email,
                $inmuebleId);
            break;

        // case 'del_Cliente':
        //     $id =      $data['id'];
        //     echo $ClienteController->updateClienteEstado($id, $estado);
        //     break;

        
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
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
        // Valida si es un número cuando corresponde
        if (is_numeric($value)) {
            return $value + 0; // Devuelve como int o float
        }
        return $value;
    }
}