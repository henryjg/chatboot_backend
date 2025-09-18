<?php 
class Session {
    private $authenticated = false;
    private $idpersona;
    private $nombre;
    private $rol;
    private $user;

    public function __construct() {
        session_start(); // Iniciar la sesión
        $this->loadSessionData(); // Cargar datos de sesión
    }

    private function loadSessionData() {
        // Cargar datos de sesión si están presentes
        if ($this->isSessionActive()) {
            $this->authenticated = true;
            $this->idpersona = $_SESSION['idpersona'] ?? null;
            $this->nombre = $_SESSION['nombre'] ?? null;
            $this->rol = $_SESSION['rol'] ?? null;
            $this->user = $_SESSION['user'] ?? null;
        } else {
            $this->authenticated = false;
        }
    }

    public function isSessionActive() {
        // Verificar si la sesión está activa
       return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }

    public function iniciarSesion($idpersona, $nombre, $rol, $user) {
        // Iniciar una nueva sesión
        session_regenerate_id(true); // Prevenir fijación de sesión
        $this->authenticated = true;
        $this->idpersona = $idpersona;
        $this->nombre = $nombre;
        $this->rol = $rol;
        $this->user = $user;

        $_SESSION['authenticated'] = true;
        $_SESSION['idpersona'] = $idpersona;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['rol'] = $rol;
        $_SESSION['user'] = $user;
    }

    public function cerrarSesion() {
        // Cerrar la sesión
        session_unset(); // Eliminar todas las variables de la sesión
        session_destroy(); // Destruir la sesión
        $this->authenticated = false; // Restablecer estado de autenticación
    }

    public function getIdPersona() {
        return $this->idpersona;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getRol() {
        return $this->rol;
    }

    public function getUser() {
        return $this->user;
    }
}
?>