<?php
require_once __DIR__ . "/BaseDatos.php";

// Clase para registrar nuevos usuarios
class Usuario {

    // Crea un usuario nuevo; devuelve false si el email ya estaba registrado
    public function registrar($nombre, $apellidos, $email, $contrasena, $telefono) {
        $bd = new BaseDatos();
        $conexion = $bd->getConexion();

        // Miramos si ya existe un usuario con ese email
        $comprobar = $conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
        $comprobar->bind_param("s", $email);
        $comprobar->execute();
        if ($comprobar->get_result()->fetch_assoc()) {
            $bd->cerrar();
            return false;
        }

        // Guardamos la contraseña cifrada por seguridad
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $insertar = $conexion->prepare(
            "INSERT INTO usuario (nombre, apellidos, email, contrasena, telefono) VALUES (?, ?, ?, ?, ?)"
        );
        $insertar->bind_param("sssss", $nombre, $apellidos, $email, $hash, $telefono);
        $insertar->execute();
        $bd->cerrar();
        return true;
    }
}
?>
