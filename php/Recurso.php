<?php
require_once __DIR__ . "/BaseDatos.php";

// Clase para consultar los recursos turisticos
class Recurso {

    // Devuelve una lista con todos los recursos
    public function listar() {
        $bd = new BaseDatos();
        $sql = "SELECT r.id_recurso, r.nombre, t.nombre AS tipo, l.nombre AS localidad,
                       r.plazas, r.fecha_inicio, r.fecha_fin, r.precio, r.descripcion
                FROM recurso r
                JOIN tipo_recurso t ON r.id_tipo = t.id_tipo
                JOIN localidad l ON r.id_localidad = l.id_localidad
                ORDER BY r.nombre";
        $resultado = $bd->consultar($sql);

        // Metemos cada fila en un array para devolverlo
        $recursos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $recursos[] = $fila;
        }
        $bd->cerrar();
        return $recursos;
    }

    // Devuelve un recurso buscado por su id (o null si no existe)
    public function obtener($id) {
        $bd = new BaseDatos();
        $consulta = $bd->getConexion()->prepare(
            "SELECT r.id_recurso, r.nombre, t.nombre AS tipo, l.nombre AS localidad,
                    r.plazas, r.fecha_inicio, r.fecha_fin, r.precio, r.descripcion
             FROM recurso r
             JOIN tipo_recurso t ON r.id_tipo = t.id_tipo
             JOIN localidad l ON r.id_localidad = l.id_localidad
             WHERE r.id_recurso = ?"
        );
        $consulta->bind_param("i", $id);
        $consulta->execute();
        $recurso = $consulta->get_result()->fetch_assoc();
        $bd->cerrar();
        return $recurso;
    }
}
?>
