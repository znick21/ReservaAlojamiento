<?php
require_once '../config/database.php';

if (!isset($_GET['idAlojamiento'], $_GET['fechaInicio'], $_GET['fechaFin'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros incompletos']);
    exit;
}

$idAlojamiento = intval($_GET['idAlojamiento']);
$fechaInicio = $_GET['fechaInicio'];
$fechaFin = $_GET['fechaFin'];

$db = (new Database())->getConnection();

// Traer todas las habitaciones del alojamiento con su precio (suponiendo columna precio)
$query = "SELECT h.idHabitacion, h.numeroHabitacion, h.precio
          FROM habitacion h
          WHERE h.idAlojamiento = :idAlojamiento";
$stmt = $db->prepare($query);
$stmt->execute(['idAlojamiento' => $idAlojamiento]);
$habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ahora verificar para cada habitación si está ocupada en las fechas dadas (reserva entre fechas)
foreach ($habitaciones as &$hab) {
    $query = "SELECT COUNT(*) FROM reserva r 
              WHERE r.idHabitacion = :idHabitacion 
                AND NOT (r.fechaFin <= :fechaInicio OR r.fechaInicio >= :fechaFin)";
    $stmt2 = $db->prepare($query);
    $stmt2->execute([
        'idHabitacion' => $hab['idHabitacion'],
        'fechaInicio' => $fechaInicio,
        'fechaFin' => $fechaFin
    ]);
    $ocupada = $stmt2->fetchColumn() > 0;
    $hab['disponible'] = !$ocupada;
}
unset($hab);

header('Content-Type: application/json');
echo json_encode($habitaciones);
