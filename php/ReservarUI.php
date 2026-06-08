<?php
require_once __DIR__ . "/Sesion.php";
require_once __DIR__ . "/Recurso.php";
require_once __DIR__ . "/Reserva.php";

// Pagina que gestiona la reserva de un recurso por fechas: presupuesto y confirmacion
class ReservarUI {

    private $sesion;
    private $recurso;
    private $id = 0;
    private $mensaje = "";
    private $fechaInicio = "";
    private $fechaFin = "";
    private $numPersonas = 1;
    private $dias = 0;
    private $presupuesto = 0;
    private $mostrarPresupuesto = false;
    private $confirmada = false;

    public function __construct() {
        $this->sesion = new Sesion();
    }

    // Comprueba la sesion, calcula presupuesto o crea la reserva y muestra la pagina
    public function ejecutar() {
        if (!$this->sesion->estaLogueado()) {
            header("Location: ../reservas.php");
            exit;
        }

        // Cogemos el id del recurso (puede venir del enlace o del formulario)
        $this->id = isset($_POST["id"]) ? intval($_POST["id"]) : (isset($_GET["id"]) ? intval($_GET["id"]) : 0);
        $recursoObj = new Recurso();
        $this->recurso = $recursoObj->obtener($this->id);

        if (!$this->recurso) {
            header("Location: RecursosUI.php");
            exit;
        }

        // Si se ha enviado el formulario, validamos y calculamos el presupuesto
        if (isset($_POST["fecha_inicio"], $_POST["fecha_fin"], $_POST["num_personas"])) {
            $this->fechaInicio = $_POST["fecha_inicio"];
            $this->fechaFin = $_POST["fecha_fin"];
            $this->numPersonas = intval($_POST["num_personas"]);

            $error = $this->validar();
            if ($error !== "") {
                $this->mensaje = $error;
            } else {
                $this->dias = $this->calcularDias();
                $this->presupuesto = $this->recurso["precio"] * $this->dias * $this->numPersonas;

                if (isset($_POST["confirmar"])) {
                    // El usuario confirma: intentamos crear la reserva
                    $reserva = new Reserva();
                    if ($reserva->crear($this->sesion->getId(), $this->id, $this->fechaInicio, $this->fechaFin, $this->numPersonas, $this->presupuesto)) {
                        $this->confirmada = true;
                    } else {
                        $this->mensaje = "No quedan plazas suficientes para ese número de personas.";
                    }
                } else {
                    $this->mostrarPresupuesto = true;
                }
            }
        }

        $this->mostrar();
    }

        private function formatearFecha($fecha) {
        return DateTime::createFromFormat("Y-m-d", substr($fecha, 0, 10))->format("d/m/Y");
    }

    // Numero de dias de la reserva (ambos extremos incluidos)
    private function calcularDias() {
        $inicio = new DateTime($this->fechaInicio);
        $fin = new DateTime($this->fechaFin);
        return $inicio->diff($fin)->days + 1;
    }

    // Comprueba las fechas y el numero de personas; devuelve "" si todo es correcto
    private function validar() {
        $winInicio = substr($this->recurso["fecha_inicio"], 0, 10);
        $winFin = substr($this->recurso["fecha_fin"], 0, 10);

        if ($this->fechaInicio === "" || $this->fechaFin === "") {
            return "Indica la fecha de inicio y la fecha de fin.";
        }
        if ($this->fechaFin < $this->fechaInicio) {
            return "La fecha de fin no puede ser anterior a la de inicio.";
        }
        if ($this->fechaInicio < $winInicio || $this->fechaFin > $winFin) {
            return "Las fechas deben estar dentro de la disponibilidad del recurso (" . $winInicio . " a " . $winFin . ").";
        }
        if ($this->numPersonas < 1 || $this->numPersonas > $this->recurso["plazas"]) {
            return "El número de personas debe estar entre 1 y " . $this->recurso["plazas"] . ".";
        }
        return "";
    }

    // Dibuja la pagina completa
    public function mostrar() {
        $winInicio = substr($this->recurso["fecha_inicio"], 0, 10);
        $winFin = substr($this->recurso["fecha_fin"], 0, 10);
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
            <p>Localidad: <?php echo htmlspecialchars($this->recurso["localidad"]); ?> — Precio por persona y día: <?php echo number_format($this->recurso["precio"], 2, ",", "."); ?> €</p>
            <p>Disponible del <?php echo $this->formatearFecha($winInicio); ?> al <?php echo $this->formatearFecha($winFin); ?></p>
            <p>Plazas disponibles: <?php echo $this->recurso["plazas"]; ?></p>
        </section>

<?php if ($this->confirmada): ?>
        <section>
            <h2>Reserva confirmada</h2>
            <p>Has reservado del <?php echo $this->formatearFecha($this->fechaInicio); ?> al <?php echo $this->formatearFecha($this->fechaFin); ?></p>
            <p>Presupuesto total: <?php echo number_format($this->presupuesto, 2, ",", "."); ?> €.</p>
            <p><a href="MisReservasUI.php">Ver mis reservas</a></p>
        </section>
<?php elseif ($this->mostrarPresupuesto): ?>
        <section>
            <h2>Presupuesto</h2>
            <p><?php echo $this->dias; ?> día(s) × <?php echo $this->numPersonas; ?> persona(s) × <?php echo number_format($this->recurso["precio"], 2, ",", "."); ?> € = <?php echo number_format($this->presupuesto, 2, ",", "."); ?> €</p>
            <form action="ReservarUI.php" method="post">
                <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
                <input type="hidden" name="fecha_inicio" value="<?php echo htmlspecialchars($this->fechaInicio); ?>" />
                <input type="hidden" name="fecha_fin" value="<?php echo htmlspecialchars($this->fechaFin); ?>" />
                <input type="hidden" name="num_personas" value="<?php echo $this->numPersonas; ?>" />
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
            <h2>Elige las fechas</h2>
<?php if ($this->mensaje !== ""): ?>
            <p><?php echo $this->mensaje; ?></p>
<?php endif; ?>
            <form action="ReservarUI.php" method="post">
                <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
                <p>
                    <label for="fecha_inicio">Fecha de inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" min="<?php echo $winInicio; ?>" max="<?php echo $winFin; ?>" value="<?php echo htmlspecialchars($this->fechaInicio); ?>" required="required" />
                </p>
                <p>
                    <label for="fecha_fin">Fecha de fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" min="<?php echo $winInicio; ?>" max="<?php echo $winFin; ?>" value="<?php echo htmlspecialchars($this->fechaFin); ?>" required="required" />
                </p>
                <p>
                    <label for="num_personas">Número de personas (máximo <?php echo $this->recurso["plazas"]; ?>):</label>
                    <input type="number" id="num_personas" name="num_personas" min="1" max="<?php echo $this->recurso["plazas"]; ?>" value="<?php echo $this->numPersonas; ?>" required="required" />
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
