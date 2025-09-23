<?php

    /* DETECCIÓN AUTOMÁTICA DE ENTORNO --------------------------------------------------- */ 
    
    // Detectar si estamos en servidor local o en producción
    $esLocal = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1' || strpos($_SERVER['SERVER_NAME'], 'localhost') !== false);
    
    if ($esLocal) {
        // CONFIGURACIÓN LOCAL (XAMPP)
        define("SERVER","localhost");
        define("USER","root");
        define("PASS","");
        define("DB","hospitaldb"); 
        
        
    } else {
        // CONFIGURACIÓN PARA HOSTINGER (PRODUCCIÓN)
        define("SERVER","localhost");
        define("USER","clinicadolor");
        define("PASS","Camila30"); // Tu contraseña de la imagen
        define("DB","bdclinica");
        

    } 


    /** Codificaci贸n de caracteres para la base de datos. */
    define('DB_CHARSET', 'utf8');
    define("DEBUG","true");
    date_default_timezone_set("America/Lima");

    define ("CLAVE", "grupomv@deTPD_fxfDc3c1!2*3s@zr");


//FUNCIONES **************************************************
function generaURLSegura ($variable, $valor){
    return $variable . "=" . md5(CLAVE . $valor);
}

function cleanfild($valor) {
    $valor = str_ireplace(['SELECT', 'UPDATE', 'DELETE', 'COPY', 'DROP', 'DUMP', 'OR', 'LIKE'], '', $valor);
    $valor = str_ireplace(['<script>', '</script>', '<?php', '?>', '–', '^', '[', ']', '', '!', '¡', '?', '=', '&'], '', $valor);
    $valor = str_replace('%', '', $valor);
    $valor = addslashes($valor);
    return $valor;
}

function FechaFormateada($FechaStamp){ 
    $ano = date('Y',$FechaStamp);
    $mes = date('n',$FechaStamp);
    $dia = date('d',$FechaStamp);
    $diasemana = date('w',$FechaStamp);         
    $diassemanaN= array("Dom","Lun","Mar","Mié",
                        "Jue","Vie","Sáb"); $mesesN=array(1=>"Ene","Feb","Mar","Abr","May","Jun","Jul",
                        "Ago","Sep","Oct","Nov","Dic");
    return $diassemanaN[$diasemana]." <br/> $dia ". $mesesN[$mes];
}

//Formateando Fecha
function FechaFormateada_meslargo($FechaStamp){ 
    $ano = date('Y',$FechaStamp);
    $mes = date('n',$FechaStamp);
    $dia = date('d',$FechaStamp);
    $diasemana = date('w',$FechaStamp);
   
    $diassemanaN= array("Domingo","Lunes","Martes","Miércoles",
                        "Jueves","Viernes","Sábado"); $mesesN=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
                   "Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    return $diassemanaN[$diasemana].", $dia de ". $mesesN[$mes]." de ".$ano;
}


?>