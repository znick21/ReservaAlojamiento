<?php
class DepartamentoTuristico {
    private $conn;
    private $table_name = "departamentoturistico";

    public $idAlojamiento;
    public $numeroHabitaciones;
    public $capacidadMaxima;
    public $amueblado;
    public $cocinaEquipada;
    public $habitacionesLujo;
    public $habitacionesEstandar;
    public $precioLujo;
    public $precioEstandar;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        if ($this->numeroHabitaciones < 1) return false;
        if ($this->capacidadMaxima < 1) return false;
        if ($this->habitacionesLujo < 0 || $this->habitacionesEstandar < 0) return false;
        if (($this->habitacionesLujo + $this->habitacionesEstandar) > $this->numeroHabitaciones) return false;
        if ($this->precioLujo <= 0 || $this->precioEstandar <= 0) return false;

        $query = "INSERT INTO " . $this->table_name . " 
                  SET idAlojamiento=:idAlojamiento, numeroHabitaciones=:numeroHabitaciones,
                      capacidadMaxima=:capacidadMaxima, amueblado=:amueblado, cocinaEquipada=:cocinaEquipada,
                      habitacionesLujo=:habitacionesLujo, habitacionesEstandar=:habitacionesEstandar,
                      precioLujo=:precioLujo, precioEstandar=:precioEstandar";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":idAlojamiento", $this->idAlojamiento, PDO::PARAM_INT);
        $stmt->bindParam(":numeroHabitaciones", $this->numeroHabitaciones, PDO::PARAM_INT);
        $stmt->bindParam(":capacidadMaxima", $this->capacidadMaxima, PDO::PARAM_INT);
        $stmt->bindParam(":amueblado", $this->amueblado, PDO::PARAM_BOOL);
        $stmt->bindParam(":cocinaEquipada", $this->cocinaEquipada, PDO::PARAM_BOOL);
        $stmt->bindParam(":habitacionesLujo", $this->habitacionesLujo, PDO::PARAM_INT);
        $stmt->bindParam(":habitacionesEstandar", $this->habitacionesEstandar, PDO::PARAM_INT);
        $stmt->bindParam(":precioLujo", $this->precioLujo);
        $stmt->bindParam(":precioEstandar", $this->precioEstandar);

        return $stmt->execute();
    }
}
