<?php
require_once __DIR__ . "/Usuario.php";

// Pagina de registro de nuevos usuarios de la central de reservas
class PaginaRegistro {

    private $mensaje = "";
    private $registrado = false;

    // Procesa el formulario de registro y luego muestra la pagina
    public function ejecutar() {
        // Si se ha enviado el formulario, intentamos registrar al usuario
        if (isset($_POST["nombre"], $_POST["apellidos"], $_POST["email"], $_POST["contrasena"])) {
            $usuario = new Usuario();
            $telefono = $_POST["telefono"] ?? "";
            if ($usuario->registrar($_POST["nombre"], $_POST["apellidos"], $_POST["email"], $_POST["contrasena"], $telefono)) {
                $this->registrado = true;
            } else {
                $this->mensaje = "Ese correo electrónico ya está registrado.";
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
    <title>Navarra - Registro</title>

    <meta name="author" content="Alejandro Requena Roncero" />
    <meta name="description" content="Registro de usuarios de la central de reservas de Navarra" />
    <meta name="keywords" content="registro, usuario, reservas, Navarra" />
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
            <li>Registro</li>
        </ol>
    </nav>

    <main>
<?php if ($this->registrado): ?>
        <section>
            <h2>Registro completado</h2>
            <p>Tu cuenta se ha creado correctamente. Ya puedes <a href="../reservas.php">iniciar sesión</a>.</p>
        </section>
<?php else: ?>
        <section>
            <h2>Crear una cuenta</h2>
            <p>Regístrate para poder reservar recursos turísticos de Navarra.</p>
<?php if ($this->mensaje !== ""): ?>
            <p><?php echo $this->mensaje; ?></p>
<?php endif; ?>
            <form action="registro.php" method="post">
                <p>
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required="required" />
                </p>
                <p>
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" required="required" />
                </p>
                <p>
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" required="required" />
                </p>
                <p>
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required="required" />
                </p>
                <p>
                    <label for="telefono">Teléfono (opcional):</label>
                    <input type="tel" id="telefono" name="telefono" />
                </p>
                <p>
                    <input type="submit" value="Registrarse" />
                </p>
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
$pagina = new PaginaRegistro();
$pagina->ejecutar();
?>
