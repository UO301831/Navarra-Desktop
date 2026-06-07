<?php
require_once __DIR__ . "/php/Sesion.php";

// Pagina principal de la central de reservas: login, logout y panel del usuario
class PaginaReservas {

    private $sesion;
    private $mensaje = "";

    public function __construct() {
        $this->sesion = new Sesion();
    }

    // Procesa logout e inicio de sesion (puede redirigir) y luego muestra la pagina
    public function ejecutar() {
        // Si el usuario pulsa "Cerrar sesion", cerramos la sesion
        if (isset($_GET["logout"])) {
            $this->sesion->logout();
            header("Location: reservas.php");
            exit;
        }

        // Si se ha enviado el formulario, intentamos iniciar sesion
        if (isset($_POST["email"], $_POST["contrasena"])) {
            if ($this->sesion->login($_POST["email"], $_POST["contrasena"])) {
                header("Location: reservas.php");
                exit;
            }
            $this->mensaje = "Correo o contraseña incorrectos.";
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
    <title>Navarra - Reservas</title>

    <meta name="author" content="Alejandro Requena Roncero" />
    <meta name="description" content="Central de reservas de recursos turísticos de Navarra" />
    <meta name="keywords" content="reservas, turismo, Navarra" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="icon" type="image/x-icon" href="multimedia/imagenes/icono-navarra.ico" />

    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
</head>

<body>
    <header>
        <h1><a href="index.html">Navarra Desktop</a></h1>

        <nav>
            <a href="index.html" title="Inicio de la aplicación Navarra Desktop">Inicio</a>
            <a href="gastronomia.html" title="Gastronomía de Navarra">Gastronomía</a>
            <a href="rutas.html" title="Rutas turísticas de Navarra">Rutas</a>
            <a href="meteorologia.html" title="Meteorología de Navarra">Meteorología</a>
            <a href="juego.html" title="Juego sobre Navarra">Juego</a>
            <a class="active" href="reservas.php" title="Central de reservas de recursos turísticos">Reservas</a>
            <a href="ayuda.html" title="Ayuda sobre la aplicación">Ayuda</a>
        </nav>
    </header>

    <nav>
        <ol>
            <li><a href="index.html">Inicio</a></li>
            <li>Reservas</li>
        </ol>
    </nav>

    <main>
        <section>
            <h2>Central de reservas</h2>
            <p>
                Desde aquí puedes reservar los recursos turísticos de Navarra: museos, rutas,
                restaurantes, hoteles y actividades. Para hacer una reserva necesitas tener una
                cuenta e iniciar sesión.
            </p>
        </section>

<?php if ($this->sesion->estaLogueado()): ?>
        <section>
            <h2>Hola, <?php echo htmlspecialchars($this->sesion->getNombre()); ?></h2>
            <ul>
                <li><a href="php/recursos.php">Ver recursos turísticos y reservar</a></li>
                <li><a href="php/misreservas.php">Consultar mis reservas</a></li>
                <li><a href="reservas.php?logout=1">Cerrar sesión</a></li>
            </ul>
        </section>
<?php else: ?>
        <section>
            <h2>Iniciar sesión</h2>
<?php if ($this->mensaje !== ""): ?>
            <p><?php echo $this->mensaje; ?></p>
<?php endif; ?>
            <form action="reservas.php" method="post">
                <p>
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" required="required" />
                </p>
                <p>
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required="required" />
                </p>
                <p>
                    <input type="submit" value="Entrar" />
                </p>
            </form>
            <p><a href="php/registro.php">¿No tienes cuenta? Regístrate</a></p>
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
$pagina = new PaginaReservas();
$pagina->ejecutar();
?>
