<?php
class Reserva {
    private $conn;
    private $table_name = "Reserva";

    public $idReserva;
    public $fechaInicio;
    public $estado;
    public $precioTotal;
    public $numeroHuespedes;
    public $idAlojamiento;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer todas las reservas
    public function read() {
        $query = "SELECT r.*, a.nombre AS nombre_alojamiento
                  FROM " . $this->table_name . " r
                  JOIN Alojamiento a ON r.idAlojamiento = a.idAlojamiento";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear una nueva reserva
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET fechaInicio=:fechaInicio, estado=:estado, precioTotal=:precioTotal, 
                      numeroHuespedes=:numeroHuespedes, idAlojamiento=:idAlojamiento";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar entradas
        $this->fechaInicio = htmlspecialchars(strip_tags($this->fechaInicio));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->precioTotal = htmlspecialchars(strip_tags($this->precioTotal));
        $this->numeroHuespedes = htmlspecialchars(strip_tags($this->numeroHuespedes));
        $this->idAlojamiento = htmlspecialchars(strip_tags($this->idAlojamiento));

        // Vincular parámetros
        $stmt->bindParam(":fechaInicio", $this->fechaInicio);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":precioTotal", $this->precioTotal);
        $stmt->bindParam(":numeroHuespedes", $this->numeroHuespedes);
        $stmt->bindParam(":idAlojamiento", $this->idAlojamiento);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>