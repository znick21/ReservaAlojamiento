<?php
require_once 'models/Reserva.php';

class ReservaController {
    private $reserva;

    public function __construct() {
        $this->reserva = new Reserva();
    }

    public function create($data) {
        $db = new Database();
        $conn = $db->getConnection();

        $idAlojamiento = $data['idAlojamiento'] ?? null;
        $tipoHabitacion = $data['tipoHabitacion'] ?? null;
        $fechaInicio = $data['fechaInicio'] ?? null;
        $fechaFin = $data['fechaFin'] ?? null;
        $estado = $data['estado'] ?? 'Pendiente';
        $notas = $data['notas'] ?? '';
        $precioTotal = $data['precioTotal'] ?? 0.00;
        $idHabitacion = $data['idHabitacion'] ?? null;

        if (!$idAlojamiento || !$tipoHabitacion || !$fechaInicio || !$fechaFin || !$idHabitacion) {
            return ['status' => 'error', 'message' => 'Faltan datos requeridos'];
        }

        $stmt = $conn->prepare("SELECT idHabitacion FROM habitacion 
                               WHERE idHabitacion = :idHabitacion 
                               AND estado = 'Disponible' 
                               AND idHabitacion NOT IN (
                                   SELECT idHabitacion FROM reserva 
                                   WHERE (fechaInicio <= :fechaFin AND fechaFin >= :fechaInicio)
                                   AND estado NOT IN ('Cancelada')
                               )");
        $stmt->bindParam(':idHabitacion', $idHabitacion, PDO::PARAM_INT);
        $stmt->bindParam(':fechaInicio', $fechaInicio);
        $stmt->bindParam(':fechaFin', $fechaFin);
        $stmt->execute();
        $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$habitacion) {
            return ['status' => 'error', 'message' => 'La habitación seleccionada no está disponible para las fechas indicadas'];
        }

        $this->reserva->idHabitacion = $idHabitacion;
        $this->reserva->fechaInicio = $fechaInicio;
        $this->reserva->fechaFin = $fechaFin;
        $this->reserva->estado = $estado;
        $this->reserva->notas = $notas;
        $this->reserva->precioTotal = $precioTotal;

        if ($this->reserva->create()) {
            $updateStmt = $conn->prepare("UPDATE habitacion SET estado = 'Ocupada' WHERE idHabitacion = :idHabitacion");
            $updateStmt->bindParam(':idHabitacion', $idHabitacion, PDO::PARAM_INT);
            $updateStmt->execute();

            return ['status' => 'success', 'message' => 'Reserva creada'];
        }
        return ['status' => 'error', 'message' => 'No se pudo crear la reserva: ' . implode(', ', $this->reserva->errorInfo ?? ['Desconocido'])];
    }

    public function getAll() {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT r.*, h.tipoHabitacion, h.numeroHabitacion, h.precio, a.nombre AS nombreAlojamiento 
                             FROM reserva r 
                             JOIN habitacion h ON r.idHabitacion = h.idHabitacion 
                             JOIN alojamiento a ON h.idAlojamiento = a.idAlojamiento");
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['status' => 'success', 'data' => $reservas];
    }

    public function get($id) {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT r.*, h.tipoHabitacion, h.numeroHabitacion, h.precio, a.nombre AS nombreAlojamiento 
                               FROM reserva r 
                               JOIN habitacion h ON r.idHabitacion = h.idHabitacion 
                               JOIN alojamiento a ON h.idAlojamiento = a.idAlojamiento 
                               WHERE r.idReserva = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($reserva) {
            return ['status' => 'success', 'data' => $reserva];
        }
        return ['status' => 'error', 'message' => 'Reserva no encontrada'];
    }

    public function update($id, $data) {
        $this->reserva->idReserva = $id;
        $this->reserva->idHabitacion = $data['idHabitacion'] ?? null;
        $this->reserva->fechaInicio = $data['fechaInicio'] ?? null;
        $this->reserva->fechaFin = $data['fechaFin'] ?? null;
        $this->reserva->estado = $data['estado'] ?? 'Pendiente';
        $this->reserva->notas = $data['notas'] ?? '';
        $this->reserva->precioTotal = $data['precioTotal'] ?? 0.00;

        if ($this->reserva->update()) {
            return ['status' => 'success', 'message' => 'Reserva actualizada'];
        }
        return ['status' => 'error', 'message' => 'No se pudo actualizar la reserva'];
    }

    public function delete($id) {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("DELETE FROM reserva WHERE idReserva = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Reserva eliminada'];
        }
        return ['status' => 'error', 'message' => 'No se pudo eliminar la reserva'];
    }
}
?>