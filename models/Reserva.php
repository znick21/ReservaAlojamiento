<?php
require_once 'config/Database.php';

class Reserva {
    private $conn;
    private $table = 'reserva';

    public $idReserva;
    public $idHabitacion;
    public $fechaInicio;
    public $fechaFin;
    public $estado;
    public $notas;
    public $precioTotal;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (idHabitacion, fechaInicio, fechaFin, estado, notas, precioTotal, fechaReserva) 
                  VALUES (:idHabitacion, :fechaInicio, :fechaFin, :estado, :notas, :precioTotal, NOW())";
        $stmt = $this->conn->prepare($query);

        $this->idHabitacion = (int)$this->idHabitacion;
        $this->fechaInicio = htmlspecialchars(strip_tags($this->fechaInicio));
        $this->fechaFin = htmlspecialchars(strip_tags($this->fechaFin));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->notas = htmlspecialchars(strip_tags($this->notas));
        $this->precioTotal = (float)$this->precioTotal;

        $stmt->bindParam(':idHabitacion', $this->idHabitacion, PDO::PARAM_INT);
        $stmt->bindParam(':fechaInicio', $this->fechaInicio);
        $stmt->bindParam(':fechaFin', $this->fechaFin);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':notas', $this->notas);
        $stmt->bindParam(':precioTotal', $this->precioTotal, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        }
        $this->errorInfo = $stmt->errorInfo();
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET idHabitacion = :idHabitacion, fechaInicio = :fechaInicio, fechaFin = :fechaFin, 
                      estado = :estado, notas = :notas, precioTotal = :precioTotal 
                  WHERE idReserva = :id";
        $stmt = $this->conn->prepare($query);

        $this->idReserva = (int)$this->idReserva;
        $this->idHabitacion = (int)$this->idHabitacion;
        $this->fechaInicio = htmlspecialchars(strip_tags($this->fechaInicio));
        $this->fechaFin = htmlspecialchars(strip_tags($this->fechaFin));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->notas = htmlspecialchars(strip_tags($this->notas));
        $this->precioTotal = (float)$this->precioTotal;

        $stmt->bindParam(':idReserva', $this->idReserva, PDO::PARAM_INT);
        $stmt->bindParam(':idHabitacion', $this->idHabitacion, PDO::PARAM_INT);
        $stmt->bindParam(':fechaInicio', $this->fechaInicio);
        $stmt->bindParam(':fechaFin', $this->fechaFin);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':notas', $this->notas);
        $stmt->bindParam(':precioTotal', $this->precioTotal, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
?>