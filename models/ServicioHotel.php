<?php
class ServicioHotel {
    private $conn;
    private $table_name = "ServicioHotel";

    public $idServicioHotel;
    public $idAlojamiento;
    public $servicio;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer servicios de un hotel
    public function read($idAlojamiento) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idAlojamiento = :idAlojamiento";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idAlojamiento", $idAlojamiento);
        $stmt->execute();
        return $stmt;
    }

    // Crear un nuevo servicio para un hotel
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET idAlojamiento=:idAlojamiento, servicio=:servicio";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar entradas
        $this->idAlojamiento = htmlspecialchars(strip_tags($this->idAlojamiento));
        $this->servicio = htmlspecialchars(strip_tags($this->servicio));

        // Vincular parámetros
        $stmt->bindParam(":idAlojamiento", $this->idAlojamiento);
        $stmt->bindParam(":servicio", $this->servicio);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>