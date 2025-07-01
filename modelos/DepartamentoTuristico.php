<?php
class DepartamentoTuristico {
    private $conn;
    private $table_name = "DepartamentoTuristico";

    public $idAlojamiento;
    public $numeroHabitaciones;
    public $capacidadMaxima;
    public $amueblado;
    public $cocinaEquipada;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear un nuevo departamento turístico
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET idAlojamiento=:idAlojamiento, numeroHabitaciones=:numeroHabitaciones, 
                      capacidadMaxima=:capacidadMaxima, amueblado=:amueblado, cocinaEquipada=:cocinaEquipada";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar entradas
        $this->idAlojamiento = htmlspecialchars(strip_tags($this->idAlojamiento));
        $this->numeroHabitaciones = htmlspecialchars(strip_tags($this->numeroHabitaciones));
        $this->capacidadMaxima = htmlspecialchars(strip_tags($this->capacidadMaxima));
        $this->amueblado = filter_var($this->amueblado, FILTER_VALIDATE_BOOLEAN);
        $this->cocinaEquipada = filter_var($this->cocinaEquipada, FILTER_VALIDATE_BOOLEAN);

        // Vincular parámetros
        $stmt->bindParam(":idAlojamiento", $this->idAlojamiento);
        $stmt->bindParam(":numeroHabitaciones", $this->numeroHabitaciones);
        $stmt->bindParam(":capacidadMaxima", $this->capacidadMaxima);
        $stmt->bindParam(":amueblado", $this->amueblado, PDO::PARAM_INT);
        $stmt->bindParam(":cocinaEquipada", $this->cocinaEquipada, PDO::PARAM_INT);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>