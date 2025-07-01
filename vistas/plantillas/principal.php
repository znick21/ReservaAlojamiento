<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Alojamiento</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header>
        <h1>Sistema de Gestión de Alojamientos</h1>
        <nav>
            <a href="index.php?controller=alojamiento&action=index">Alojamientos</a>
            <a href="index.php?controller=alojamiento&action=create">Nuevo Alojamiento</a>
            <a href="index.php?controller=reserva&action=index">Reservas</a>
            <a href="index.php?controller=reserva&action=create">Nueva Reserva</a>
        </nav>
    </header>
    <main>
        <?php echo $content; ?>
    </main>
</body>
</html>