<?php
include_once './utils/response.php';
include_once './config/database.php';

class CitasController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    public function getCita($id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    citas_id as id,
                    citas_fecha as fecha,
                    citas_dni as dni,
                    citas_nombre as nombre,
                    citas_procedencia as procedencia,
                    citas_descripcion as descripcion,
                    citas_precio as precio,
                    citas_estado as estado,
                    citas_consultorio as consultorio,
                    cita_preciogeneral as precio_general,
                    cita_preciofinal as precio_final
                FROM citas 
                WHERE citas_id = '$id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $cita = $result->fetch_assoc();
            response::success($cita, 'Consulta de cita exitosa');
        } else {
            response::error('No se encontró la cita');
        }
    }

    public function getCitas() {
        $conexion = new Conexion();
        $query = "SELECT 
                    citas_id as id,
                    citas_fecha as fecha,
                    citas_dni as dni,
                    citas_nombre as nombre,
                    citas_procedencia as procedencia,
                    citas_descripcion as descripcion,
                    citas_precio as precio,
                    citas_estado as estado,
                    citas_consultorio as consultorio,
                    cita_preciogeneral as precio_general,
                    cita_preciofinal as precio_final
                FROM citas";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $citas = array();
            while ($cita = $result->fetch_assoc()) {
                $citas[] = $cita;
            }
            response::success($citas, 'Lista de citas obtenida correctamente');
        } else {
            response::error('No se encontraron citas registradas');
        }
    }

    public function updateCita(
        $citas_id,
        $citas_fecha,
        $citas_dni,
        $citas_nombre,
        $citas_procedencia,
        $citas_descripcion,
        $citas_precio,
        $citas_estado,
        $citas_consultorio,
        $cita_preciogeneral,
        $cita_preciofinal
    ) {
        $conexion = new Conexion();
        $query = "UPDATE citas SET 
                    citas_fecha = '$citas_fecha',
                    citas_dni = '$citas_dni',
                    citas_nombre = '$citas_nombre',
                    citas_procedencia = '$citas_procedencia',
                    citas_descripcion = '$citas_descripcion',
                    citas_precio = '$citas_precio',
                    citas_estado = '$citas_estado',
                    citas_consultorio = '$citas_consultorio',
                    cita_preciogeneral = '$cita_preciogeneral',
                    cita_preciofinal = '$cita_preciofinal'
                WHERE citas_id = $citas_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Cita actualizada correctamente');
        } else {
            response::error('Error al actualizar la cita');
        }
    }

    public function insertCita(
        $citas_fecha,
        $citas_dni,
        $citas_nombre,
        $citas_procedencia,
        $citas_descripcion,
        $citas_precio,
        $citas_consultorio,
        $cita_preciogeneral,
        $cita_preciofinal
    ) {
        $conexion = new Conexion();
        
        $query = "INSERT INTO citas (
                    citas_fecha,
                    citas_dni,
                    citas_nombre,
                    citas_procedencia,
                    citas_descripcion,
                    citas_precio,
                    citas_estado,
                    citas_consultorio,
                    cita_preciogeneral,
                    cita_preciofinal
                  ) VALUES (
                    '$citas_fecha',
                    '$citas_dni',
                    '$citas_nombre',
                    '$citas_procedencia',
                    '$citas_descripcion',
                    '$citas_precio',
                    'Pendiente',
                    '$citas_consultorio',
                    '$cita_preciogeneral',
                    '$cita_preciofinal'
                  )";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Cita insertada correctamente');
        } else {
            response::error('Error al insertar la cita');
        }
    }
          
    public function deleteCita($id) {
        $conexion = new Conexion();
        $query = "DELETE FROM citas WHERE citas_id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Cita eliminada correctamente');
        } else {
            response::error('Error al eliminar la cita');
        }
    }

    public function updateEstado($id, $citas_estado) {
        $conexion = new Conexion();
        $query = "UPDATE citas SET 
                    citas_estado = '$citas_estado'
                WHERE citas_id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Estado actualizado correctamente');
        } else {
            response::error('Error al actualizar el estado');
        }
    }

    // Función para obtener horarios disponibles por fecha
    public function getHorariosDisponibles($fecha) {
        $conexion = new Conexion();
        
        // Obtener todos los horarios del día con información de si están ocupados
        $queryHorarios = "SELECT 
                            h.hora_id as id,
                            h.hora_fechainicio as inicio,
                            h.hora_fechafin as fin,
                            h.hora_citaid as cita_id,
                            c.citas_id as cita_existe,
                            c.citas_estado as cita_estado
                        FROM horarios h
                        LEFT JOIN citas c ON h.hora_citaid = c.citas_id 
                            AND c.citas_estado IN ('Pendiente', 'Confirmada', 'En Proceso')
                        WHERE DATE(h.hora_fechainicio) = '$fecha'
                        ORDER BY h.hora_fechainicio";
        $resultHorarios = $conexion->ejecutarConsulta($queryHorarios);
        
        if ($resultHorarios && $resultHorarios->num_rows > 0) {
            $horariosDisponibles = array();
            while ($horario = $resultHorarios->fetch_assoc()) {
                // Generar slots de 15 minutos entre inicio y fin
                $inicio = new DateTime($horario['inicio']);
                $fin = new DateTime($horario['fin']);
                
                // Verificar si este horario específico está ocupado
                $estaOcupado = ($horario['cita_existe'] !== null);
                
                while ($inicio < $fin) {
                    $horarioFormateado = $inicio->format('H:i');
                    
                    // Solo agregar si no está ocupado
                    if (!$estaOcupado) {
                        $horariosDisponibles[] = array(
                            'horario' => $horarioFormateado,
                            'fecha' => $fecha,
                            'disponible' => true,
                            'horario_id' => $horario['id']
                        );
                    }
                    
                    // Agregar 15 minutos para el siguiente slot
                    $inicio->add(new DateInterval('PT15M'));
                }
            }
            
            response::success($horariosDisponibles, 'Horarios disponibles obtenidos correctamente');
        } else {
            response::error('No hay horarios configurados para esta fecha');
        }
    }

    // Función para verificar disponibilidad de un horario específico
    public function verificarDisponibilidad($fecha, $horario) {
        $conexion = new Conexion();
        
        // Verificar que el horario esté dentro de los horarios de atención configurados
        // y que no esté ocupado por una cita
        $queryHorarios = "SELECT 
                            h.hora_id,
                            h.hora_citaid,
                            c.citas_id as cita_existe,
                            c.citas_estado
                        FROM horarios h
                        LEFT JOIN citas c ON h.hora_citaid = c.citas_id 
                            AND c.citas_estado IN ('Pendiente', 'Confirmada', 'En Proceso')
                        WHERE DATE(h.hora_fechainicio) = '$fecha'
                        AND TIME('$horario:00') >= TIME(h.hora_fechainicio)
                        AND TIME('$horario:00') < TIME(h.hora_fechafin)";
        $resultHorarios = $conexion->ejecutarConsulta($queryHorarios);
        
        if ($resultHorarios && $resultHorarios->num_rows > 0) {
            $rowHorarios = $resultHorarios->fetch_assoc();
            
            // Si existe el horario pero no tiene cita asignada, está disponible
            if ($rowHorarios['cita_existe'] === null) {
                response::success(array('disponible' => true), 'Horario disponible');
            } else {
                response::success(array('disponible' => false), 'Horario ocupado por cita existente');
            }
        } else {
            response::success(array('disponible' => false), 'Horario fuera del rango de atención o no configurado');
        }
    }

    // Función para obtener horarios disponibles para el chatbot (formato simple)
    public function getHorariosParaChatbot($fecha) {
        $conexion = new Conexion();
        
        // Obtener todos los horarios del día que no estén ocupados
        $queryHorarios = "SELECT 
                            h.hora_fechainicio as inicio,
                            h.hora_fechafin as fin,
                            c.citas_id as cita_existe
                        FROM horarios h
                        LEFT JOIN citas c ON h.hora_citaid = c.citas_id 
                            AND c.citas_estado IN ('Pendiente', 'Confirmada', 'En Proceso')
                        WHERE DATE(h.hora_fechainicio) = '$fecha'
                        AND c.citas_id IS NULL
                        ORDER BY h.hora_fechainicio";
        $resultHorarios = $conexion->ejecutarConsulta($queryHorarios);
        
        $horariosDisponibles = array();
        if ($resultHorarios && $resultHorarios->num_rows > 0) {
            while ($horario = $resultHorarios->fetch_assoc()) {
                $inicio = new DateTime($horario['inicio']);
                $fin = new DateTime($horario['fin']);
                
                while ($inicio < $fin) {
                    $horarioFormateado = $inicio->format('H:i');
                    $horariosDisponibles[] = $horarioFormateado;
                    $inicio->add(new DateInterval('PT15M'));
                }
            }
        }
        
        response::success($horariosDisponibles, 'Horarios disponibles para chatbot');
    }

    // Función para asignar una cita a un horario específico
    public function asignarCitaAHorario($citas_id, $horario_id) {
        $conexion = new Conexion();
        
        // Verificar que el horario no esté ocupado
        $queryVerificar = "SELECT h.hora_citaid, c.citas_id as cita_existe
                          FROM horarios h
                          LEFT JOIN citas c ON h.hora_citaid = c.citas_id 
                              AND c.citas_estado IN ('Pendiente', 'Confirmada', 'En Proceso')
                          WHERE h.hora_id = $horario_id";
        $resultVerificar = $conexion->ejecutarConsulta($queryVerificar);
        
        if ($resultVerificar && $resultVerificar->num_rows > 0) {
            $row = $resultVerificar->fetch_assoc();
            if ($row['cita_existe'] !== null) {
                response::error('El horario ya está ocupado');
                return;
            }
        }
        
        // Asignar la cita al horario
        $queryAsignar = "UPDATE horarios SET hora_citaid = $citas_id WHERE hora_id = $horario_id";
        $result = $conexion->save($queryAsignar);
        
        if ($result > 0) {
            response::success($result, 'Cita asignada al horario correctamente');
        } else {
            response::error('Error al asignar la cita al horario');
        }
    }

    // Función para liberar un horario (quitar la cita asignada)
    public function liberarHorario($horario_id) {
        $conexion = new Conexion();
        
        $query = "UPDATE horarios SET hora_citaid = NULL WHERE hora_id = $horario_id";
        $result = $conexion->save($query);
        
        if ($result > 0) {
            response::success($result, 'Horario liberado correctamente');
        } else {
            response::error('Error al liberar el horario');
        }
    }

    // Función para generar horarios de atención para una fecha específica
    public function generarHorariosAtencion($fecha, $horario_manana_inicio = '07:00', $horario_manana_fin = '13:00', $horario_tarde_inicio = '16:00', $horario_tarde_fin = '19:00') {
        $conexion = new Conexion();
        
        // Verificar si ya existen horarios para esta fecha
        $queryVerificar = "SELECT COUNT(*) as total FROM horarios WHERE DATE(hora_fechainicio) = '$fecha'";
        $resultVerificar = $conexion->ejecutarConsulta($queryVerificar);
        $row = $resultVerificar->fetch_assoc();
        
        if ($row['total'] > 0) {
            response::error('Ya existen horarios configurados para esta fecha');
            return;
        }
        
        $horariosCreados = 0;
        
        // Crear horarios de mañana
        $queryManana = "INSERT INTO horarios (hora_fechainicio, hora_fechafin, hora_citaid) VALUES 
                       ('$fecha $horario_manana_inicio:00', '$fecha $horario_manana_fin:00', NULL)";
        if ($conexion->insertar($queryManana) > 0) {
            $horariosCreados++;
        }
        
        // Crear horarios de tarde
        $queryTarde = "INSERT INTO horarios (hora_fechainicio, hora_fechafin, hora_citaid) VALUES 
                      ('$fecha $horario_tarde_inicio:00', '$fecha $horario_tarde_fin:00', NULL)";
        if ($conexion->insertar($queryTarde) > 0) {
            $horariosCreados++;
        }
        
        if ($horariosCreados > 0) {
            response::success($horariosCreados, "Se crearon $horariosCreados bloques de horarios para el día $fecha");
        } else {
            response::error('Error al crear los horarios de atención');
        }
    }

    // Función para obtener todos los horarios configurados
    public function getHorariosConfigurados($fecha = null) {
        $conexion = new Conexion();
        
        $whereClause = $fecha ? "WHERE DATE(hora_fechainicio) = '$fecha'" : "";
        
        $query = "SELECT 
                    hora_id as id,
                    hora_fechainicio as inicio,
                    hora_fechafin as fin,
                    hora_citaid as cita_id,
                    DATE(hora_fechainicio) as fecha
                FROM horarios 
                $whereClause
                ORDER BY hora_fechainicio";
        
        $result = $conexion->ejecutarConsulta($query);
        
        if ($result && $result->num_rows > 0) {
            $horarios = array();
            while ($horario = $result->fetch_assoc()) {
                $horarios[] = $horario;
            }
            response::success($horarios, 'Horarios configurados obtenidos correctamente');
        } else {
            response::error('No hay horarios configurados' . ($fecha ? " para la fecha $fecha" : ""));
        }
    }

}
?>