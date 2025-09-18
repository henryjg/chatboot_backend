<?php
include_once './utils/response.php';
include_once './config/database.php';

class ControladorUbigeo {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    // ------------------------------------------------------
    public function get_ubigeo() {
        $conexion = new Conexion();
        $query =    "select idDist as id, 
                        concat(departamento,' - ',provincia,' - ',distrito) as ubigeo,
                        departamento, provincia, distrito
                    from ubdistrito d
                    inner join ubprovincia pro on pro.idProv= d.idProv
                    inner join ubdepartamento dep on dep.idDepa = pro.idDepa
                    where dep.departamento='PIURA'
                    order by departamento asc";
        $result = $conexion->ejecutarConsulta($query);
        if ($result && $result->num_rows > 0) {
            $ubigeos = array();
            while ($ubigeo = $result->fetch_assoc()) {
                $ubigeos[] = $ubigeo;
            }
            response::success($ubigeos, 'Lista de inmuebles obtenida correctamente');
        } else {
            response::error('No se encontraron inmuebles registrados');
        }
    }


    // ------------------------------------------------------
    public function get_ubigeo_dataInmuebles() {
        $conexion = new Conexion();
        $query =    "select idDist as id, 
                        concat(departamento,' - ',provincia,' - ',distrito) as ubigeo,
                        departamento, provincia, distrito
                    from ubdistrito d
                    inner join ubprovincia pro on pro.idProv= d.idProv
                    inner join ubdepartamento dep on dep.idDepa = pro.idDepa
                    where dep.departamento='PIURA' and
                        idDist IN (SELECT distinct inmu_ubigeo FROM inmueble WHERE inmu_ubigeo NOT IN ('',0))
                    order by departamento asc";
        $result = $conexion->ejecutarConsulta($query);
        if ($result && $result->num_rows > 0) {
            $ubigeos = array();
            while ($ubigeo = $result->fetch_assoc()) {
                $ubigeos[] = $ubigeo;
            }
            response::success($ubigeos, 'Lista de inmuebles obtenida correctamente');
        } else {
            response::error('No se encontraron inmuebles registrados');
        }
    }
   
}
?>
