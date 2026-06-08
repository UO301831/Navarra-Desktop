<?php
require_once __DIR__ . "/BaseDatos.php";

// Clase para crear, listar y anular las reservas de los usuarios
class Reserva {

    // Crea una reserva para un rango de fechas y un numero de personas.
    // Descuenta las personas de las plazas del recurso. Devuelve false si no quedan plazas
    public function crear($idUsuario, $idRecurso, $fechaInicio, $fechaFin, $numPersonas, $presupuesto) {
        $bd = new BaseDatos();
        $conexion = $bd->getConexion();
        $conexion->begin_transaction();

        // Resta las plazas solo si hay suficientes disponibles para esas personas
        $restar = $conexion->prepare(
            "UPDATE recurso SET plazas = plazas - ? WHERE id_recurso = ? AND plazas >= ?"
        );
        $restar->bind_param("iii", $numPersonas, $idRecurso, $numPersonas);
        $restar->execute();

        if ($restar->affected_rows === 0) {
            $conexion->rollback();
            $bd->cerrar();
            return false;
        }

        // Guardamos la reserva con sus fechas y personas
        $guardar = $conexion->prepare(
            "INSERT INTO reserva (id_usuario, id_recurso, fecha_reserva, fecha_inicio, fecha_fin, num_personas, presupuesto, estado)
             VALUES (?, ?, NOW(), ?, ?, ?, ?, 'confirmada')"
        );
        $guardar->bind_param("iissid", $idUsuario, $idRecurso, $fechaInicio, $fechaFin, $numPersonas, $presupuesto);

        // Si no se pudo guardar la reserva, deshacemos tambien la resta de plazas
        if (!$guardar->execute()) {
            $conexion->rollback();
            $bd->cerrar();
            return false;
        }

        $conexion->commit();
        $bd->cerrar();
        return true;
    }

    // Devuelve una lista con las reservas de un usuario
    public function listarPorUsuario($idUsuario) {
        $bd = new BaseDatos();
        $consulta = $bd->getConexion()->prepare(
            "SELECT res.id_reserva, rec.nombre AS recurso, res.fecha_inicio, res.fecha_fin,
                    res.num_personas, res.presupuesto, res.estado
             FROM reserva res
             JOIN recurso rec ON res.id_recurso = rec.id_recurso
             WHERE res.id_usuario = ?
             ORDER BY res.fecha_inicio DESC"
        );
        $consulta->bind_param("i", $idUsuario);
        $consulta->execute();
        $resultado = $consulta->get_result();

        // Metemos cada reserva en un array para devolverlo
        $reservas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $reservas[] = $fila;
        }
        $bd->cerrar();
        return $reservas;
    }

    // Anula una reserva del usuario y devuelve sus personas a las plazas del recurso
    public function anular($idReserva, $idUsuario) {
        $bd = new BaseDatos();
        $conexion = $bd->getConexion();

        // Buscamos la reserva (tiene que ser del usuario y estar confirmada)
        $consulta = $conexion->prepare(
            "SELECT id_recurso, num_personas FROM reserva
             WHERE id_reserva = ? AND id_usuario = ? AND estado = 'confirmada'"
        );
        $consulta->bind_param("ii", $idReserva, $idUsuario);
        $consulta->execute();
        $reserva = $consulta->get_result()->fetch_assoc();
        if (!$reserva) {
            $bd->cerrar();
            return false;
        }

        // Marcamos la reserva como anulada
        $anular = $conexion->prepare("UPDATE reserva SET estado = 'anulada' WHERE id_reserva = ?");
        $anular->bind_param("i", $idReserva);
        $anular->execute();

        // Sumamos otra vez las personas a las plazas del recurso
        $devolver = $conexion->prepare("UPDATE recurso SET plazas = plazas + ? WHERE id_recurso = ?");
        $devolver->bind_param("ii", $reserva["num_personas"], $reserva["id_recurso"]);
        $devolver->execute();

        $bd->cerrar();
        return true;
    }
}
?>
