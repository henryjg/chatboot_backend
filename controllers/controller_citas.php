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
                    cita_celular as celular,
                    citas_procedencia as procedencia,
                    citas_descripcion as descripcion,
                    citas_precio as precio,
                    citas_estado as estado,
                    citas_consultorio as consultorio,
                    cita_preciogeneral as preciogeneral,
                    cita_preciofinal as preciofinal
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

    public function getCitaConHorario($citas_id) {
        $conexion = new Conexion();
        
        $query = "SELECT c.citas_id as id,
                    c.citas_fecha as fecha,
                    c.citas_dni as dni,
                    c.citas_nombre as nombre,
                    c.cita_celular as celular,
                    c.citas_procedencia as procedencia,
                    c.citas_descripcion as descripcion,
                    c.citas_precio as precio,
                    c.citas_estado as estado,
                    c.citas_consultorio as consultorio,
                    c.cita_preciogeneral as preciogeneral,
                    c.cita_preciofinal as preciofinal, 
                    h.hora_fechainicio, 
                    h.hora_fechafin, h.hora_id
                  FROM citas c
                  LEFT JOIN horadiacita hdc ON c.citas_id = hdc.hdc_citaId
                  LEFT JOIN horarios h ON hdc.hdc_horarioId = h.hora_id
                  WHERE c.citas_id = $citas_id";
        
        $result = $conexion->ejecutarConsulta($query);
        
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            response::success($data, 'Cita obtenida correctamente');
        } else {
            response::error('Cita no encontrada');
        }
    }

    public function getCitas() {
        $conexion = new Conexion();
        $query = "SELECT 
                   citas_id as id,
                   citas_fecha as fecha,
                   citas_dni as dni,
                   citas_nombre as nombre,
                   cita_celular as celular,
                   citas_procedencia as procedencia,
                   citas_descripcion as descripcion,
                   citas_precio as precio,
                   citas_estado as estado,
                   citas_consultorio as consultorio,
                   cita_preciogeneral as preciogeneral,
                   cita_preciofinal as preciofinal
                FROM citas 
                ORDER BY citas_fecha DESC, citas_id ASC";
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
        $cita_celular,
        $citas_procedencia,
        $citas_descripcion,
        $citas_precio,
        $citas_estado,
        $citas_consultorio,
        $cita_preciogeneral,
        $cita_preciofinal,
        $horario_id
    ) {
        $conexion = new Conexion();

        // Validar que se proporcione un horario (OBLIGATORIO)
        if ($horario_id === null || $horario_id === '' || $horario_id === 0) {
            response::error('Debe seleccionar un horario para la cita. El horario es obligatorio.');
            return;
        }

        // Verificar si el horario existe
        $queryHorario = "SELECT COUNT(*) as total FROM horarios WHERE hora_id = $horario_id";
        $resultHorario = $conexion->ejecutarConsulta($queryHorario);
        $horarioExiste = $resultHorario->fetch_assoc()['total'] > 0;
        
        if (!$horarioExiste) {
            response::error('El horario seleccionado no existe');
            return;
        }
        
        // Verificar si el horario ya está ocupado para esa fecha (excluyendo la cita actual)
        $queryOcupado = "SELECT COUNT(*) as total 
                        FROM horadiacita hdc
                        INNER JOIN citas c ON hdc.hdc_citaId = c.citas_id
                        WHERE hdc.hdc_horarioId = $horario_id 
                        AND c.citas_fecha = '$citas_fecha' 
                        AND c.citas_estado != 'cancelada'
                        AND c.citas_id != $citas_id";
        $resultOcupado = $conexion->ejecutarConsulta($queryOcupado);
        $horarioOcupado = $resultOcupado->fetch_assoc()['total'] > 0;
        
        if ($horarioOcupado) {
            response::error('El horario seleccionado ya está ocupado para esta fecha. Por favor, elija otro horario.');
            return;
        }

        // Actualizar la cita
        $query = "UPDATE citas SET 
                    citas_fecha = '$citas_fecha',
                    citas_dni = '$citas_dni',
                    citas_nombre = '$citas_nombre',
                    cita_celular = '$cita_celular',
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
            // Actualizar la relación del horario (SIEMPRE se ejecuta porque es obligatorio)
            // Verificar si la cita ya tiene un horario asignado
            $queryExisteRelacion = "SELECT COUNT(*) as total FROM horadiacita WHERE hdc_citaId = $citas_id";
            $resultExisteRelacion = $conexion->ejecutarConsulta($queryExisteRelacion);
            $existeRelacion = $resultExisteRelacion->fetch_assoc()['total'] > 0;
            
            if ($existeRelacion) {
                // Si ya existe una relación, eliminarla primero
                $conexion->save("DELETE FROM horadiacita WHERE hdc_citaId = $citas_id");
            }
            
            // Insertar la nueva relación
            $queryInsertHorario = "INSERT INTO horadiacita (hdc_horarioId, hdc_citaId) VALUES ($horario_id, $citas_id)";
            $resultHorario = $conexion->insertar($queryInsertHorario);
            
            if ($resultHorario > 0) {
                response::success($result, 'Cita y horario actualizados correctamente');
            } else {
                response::error('Cita actualizada pero error al asignar el nuevo horario');
            }
        } else {
            response::error('Error al actualizar la cita');
        }
    }

    public function insertCita(
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
    ) {
        $conexion = new Conexion();
        
        // Validar que se proporcione un horario (OBLIGATORIO)
        if ($horario_id === null || $horario_id === '' || $horario_id === 0) {
            response::error('Debe seleccionar un horario para la cita. El horario es obligatorio.');
            return;
        }
        
        // Verificar si el horario existe
        $queryHorario = "SELECT COUNT(*) as total FROM horarios WHERE hora_id = $horario_id";
        $resultHorario = $conexion->ejecutarConsulta($queryHorario);
        
        if (!$resultHorario) {
            response::error('Error al validar el horario');
            return;
        }
        
        $horarioExiste = $resultHorario->fetch_assoc()['total'] > 0;
        
        if (!$horarioExiste) {
            response::error('El horario seleccionado no existe');
            return;
        }
        
        // Verificar si el horario ya está ocupado para esa fecha
        $queryOcupado = "SELECT COUNT(*) as total 
                        FROM horadiacita hdc
                        INNER JOIN citas c ON hdc.hdc_citaId = c.citas_id
                        WHERE hdc.hdc_horarioId = $horario_id 
                        AND c.citas_fecha = '$citas_fecha' 
                        AND c.citas_estado != 'cancelada'";
        $resultOcupado = $conexion->ejecutarConsulta($queryOcupado);
        
        if (!$resultOcupado) {
            response::error('Error al verificar disponibilidad del horario');
            return;
        }
        
        $horarioOcupado = $resultOcupado->fetch_assoc()['total'] > 0;
        
        if ($horarioOcupado) {
            response::error('El horario seleccionado ya está ocupado para esta fecha. Por favor, elija otro horario.');
            return;
        }
        
        // Insertar la cita
        $query = "INSERT INTO citas (
                    citas_fecha, 
                    citas_dni, 
                    citas_nombre,
                    cita_celular,
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
                    '$cita_celular',
                    '$citas_procedencia',
                    '$citas_descripcion',
                    '$citas_precio',
                    'pendiente',
                    '$citas_consultorio',
                    '$cita_preciogeneral',
                    '$cita_preciofinal'
                  )";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            // Asignar horario (SIEMPRE se ejecuta porque es obligatorio)
            $queryInsertHorario = "INSERT INTO horadiacita (hdc_horarioId, hdc_citaId) VALUES ($horario_id, $result)";
            $resultHorario = $conexion->insertar($queryInsertHorario);
            
            if ($resultHorario > 0) {
                response::success($result, 'Cita creada y horario asignado correctamente');
            } else {
                // Si falla la asignación de horario, eliminar la cita creada
                $conexion->save("DELETE FROM citas WHERE citas_id = $result");
                response::error('Error al asignar el horario. La cita no fue creada.');
            }
        } else {
            response::error('Error al insertar la cita');
        }
    }

    public function deleteCita($id) {
        $conexion = new Conexion();
        
        // Primero eliminar las relaciones de horarios
        $queryDeleteHorario = "DELETE FROM horadiacita WHERE hdc_citaId = $id";
        $conexion->save($queryDeleteHorario);
        
        // Luego eliminar la cita
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

    public function getCitasPorFecha($fecha) {
        $conexion = new Conexion();
        $query = "SELECT 
                   c.citas_id as id,
                   c.citas_fecha as fecha,
                   c.citas_dni as dni,
                   c.citas_nombre as nombre,
                   c.cita_celular as celular,
                   c.citas_procedencia as procedencia,
                   c.citas_descripcion as descripcion,
                   c.citas_precio as precio,
                   c.citas_estado as estado,
                   c.citas_consultorio as consultorio,
                   c.cita_preciogeneral as preciogeneral,
                   c.cita_preciofinal as preciofinal,
                   h.hora_fechainicio as hora_inicio,
                   h.hora_fechafin as hora_fin
                FROM citas c
                LEFT JOIN horadiacita hdc ON c.citas_id = hdc.hdc_citaId
                LEFT JOIN horarios h ON hdc.hdc_horarioId = h.hora_id
                WHERE c.citas_fecha = '$fecha'
                ORDER BY h.hora_fechainicio ASC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $citas = array();
            while ($cita = $result->fetch_assoc()) {
                $citas[] = $cita;
            }
            response::success($citas, 'Citas del día obtenidas correctamente');
        } else {
            response::success(array(), 'No hay citas programadas para esta fecha');
        }
    }

    public function getHorariosDisponibles($fecha) {
        $conexion = new Conexion();
        
        // Obtener todos los horarios disponibles
        $queryHorarios = "SELECT 
                            hora_id as id,
                            hora_fechainicio as hora_inicio,
                            hora_fechafin as hora_fin
                        FROM horarios 
                        ORDER BY hora_fechainicio ASC";
        $resultHorarios = $conexion->ejecutarConsulta($queryHorarios);

        if (!$resultHorarios || $resultHorarios->num_rows == 0) {
            response::error('No se encontraron horarios configurados');
            return;
        }

        // Obtener horarios ya ocupados para esa fecha
        $queryOcupados = "SELECT hdc.hdc_horarioId 
                         FROM horadiacita hdc
                         INNER JOIN citas c ON hdc.hdc_citaId = c.citas_id
                         WHERE c.citas_fecha = '$fecha' AND c.citas_estado != 'cancelada'";
        $resultOcupados = $conexion->ejecutarConsulta($queryOcupados);

        $horariosOcupados = array();
        if ($resultOcupados && $resultOcupados->num_rows > 0) {
            while ($ocupado = $resultOcupados->fetch_assoc()) {
                $horariosOcupados[] = $ocupado['hdc_horarioId'];
            }
        }

        // Procesar horarios disponibles
        $horariosDisponibles = array();
        while ($horario = $resultHorarios->fetch_assoc()) {
            $horarioId = $horario['id'];
            $ocupado = in_array($horarioId, $horariosOcupados);

            $horariosDisponibles[] = array(
                'id' => $horarioId,
                'hora_inicio' => $horario['hora_inicio'],
                'hora_fin' => $horario['hora_fin'],
                'disponible' => !$ocupado,
                'estado' => $ocupado ? 'ocupado' : 'disponible'
            );
        }

        response::success($horariosDisponibles, 'Horarios obtenidos correctamente');
    }

    // Asignar horario a una cita
    public function asignarHorarioCita($cita_id, $horario_id) {
        $conexion = new Conexion();
        
        // Verificar si la cita existe
        $queryCita = "SELECT COUNT(*) as total FROM citas WHERE citas_id = $cita_id";
        $resultCita = $conexion->ejecutarConsulta($queryCita);
        $citaExiste = $resultCita->fetch_assoc()['total'] > 0;
        
        if (!$citaExiste) {
            response::error('La cita no existe');
            return;
        }
        
        // Verificar si el horario existe
        $queryHorario = "SELECT COUNT(*) as total FROM horarios WHERE hora_id = $horario_id";
        $resultHorario = $conexion->ejecutarConsulta($queryHorario);
        $horarioExiste = $resultHorario->fetch_assoc()['total'] > 0;
        
        if (!$horarioExiste) {
            response::error('El horario no existe');
            return;
        }
        
        // Verificar si el horario ya está ocupado
        $queryOcupado = "SELECT COUNT(*) as total FROM horadiacita WHERE hdc_horarioId = $horario_id";
        $resultOcupado = $conexion->ejecutarConsulta($queryOcupado);
        $horarioOcupado = $resultOcupado->fetch_assoc()['total'] > 0;
        
        if ($horarioOcupado) {
            response::error('El horario ya está ocupado');
            return;
        }
        
        // Insertar en horadiacita
        $queryInsert = "INSERT INTO horadiacita (hdc_horarioId, hdc_citaId) VALUES ($horario_id, $cita_id)";
        $result = $conexion->insertar($queryInsert);
        
        if ($result > 0) {
            response::success($result, 'Horario asignado a la cita correctamente');
        } else {
            response::error('Error al asignar el horario a la cita');
        }
    }

    // Función auxiliar para verificar si un horario está disponible
    private function verificarHorarioDisponible($fecha, $horaInicio, $horaFin, $excluirCitaId = null) {
        $conexion = new Conexion();
        $query = "SELECT COUNT(*) as total FROM citas 
                 WHERE citas_fecha = '$fecha' 
                 AND citas_estado != 'cancelada'
                 AND (
                     (hora_inicio <= '$horaInicio' AND hora_fin > '$horaInicio') OR
                     (hora_inicio < '$horaFin' AND hora_fin >= '$horaFin') OR
                     (hora_inicio >= '$horaInicio' AND hora_fin <= '$horaFin')
                 )";
        
        if ($excluirCitaId) {
            $query .= " AND citas_id != $excluirCitaId";
        }

        $result = $conexion->ejecutarConsulta($query);
        
        if ($result) {
            $fila = $result->fetch_assoc();
            return $fila['total'] == 0;
        }
        
        return false;
    }

    // Función auxiliar para verificar si dos horarios se superponen
    private function horariosSeSuperpronen($inicio1, $fin1, $inicio2, $fin2) {
        return ($inicio1 < $fin2 && $fin1 > $inicio2);
    }
}
?>
