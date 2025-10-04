<?php
include_once './utils/response.php';
include_once './config/database.php';

class PagosController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    public function getPago($id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    pago_id as id,
                    pago_tipo as tipo,
                    pago_monto as monto,
                    pago_comentario as comentario,
                    pago_url as url,
                    pago_citaid as cita_id
                FROM pago 
                WHERE pago_id = '$id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $pago = $result->fetch_assoc();
            response::success($pago, 'Consulta de pago exitosa');
        } else {
            response::error('No se encontró el pago');
        }
    }

    public function getPagos() {
        $conexion = new Conexion();
        $query = "SELECT 
                    p.pago_id as id,
                    p.pago_tipo as tipo,
                    p.pago_monto as monto,
                    p.pago_comentario as comentario,
                    p.pago_url as url,
                    p.pago_citaid as cita_id,
                    c.citas_nombre as paciente_nombre,
                    c.citas_fecha as cita_fecha
                FROM pago p
                LEFT JOIN citas c ON p.pago_citaid = c.citas_id
                ORDER BY p.pago_id DESC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $pagos = array();
            while ($pago = $result->fetch_assoc()) {
                $pagos[] = $pago;
            }
            response::success($pagos, 'Lista de pagos obtenida correctamente');
        } else {
            response::error('No se encontraron pagos registrados');
        }
    }

    public function updatePago(
        $pago_id,
        $pago_tipo,
        $pago_monto,
        $pago_comentario,
        $pago_url,
        $pago_citaid
    ) {
        $conexion = new Conexion();
        $query = "UPDATE pago SET 
                    pago_tipo = '$pago_tipo',
                    pago_monto = '$pago_monto',
                    pago_comentario = '$pago_comentario',
                    pago_url = '$pago_url',
                    pago_citaid = '$pago_citaid'
                WHERE pago_id = $pago_id";

        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Pago actualizado correctamente');
        } else {
            response::error('Error al actualizar el pago');
        }
    }

    public function insertPago(
        $pago_tipo,
        $pago_monto,
        $pago_comentario,
        $pago_url,
        $pago_citaid
    ) {
        $conexion = new Conexion();
        
        $query = "INSERT INTO pago (
                    pago_tipo,
                    pago_monto,
                    pago_comentario,
                    pago_url,
                    pago_citaid
                  ) VALUES (
                    '$pago_tipo',
                    '$pago_monto',
                    '$pago_comentario',
                    '$pago_url',
                    '$pago_citaid'
                  )";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Pago insertado correctamente');
        } else {
            response::error('Error al insertar el pago');
        }
    }
          
    public function deletePago($id) {
        $conexion = new Conexion();
        $query = "DELETE FROM pago WHERE pago_id = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Pago eliminado correctamente');
        } else {
            response::error('Error al eliminar el pago');
        }
    }

    // Función para obtener pagos por cita
    public function getPagosPorCita($cita_id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    p.pago_id as id,
                    p.pago_tipo as tipo,
                    p.pago_monto as monto,
                    p.pago_comentario as comentario,
                    p.pago_url as url,
                    p.pago_citaid as cita_id,
                    c.citas_nombre as paciente_nombre,
                    c.citas_fecha as cita_fecha
                FROM pago p
                LEFT JOIN citas c ON p.pago_citaid = c.citas_id
                WHERE p.pago_citaid = '$cita_id'
                ORDER BY p.pago_id DESC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $pagos = array();
            while ($pago = $result->fetch_assoc()) {
                $pagos[] = $pago;
            }
            response::success($pagos, 'Pagos de la cita obtenidos correctamente');
        } else {
            response::error('No se encontraron pagos para esta cita');
        }
    }

    // Función para obtener total de pagos por cita
    public function getTotalPagosPorCita($cita_id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    COUNT(*) as total_pagos,
                    SUM(CAST(pago_monto AS DECIMAL(10,2))) as total_monto
                FROM pago 
                WHERE pago_citaid = '$cita_id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $total = $result->fetch_assoc();
            response::success($total, 'Total de pagos obtenido correctamente');
        } else {
            response::error('Error al obtener el total de pagos');
        }
    }

    // Función para obtener pagos por tipo
    public function getPagosPorTipo($tipo) {
        $conexion = new Conexion();
        $query = "SELECT 
                    p.pago_id as id,
                    p.pago_tipo as tipo,
                    p.pago_monto as monto,
                    p.pago_comentario as comentario,
                    p.pago_url as url,
                    p.pago_citaid as cita_id,
                    c.citas_nombre as paciente_nombre,
                    c.citas_fecha as cita_fecha
                FROM pago p
                LEFT JOIN citas c ON p.pago_citaid = c.citas_id
                WHERE p.pago_tipo = '$tipo'
                ORDER BY p.pago_id DESC";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $pagos = array();
            while ($pago = $result->fetch_assoc()) {
                $pagos[] = $pago;
            }
            response::success($pagos, "Pagos de tipo '$tipo' obtenidos correctamente");
        } else {
            response::error("No se encontraron pagos de tipo '$tipo'");
        }
    }

}
?>