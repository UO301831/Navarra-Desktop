<?php
require_once __DIR__ . "/Sesion.php";
require_once __DIR__ . "/Recurso.php";

// Pagina que lista los recursos turisticos disponibles para reservar
class RecursosUI {

    private $sesion;
    private $listado = [];

    public function __construct() {
        $this->sesion = new Sesion();
    }

    // Comprueba la sesion, pide los recursos y muestra la pagina
    public function ejecutar() {
        // Solo se ven los recursos si hay sesion iniciada
        if (!$this->sesion->estaLogueado()) {
            header("Location: ../reservas.php");
            exit;
        }

        // Pedimos la lista de recursos a la base de datos
        $recurso = new Recurso();
        $this->listado = $recurso->listar();

        $this->mostrar();
    }

    // Convierte fecha de YYYY-MM-DD (o YYYY-MM-DD HH:MM:SS) a DD/MM/YYYY
    private function formatearFecha($fecha) {
        return DateTime::createFromFormat("Y-m-d", substr($fecha, 0, 10))->format("d/m/Y");
    }

    // Dibuja la pagina completa
    public function mostrar() {
?>
<!DOCTYPE HTML>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Navarra - Recursos turísticos</title>

    <meta name="author" content="Alejandro Requena Roncero" />
    <meta name="description" content="Recursos turísticos de Navarra disponibles para reservar" />
    <meta name="keywords" content="recursos, turismo, reservas, Navarra" />
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
            <li>Recursos turísticos</li>
        </ol>
    </nav>

    <main>
        <section>
            <h2>Recursos turísticos disponibles</h2>
            <p>Estos son los recursos turísticos de Navarra que puedes reservar.</p>
        </section>

<?php foreach ($this->listado as $r): ?>
        <article>
            <h3><?php echo htmlspecialchars($r["nombre"]); ?></h3>
            <p>Tipo: <?php echo htmlspecialchars($r["tipo"]); ?> — Localidad: <?php echo htmlspecialchars($r["localidad"]); ?></p>
            <p>Disponible del <?php echo $this->formatearFecha($r["fecha_inicio"]); ?> al <?php echo $this->formatearFecha($r["fecha_fin"]); ?></p>
            <p>Plazas: <?php echo $r["plazas"]; ?> — Precio por persona y día: <?php echo number_format($r["precio"], 2, ",", "."); ?> €</p>
            <p><?php echo htmlspecialchars($r["descripcion"]); ?></p>
<?php if ($r["plazas"] > 0): ?>
            <p><a href="ReservarUI.php?id=<?php echo $r["id_recurso"]; ?>">Reservar</a></p>
<?php else: ?>
            <p>Sin plazas disponibles</p>
<?php endif; ?>
        </article>
<?php endforeach; ?>
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
$pagina = new RecursosUI();
$pagina->ejecutar();
?>
