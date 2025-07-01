<?php
class Hostal {
    private $conn;
    private $table_name = "Hostal";

    public $idAlojamiento;
    public $horaCheckIn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear un nuevo hostal
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET idAlojamiento=:idAlojamiento, horaCheckIn=:horaCheckIn";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar entradas
        $this->idAlojamiento = htmlspecialchars(strip_tags($this->idAlojamiento));
        $this->horaCheckIn = htmlspecialchars(strip_tags($this->horaCheckIn));

        // Vincular parámetros
        $stmt->bindParam(":idAlojamiento", $this->idAlojamiento);
        $stmt->bindParam(":horaCheckIn", $this->horaCheckIn);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>