<?php
class Hotel {
    private $conn;
    private $table_name = "Hotel";

    public $idAlojamiento;
    public $estrellas;
    public $telefonoContacto;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear un nuevo hotel
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET idAlojamiento=:idAlojamiento, estrellas=:estrellas, telefonoContacto=:telefonoContacto";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar entradas
        $this->idAlojamiento = htmlspecialchars(strip_tags($this->idAlojamiento));
        $this->estrellas = htmlspecialchars(strip_tags($this->estrellas));
        $this->telefonoContacto = htmlspecialchars(strip_tags($this->telefonoContacto));

        // Vincular parámetros
        $stmt->bindParam(":idAlojamiento", $this->idAlojamiento);
        $stmt->bindParam(":estrellas", $this->estrellas);
        $stmt->bindParam(":telefonoContacto", $this->telefonoContacto);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>