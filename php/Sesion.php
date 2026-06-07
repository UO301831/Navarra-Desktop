<?php
require_once __DIR__ . "/BaseDatos.php";

// Clase que controla el inicio y cierre de sesion del usuario
class Sesion {

    // Arranca la sesion de PHP si no estaba ya iniciada
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Comprueba email y contraseña; si son correctos guarda al usuario en la sesion
    public function login($email, $contrasena) {
        $bd = new BaseDatos();
        $consulta = $bd->getConexion()->prepare(
            "SELECT id_usuario, nombre, contrasena FROM usuario WHERE email = ?"
        );
        $consulta->bind_param("s", $email);
        $consulta->execute();
        $usuario = $consulta->get_result()->fetch_assoc();
        $bd->cerrar();

        if ($usuario && password_verify($contrasena, $usuario["contrasena"])) {
            $_SESSION["id_usuario"] = $usuario["id_usuario"];
            $_SESSION["nombre"] = $usuario["nombre"];
            return true;
        }
        return false;
    }

    // Cierra la sesion del usuario
    public function logout() {
        session_destroy();
    }

    // Dice si hay un usuario con la sesion iniciada
    public function estaLogueado() {
        return isset($_SESSION["id_usuario"]);
    }

    // Devuelve el id del usuario que ha iniciado sesion
    public function getId() {
        return $_SESSION["id_usuario"];
    }

    // Devuelve el nombre del usuario que ha iniciado sesion
    public function getNombre() {
        return $_SESSION["nombre"];
    }
}
?>
