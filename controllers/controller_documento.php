<?php
include_once './utils/response.php';
include_once './config/database.php';

class DocumentosController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }

    public function getDocumento($id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    id_documento as id,
                    documento_nombre as nombre,
                    documento_descripcion as descripcion,
                    documento_fecharegistro as fecha_registro,
                    url_documento as url,
                    id_cliente
                FROM documentos 
                WHERE id_documento = '$id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $documento = $result->fetch_assoc();
            response::success($documento, 'Consulta de documento exitosa');
        } else {
            response::error('No se encontró el documento');
        }
    }

    public function getDocumentos() {
        $conexion = new Conexion();
        $query = "SELECT 
                    id_documento as id,
                    documento_nombre as nombre,
                    documento_descripcion as descripcion,
                    documento_fecharegistro as fecha_registro,
                    url_documento as url,
                    id_cliente
                FROM documentos";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $documentos = array();
            while ($documento = $result->fetch_assoc()) {
                $documentos[] = $documento;
            }
            response::success($documentos, 'Lista de documentos obtenida correctamente');
        } else {
            response::error('No se encontraron documentos registrados');
        }
    }

    public function insertDocumento(
        $documento_nombre,
        $documento_descripcion,
        $url_documento,
        $id_cliente
    ) {
        $conexion = new Conexion();
        $fecha_registro = date('Y-m-d H:i:s'); // Generar fecha actual para el registro
        $query = "INSERT INTO documentos (
                    documento_nombre, 
                    documento_descripcion, 
                    documento_fecharegistro, 
                    url_documento,
                    id_cliente
                  ) VALUES (
                    '$documento_nombre', 
                    '$documento_descripcion', 
                    '$fecha_registro', 
                    '$url_documento',
                    '$id_cliente'
                  )";
        $result = $conexion->insertar($query);

        if ($result > 0) {
            response::success($result, 'Documento insertado correctamente');
        } else {
            response::error('Error al insertar el documento');
        }
    }

    public function updateDocumento(
        $id_documento,
        $documento_nombre,
        $documento_descripcion,
        $url_documento,
        $id_cliente
    ) {
        $conexion = new Conexion();
        $query = "UPDATE documentos SET 
                    documento_nombre = '$documento_nombre',
                    documento_descripcion = '$documento_descripcion',
                    url_documento = '$url_documento',
                    id_cliente = '$id_cliente'
                WHERE id_documento = $id_documento";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Documento actualizado correctamente');
        } else {
            response::error('Error al actualizar el documento');
        }
    }

    public function deleteDocumento($id) {
        $conexion = new Conexion();
        $query = "DELETE FROM documentos WHERE id_documento = $id";
        $result = $conexion->save($query);

        if ($result > 0) {
            response::success($result, 'Documento eliminado correctamente');
        } else {
            response::error('Error al eliminar el documento');
        }
    }

    public function getDocumentosPorCliente($cliente_id) {
        $conexion = new Conexion();
        $query = "SELECT 
                    d.id_documento as id,
                    d.documento_nombre as nombre,
                    d.documento_descripcion as descripcion,
                    d.documento_fecharegistro as fecha_registro,
                    d.url_documento as url,
                    d.id_cliente
                FROM documentos d
                WHERE d.id_cliente = '$cliente_id'";
        $result = $conexion->ejecutarConsulta($query);

        if ($result && $result->num_rows > 0) {
            $documentos = array();
            while ($documento = $result->fetch_assoc()) {
                $documentos[] = $documento;
            }
            response::success($documentos, 'Documentos del cliente obtenidos correctamente');
        } else {
            response::error('No se encontraron documentos para el cliente');
        }
    }
}
?>