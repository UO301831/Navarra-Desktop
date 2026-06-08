<?php
require_once __DIR__ . "/CargadorCSV.php";

// Crea la base de datos y sus tablas (ejecutando navarra.sql) y carga los datos
// de ejemplo de los archivos CSV. Lo usa BaseDatos la primera vez que se accede,
// cuando la base de datos todavia no existe.
class Instalador {

    private $conexion;
    private $nombreBD;

    public function __construct($conexion, $nombreBD) {
        $this->conexion = $conexion;
        $this->nombreBD = $nombreBD;
    }

    // Crea el esquema y despues carga los datos
    public function instalar() {
        $this->crearEsquema();
        $this->cargarDatos();
    }

    // Ejecuta el script navarra.sql (crea la BD, las tablas y las claves foraneas)
    private function crearEsquema() {
        $sql = file_get_contents(__DIR__ . "/navarra.sql");
        $this->conexion->multi_query($sql);

        // Agotamos todos los resultados del script para dejar libre la conexion
        while ($this->conexion->more_results() && $this->conexion->next_result()) {
        }

        $this->conexion->select_db($this->nombreBD);
    }

    // Inserta los datos de ejemplo desde los archivos CSV (primero las tablas
    // sin claves foraneas para respetar las dependencias)
    private function cargarDatos() {
        $cargador = new CargadorCSV($this->conexion);
        $cargador->cargarTabla("tipo_recurso", __DIR__ . "/tipo_recurso.csv");
        $cargador->cargarTabla("localidad", __DIR__ . "/localidad.csv");
        $cargador->cargarTabla("recurso", __DIR__ . "/recurso.csv");
    }
}
?>
