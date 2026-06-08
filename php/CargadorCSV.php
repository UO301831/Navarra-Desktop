<?php
// Carga los datos de los archivos .csv en las tablas de la base de datos.
// Es una herramienta de inicializacion (la usa el Instalador); no se enlaza desde el menu.
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
?>
