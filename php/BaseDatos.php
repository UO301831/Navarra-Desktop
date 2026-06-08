<?php
require_once __DIR__ . "/Instalador.php";

// Clase que se encarga de la conexion con la base de datos
class BaseDatos {
    const SERVIDOR = "127.0.0.1";
    const USUARIO = "DBUSER2026";
    const PASSWORD = "DBPWD2026";
    const NOMBRE = "UO301831_DB";

    private $conexion;

    // Abre la conexion al crear el objeto. Si la base de datos aun no existe,
    // la crea e inicializa automaticamente (tablas y datos de ejemplo).
    public function __construct() {
        mysqli_report(MYSQLI_REPORT_OFF);

        // Nos conectamos al servidor sin elegir BD, para poder crearla si falta
        $this->conexion = new mysqli(self::SERVIDOR, self::USUARIO, self::PASSWORD);
        if ($this->conexion->connect_errno) {
            die("Error de conexión con la base de datos.");
        }
        $this->conexion->set_charset("utf8mb4");

        // Si la base de datos no existe todavia, se crea e inicializa una sola vez
        if (!$this->conexion->select_db(self::NOMBRE)) {
            $instalador = new Instalador($this->conexion, self::NOMBRE);
            $instalador->instalar();
            $this->conexion->select_db(self::NOMBRE);
        }
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
