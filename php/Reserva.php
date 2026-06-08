<?php
require_once __DIR__ . "/BaseDatos.php";

// Clase para crear, listar y anular las reservas de los usuarios
class Reserva {

    // Crea una reserva para un rango de fechas y un numero de personas.
    // Solo se permite si ningun dia del rango supera la capacidad (plazas) del recurso.
    // Devuelve false si algun dia esta lleno.
    public function crear($idUsuario, $idRecurso, $fechaInicio, $fechaFin, $numPersonas, $presupuesto) {
        $bd = new BaseDatos();
        $conexion = $bd->getConexion();
        $conexion->begin_transaction();

        // Capacidad del recurso. Bloqueamos la fila para evitar reservas simultaneas
        $cap = $conexion->prepare("SELECT plazas FROM recurso WHERE id_recurso = ? FOR UPDATE");
        $cap->bind_param("i", $idRecurso);
        $cap->execute();
        $recurso = $cap->get_result()->fetch_assoc();
        if (!$recurso) {
            $conexion->rollback();
            $bd->cerrar();
            return false;
        }
        $capacidad = (int) $recurso["plazas"];

        // Reservas confirmadas que solapan con el rango pedido
        $solapan = $conexion->prepare(
            "SELECT fecha_inicio, fecha_fin, num_personas FROM reserva
             WHERE id_recurso = ? AND estado = 'confirmada' AND fecha_inicio <= ? AND fecha_fin >= ?"
        );
        $solapan->bind_param("iss", $idRecurso, $fechaFin, $fechaInicio);
        $solapan->execute();
        $resultado = $solapan->get_result();
        $reservas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $reservas[] = $fila;
        }

        // Cada dia del rango: las personas ya reservadas + las nuevas no pueden superar la capacidad
        $dia = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        while ($dia <= $fin) {
            $hoy = $dia->format("Y-m-d");
            $ocupadas = 0;
            foreach ($reservas as $rsv) {
                if ($rsv["fecha_inicio"] <= $hoy && $rsv["fecha_fin"] >= $hoy) {
                    $ocupadas += (int) $rsv["num_personas"];
                }
            }
            if ($ocupadas + $numPersonas > $capacidad) {
                $conexion->rollback();
                $bd->cerrar();
                return false;
            }
            $dia->modify("+1 day");
        }

        // Hay sitio todos los dias: guardamos la reserva
        $guardar = $conexion->prepare(
            "INSERT INTO reserva (id_usuario, id_recurso, fecha_reserva, fecha_inicio, fecha_fin, num_personas, presupuesto, estado)
             VALUES (?, ?, NOW(), ?, ?, ?, ?, 'confirmada')"
        );
        $guardar->bind_param("iissid", $idUsuario, $idRecurso, $fechaInicio, $fechaFin, $numPersonas, $presupuesto);
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

    // Anula una reserva del usuario (libera esos dias al dejar de contar como confirmada)
    public function anular($idReserva, $idUsuario) {
        $bd = new BaseDatos();
        $anular = $bd->getConexion()->prepare(
            "UPDATE reserva SET estado = 'anulada' WHERE id_reserva = ? AND id_usuario = ? AND estado = 'confirmada'"
        );
        $anular->bind_param("ii", $idReserva, $idUsuario);
        $anular->execute();
        $ok = $anular->affected_rows > 0;
        $bd->cerrar();
        return $ok;
    }
}
?>
