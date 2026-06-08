<?php
require_once __DIR__ . "/Sesion.php";
require_once __DIR__ . "/Recurso.php";
require_once __DIR__ . "/Reserva.php";

// Pagina que gestiona la reserva de un recurso: presupuesto y confirmacion
class ReservarUI {

    private $sesion;
    private $recurso;
    private $id = 0;
    private $mensaje = "";
    private $numPlazas = 0;
    private $presupuesto = 0;
    private $mostrarPresupuesto = false;
    private $confirmada = false;

    public function __construct() {
        $this->sesion = new Sesion();
    }

    // Comprueba la sesion, calcula presupuesto o crea la reserva y muestra la pagina
    public function ejecutar() {
        // Solo se puede reservar si hay sesion iniciada
        if (!$this->sesion->estaLogueado()) {
            header("Location: ../reservas.php");
            exit;
        }

        // Cogemos el id del recurso (puede venir del enlace o del formulario)
        $this->id = isset($_POST["id"]) ? intval($_POST["id"]) : (isset($_GET["id"]) ? intval($_GET["id"]) : 0);
        $recursoObj = new Recurso();
        $this->recurso = $recursoObj->obtener($this->id);

        // Si el recurso no existe volvemos al listado
        if (!$this->recurso) {
            header("Location: RecursosUI.php");
            exit;
        }

        if (isset($_POST["confirmar"])) {
            // El usuario confirma: intentamos crear la reserva
            $this->numPlazas = intval($_POST["num_plazas"]);
            $this->presupuesto = $this->recurso["precio"] * $this->numPlazas;
            $reserva = new Reserva();
            if ($reserva->crear($this->sesion->getId(), $this->id, $this->numPlazas, $this->presupuesto)) {
                $this->confirmada = true;
            } else {
                $this->mensaje = "Ya no quedan plazas suficientes para este recurso.";
            }
        } elseif (isset($_POST["num_plazas"])) {
            // El usuario elige plazas: calculamos el presupuesto
            $this->numPlazas = intval($_POST["num_plazas"]);
            if ($this->numPlazas < 1 || $this->numPlazas > $this->recurso["plazas"]) {
                $this->mensaje = "Indica un número de plazas entre 1 y " . $this->recurso["plazas"] . ".";
            } else {
                $this->presupuesto = $this->recurso["precio"] * $this->numPlazas;
                $this->mostrarPresupuesto = true;
            }
        }

        $this->mostrar();
    }

    // Dibuja la pagina completa
    public function mostrar() {
?>
<!DOCTYPE HTML>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Navarra - Reservar recurso</title>

    <meta name="author" content="Alejandro Requena Roncero" />
    <meta name="description" content="Reserva de un recurso turístico de Navarra" />
    <meta name="keywords" content="reserva, presupuesto, turismo, Navarra" />
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
            <li><a href="RecursosUI.php">Recursos turísticos</a></li>
            <li>Reservar</li>
        </ol>
    </nav>

    <main>
        <section>
            <h2>Reservar: <?php echo htmlspecialchars($this->recurso["nombre"]); ?></h2>
            <p>Localidad: <?php echo htmlspecialchars($this->recurso["localidad"]); ?> — Precio por plaza: <?php echo number_format($this->recurso["precio"], 2, ",", "."); ?> €</p>
            <p>Plazas disponibles: <?php echo $this->recurso["plazas"]; ?></p>
        </section>

<?php if ($this->confirmada): ?>
        <section>
            <h2>Reserva confirmada</h2>
            <p>Has reservado <?php echo $this->numPlazas; ?> plaza(s). Presupuesto total: <?php echo number_format($this->presupuesto, 2, ",", "."); ?> €.</p>
            <p><a href="MisReservasUI.php">Ver mis reservas</a></p>
        </section>
<?php elseif ($this->mostrarPresupuesto): ?>
        <section>
            <h2>Presupuesto</h2>
            <p><?php echo $this->numPlazas; ?> plaza(s) × <?php echo number_format($this->recurso["precio"], 2, ",", "."); ?> € = <?php echo number_format($this->presupuesto, 2, ",", "."); ?> €</p>
            <form action="ReservarUI.php" method="post">
                <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
                <input type="hidden" name="num_plazas" value="<?php echo $this->numPlazas; ?>" />
                <input type="hidden" name="confirmar" value="1" />
                <p><input type="submit" value="Confirmar reserva" /></p>
            </form>
            <p><a href="RecursosUI.php">Volver a los recursos</a></p>
        </section>
<?php elseif ($this->recurso["plazas"] < 1): ?>
        <section>
            <h2>Sin plazas disponibles</h2>
            <p>Lo sentimos, este recurso ya no tiene plazas disponibles.</p>
            <p><a href="RecursosUI.php">Volver a los recursos</a></p>
        </section>
<?php else: ?>
        <section>
            <h2>Elige las plazas</h2>
<?php if ($this->mensaje !== ""): ?>
            <p><?php echo $this->mensaje; ?></p>
<?php endif; ?>
            <form action="ReservarUI.php" method="post">
                <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
                <p>
                    <label for="num_plazas">Número de plazas (máximo <?php echo $this->recurso["plazas"]; ?>):</label>
                    <input type="number" id="num_plazas" name="num_plazas" min="1" max="<?php echo $this->recurso["plazas"]; ?>" value="1" required="required" />
                </p>
                <p><input type="submit" value="Calcular presupuesto" /></p>
            </form>
        </section>
<?php endif; ?>
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
$pagina = new ReservarUI();
$pagina->ejecutar();
?>
