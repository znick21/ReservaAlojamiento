<?php
ob_start();
require_once 'controllers/AlojamientoController.php';
require_once 'controllers/ReservaController.php';

// Obtener controlador y acción desde parámetros GET
$controller = isset($_GET['controller']) ? strtolower($_GET['controller']) : 'alojamiento';
$action = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Variable para almacenar el contenido de la vista
$content = '';
$response = null;

// Procesar solicitudes
switch ($controller) {
    case 'alojamiento':
        $alojamientoController = new AlojamientoController();

        switch ($action) {
            case 'crear':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = $_POST;
                    $response = $alojamientoController->create($data);
                    if ($response['status'] === 'success') {
                        header('Location: index.php?controller=alojamiento&action=index&message=Alojamiento creado');
                        exit;
                    } else {
                        $content = "<p class='error'>Error: {$response['message']}</p>";
                    }
                }
                $content = include_view('alojamiento/crear.php');
                break;

            case 'index':
                $response = $alojamientoController->getAll();
                $content = include_view('alojamiento/index.php', ['alojamientos' => $response['data']]);
                break;

            case 'editar':
                if ($id) {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $data = $_POST;
                        $response = $alojamientoController->update($id, $data);
                        if ($response['status'] === 'success') {
                            header('Location: index.php?controller=alojamiento&action=index&message=Alojamiento actualizado');
                            exit;
                        } else {
                            $content = "<p class='error'>Error: {$response['message']}</p>";
                        }
                    }
                    $response = $alojamientoController->get($id);
                    $content = include_view('alojamiento/crear.php', ['alojamiento' => $response['data'], 'editar' => true]);
                } else {
                    $content = "<p class='error'>Error: ID requerido</p>";
                }
                break;

            case 'eliminar':
                if ($id) {
                    $response = $alojamientoController->delete($id);
                    header('Location: index.php?controller=alojamiento&action=index&message=' . urlencode($response['message']));
                    exit;
                } else {
                    $content = "<p class='error'>Error: ID requerido</p>";
                }
                break;

            default:
                $content = "<p class='error'>Acción no válida</p>";
                break;
        }
        break;

    case 'reserva':
        $reservaController = new ReservaController();

        switch ($action) {
            case 'crear':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = $_POST;
                    $response = $reservaController->create($data);
                    if ($response['status'] === 'success') {
                        header('Location: index.php?controller=reserva&action=index&message=' . urlencode($response['message']));
                        exit;
                    } else {
                        $content = "<p class='error'>Error: {$response['message']}</p>";
                        $content .= include_view('reserva/crear.php', ['reserva' => $data]);
                    }
                } else {
                    $content = include_view('reserva/crear.php');
                }
                break;

            case 'index':
                $response = $reservaController->getAll();
                $content = include_view('reserva/index.php', ['reservas' => $response['data']]);
                break;

            case 'editar':
                if ($id) {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $data = $_POST;
                        $response = $reservaController->update($id, $data);
                        if ($response['status'] === 'success') {
                            header('Location: index.php?controller=reserva&action=index&message=' . urlencode($response['message']));
                            exit;
                        } else {
                            $content = "<p class='error'>Error: {$response['message']}</p>";
                        }
                    }
                    $response = $reservaController->get($id);
                    $content = include_view('reserva/crear.php', ['reserva' => $response['data'], 'editar' => true]);
                } else {
                    $content = "<p class='error'>Error: ID requerido</p>";
                }
                break;

            case 'eliminar':
                if ($id) {
                    $response = $reservaController->delete($id);
                    header('Location: index.php?controller=reserva&action=index&message=' . urlencode($response['message']));
                    exit;
                } else {
                    $content = "<p class='error'>Error: ID requerido</p>";
                }
                break;

            default:
                $content = "<p class='error'>Acción no válida</p>";
                break;
        }
        break;

    default:
        $content = "<p class='error'>Controlador no válido</p>";
        break;
}

// Función para incluir vistas
function include_view($view, $data = []) {
    extract($data);
    ob_start();
    require_once "views/$view";
    return ob_get_clean();
}

// Funciones auxiliares para obtener habitaciones y servicios
function getHabitaciones() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->query("SELECT idHabitacion, CONCAT(tipoHabitacion, ' - ', numeroHabitacion) as nombre FROM habitacion WHERE estado = 'Disponible'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Nuevas funciones para acciones AJAX
function getHabitacionesByAlojamiento($id) {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT idHabitacion, tipoHabitacion, numeroHabitacion, estado, precio FROM habitacion WHERE idAlojamiento = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPrecioAlojamiento($id, $type) {
    // Esta función ya no se usa, pero la dejamos por compatibilidad
    return ['status' => 'error', 'precio' => 0, 'message' => 'No implementado'];
}

if ($_GET['controller'] === 'alojamiento' && $_GET['action'] === 'getPrecio' && isset($_GET['id']) && isset($_GET['type'])) {
    header('Content-Type: application/json');
    $result = getPrecioAlojamiento($_GET['id'], $_GET['type']);
    echo json_encode($result);
    exit;
}

function getPrecioHabitacion($id) {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT precio FROM habitacion WHERE idHabitacion = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $precio = $stmt->fetch(PDO::FETCH_ASSOC);
    return $precio ? ['status' => 'success', 'precio' => $precio['precio']] : ['status' => 'error', 'precio' => 0];
}

// Manejo de solicitudes AJAX
if ($_GET['controller'] === 'habitacion' && $_GET['action'] === 'getByAlojamiento' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(getHabitacionesByAlojamiento($_GET['id']));
    exit;
}

if ($_GET['controller'] === 'habitacion' && $_GET['action'] === 'getPrecio' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(getPrecioHabitacion($_GET['id']));
    exit;
}

?>

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
            <a href="index.php?controller=alojamiento&action=crear">Nuevo Alojamiento</a>
            <a href="index.php?controller=reserva&action=index">Reservas</a>
            <a href="index.php?controller=reserva&action=crear">Nueva Reserva</a>
        </nav>
    </header>
    <main>
        <?php 
        if (isset($_GET['message'])) {
            echo '<p class="message">' . htmlspecialchars($_GET['message']) . '</p>';
        }
        echo $content; 
        ?>
    </main>
</body>
</html>
<?php ob_end_flush(); ?>