<?php
// Clase que se encarga de la conexion con la base de datos
class BaseDatos {
    const SERVIDOR = "127.0.0.1";
    const USUARIO = "DBUSER2026";
    const PASSWORD = "DBPWD2026";
    const NOMBRE = "UO301831_DB";

    private $conexion;

    // Abre la conexion con la base de datos al crear el objeto
    public function __construct() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->conexion = new mysqli(self::SERVIDOR, self::USUARIO, self::PASSWORD, self::NOMBRE);
        if ($this->conexion->connect_errno) {
            die("Error de conexión con la base de datos.");
        }
        $this->conexion->set_charset("utf8mb4");
    }

    // Devuelve la conexion para poder hacer consultas desde otras clases
    public function getConexion() {
        return $this->conexion;
    }

    // Ejecuta una consulta SELECT y devuelve el resultado
    public function consultar($sql) {
        return $this->conexion->query($sql);
    }

    // Cierra la conexion con la base de datos
    public function cerrar() {
        $this->conexion->close();
    }
}
?>
