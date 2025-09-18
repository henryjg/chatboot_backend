<?php


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