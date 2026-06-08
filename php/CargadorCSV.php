<?php
require_once __DIR__ . "/BaseDatos.php";

// Carga los datos de los archivos .csv en las tablas de la base de datos.
// No se enlaza desde el menú: herramienta para inicializar la base de datos.
class CargadorCSV {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Inserta en una tabla las filas de su .csv (la primera linea son las columnas)
    public function cargarTabla($tabla, $rutaCsv) {
        $fichero = fopen($rutaCsv, "r");
        $columnas = fgetcsv($fichero);
        $interrogantes = implode(", ", array_fill(0, count($columnas), "?"));

        $sentencia = $this->conexion->prepare(
            "INSERT INTO $tabla (" . implode(", ", $columnas) . ") VALUES ($interrogantes)"
        );

        while (($fila = fgetcsv($fichero)) !== false) {
            if (count($fila) !== count($columnas)) {
                continue;
            }
            $sentencia->bind_param(str_repeat("s", count($fila)), ...$fila);
            $sentencia->execute();
        }
        fclose($fichero);
    }
}

// Carga todos los csv en orden (primero las tablas sin claves foraneas)
$bd = new BaseDatos();
$cargador = new CargadorCSV($bd->getConexion());
$cargador->cargarTabla("tipo_recurso", __DIR__ . "/tipo_recurso.csv");
$cargador->cargarTabla("localidad", __DIR__ . "/localidad.csv");
$cargador->cargarTabla("recurso", __DIR__ . "/recurso.csv");
$bd->cerrar();

echo "Base de datos inicializada con los datos de los archivos CSV.";
?>
