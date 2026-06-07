<?php
require_once __DIR__ . "/Sesion.php";
require_once __DIR__ . "/Reserva.php";

// Pagina que lista las reservas del usuario y permite anularlas
class PaginaMisReservas {

    private $sesion;
    private $listado = [];

    public function __construct() {
        $this->sesion = new Sesion();
    }

    // Comprueba la sesion, gestiona la anulacion, pide las reservas y muestra la pagina
    public function ejecutar() {
        // Solo se ven las reservas si hay sesion iniciada
        if (!$this->sesion->estaLogueado()) {
            header("Location: ../reservas.php");
            exit;
        }

        $reserva = new Reserva();

        // Si el usuario pulsa "Anular", anulamos esa reserva
        if (isset($_POST["anular"])) {
            $reserva->anular(intval($_POST["anular"]), $this->sesion->getId());
            header("Location: misreservas.php");
            exit;
        }

        // Cogemos todas las reservas del usuario para mostrarlas
        $this->listado = $reserva->listarPorUsuario($this->sesion->getId());

        $this->mostrar();
    }

    // Dibuja la pagina completa
    public function mostrar() {
?>
<!DOCTYPE HTML>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Navarra - Mis reservas</title>

    <meta name="author" content="Alejandro Requena Roncero" />
    <meta name="description" content="Consulta de las reservas del usuario en Navarra" />
    <meta name="keywords" content="reservas, consulta, turismo, Navarra" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="icon" type="image/x-icon" href="../multimedia/imagenes/icono-navarra.ico" />

    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>

<body>
    <header>
        <h1><a href="../index.html">Navarra Desktop</a></h1>

        <nav>
            <a href="../index.html" title="Inicio de la aplicación Navarra Desktop">Inicio</a>
            <a href="../gastronomia.html" title="Gastronomía de Navarra">Gastronomía</a>
            <a href="../rutas.html" title="Rutas turísticas de Navarra">Rutas</a>
            <a href="../meteorologia.html" title="Meteorología de Navarra">Meteorología</a>
            <a href="../juego.html" title="Juego sobre Navarra">Juego</a>
            <a class="active" href="../reservas.php" title="Central de reservas de recursos turísticos">Reservas</a>
            <a href="../ayuda.html" title="Ayuda sobre la aplicación">Ayuda</a>
        </nav>
    </header>

    <nav>
        <ol>
            <li><a href="../index.html">Inicio</a></li>
            <li><a href="../reservas.php">Reservas</a></li>
            <li>Mis reservas</li>
        </ol>
    </nav>

    <main>
        <section>
            <h2>Mis reservas</h2>
<?php if (count($this->listado) === 0): ?>
            <p>Todavía no tienes ninguna reserva. <a href="recursos.php">Ver recursos turísticos</a>.</p>
<?php else: ?>
            <table>
                <caption>Listado de tus reservas</caption>
                <thead>
                    <tr>
                        <th scope="col">Recurso</th>
                        <th scope="col">Fecha de la reserva</th>
                        <th scope="col">Plazas</th>
                        <th scope="col">Presupuesto</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($this->listado as $res): ?>
                    <tr>
                        <th scope="row"><?php echo htmlspecialchars($res["recurso"]); ?></th>
                        <td><?php echo $res["fecha_reserva"]; ?></td>
                        <td><?php echo $res["num_plazas"]; ?></td>
                        <td><?php echo number_format($res["presupuesto"], 2, ",", "."); ?> €</td>
                        <td><?php echo htmlspecialchars($res["estado"]); ?></td>
                        <td>
<?php if ($res["estado"] === "confirmada"): ?>
                            <form action="misreservas.php" method="post">
                                <input type="hidden" name="anular" value="<?php echo $res["id_reserva"]; ?>" />
                                <input type="submit" value="Anular" />
                            </form>
<?php else: ?>
                            —
<?php endif; ?>
                        </td>
                    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
<?php endif; ?>
        </section>
    </main>

    <footer>
        <p>© 2026 Alejandro Requena Roncero — Universidad de Oviedo</p>
    </footer>
</body>

</html>
<?php
    }
}

// Arranque de la pagina
$pagina = new PaginaMisReservas();
$pagina->ejecutar();
?>
