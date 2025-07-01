<?php
class Alojamiento {
    private $conn;
    private $table_name = "Alojamiento";

    public $idAlojamiento;
    public $nombre;
    public $tipo;
    public $precioPorNoche;
    public $capacidad;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer todos los alojamientos
    public function read() {
        $query = "SELECT a.*, 
                         h.estrellas, 
                         h.telefonoContacto,
                         ho.horaCheckIn,
                         d.numeroHabitaciones,
                         d.capacidadMaxima,
                         d.amueblado,
                         d.cocinaEquipada
                  FROM " . $this->table_name . " a
                  LEFT JOIN Hotel h ON a.idAlojamiento = h.idAlojamiento
                  LEFT JOIN Hostal ho ON a.idAlojamiento = ho.idAlojamiento
                  LEFT JOIN DepartamentoTuristico d ON a.idAlojamiento = d.idAlojamiento";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear un nuevo alojamiento
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET nombre=:nombre, tipo=:tipo, precioPorNoche=:precioPorNoche, capacidad=:capacidad";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar entradas
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->precioPorNoche = htmlspecialchars(strip_tags($this->precioPorNoche));
        $this->capacidad = htmlspecialchars(strip_tags($this->capacidad));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":precioPorNoche", $this->precioPorNoche);
        $stmt->bindParam(":capacidad", $this->capacidad);

        if($stmt->execute()) {
            return $this->conn->lastInsertId(); // Retorna el ID del nuevo alojamiento
        }
        return false;
    }
}
?>