<?php
class Hostal {
    private $conn;
    private $table_name = "hostal";

    public $idAlojamiento;
    public $estrellas;
    public $horaCheckIn;
    public $habitacionesLujo;
    public $habitacionesEstandar;
    public $precioLujo;
    public $precioEstandar;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        if ($this->estrellas < 1 || $this->estrellas > 5) return false;
        if ($this->habitacionesLujo < 0 || $this->habitacionesEstandar < 0) return false;
        if (empty($this->horaCheckIn)) return false;
        if ($this->precioLujo <= 0 || $this->precioEstandar <= 0) return false;

        $query = "INSERT INTO " . $this->table_name . " 
                  SET idAlojamiento = :idAlojamiento, 
                      estrellas = :estrellas, 
                      horaCheckIn = :horaCheckIn, 
                      habitacionesLujo = :habitacionesLujo, 
                      habitacionesEstandar = :habitacionesEstandar,
                      precioLujo = :precioLujo,
                      precioEstandar = :precioEstandar";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":idAlojamiento", $this->idAlojamiento, PDO::PARAM_INT);
        $stmt->bindParam(":estrellas", $this->estrellas, PDO::PARAM_INT);
        $stmt->bindParam(":horaCheckIn", $this->horaCheckIn, PDO::PARAM_STR);
        $stmt->bindParam(":habitacionesLujo", $this->habitacionesLujo, PDO::PARAM_INT);
        $stmt->bindParam(":habitacionesEstandar", $this->habitacionesEstandar, PDO::PARAM_INT);
        $stmt->bindParam(":precioLujo", $this->precioLujo);
        $stmt->bindParam(":precioEstandar", $this->precioEstandar);

        return $stmt->execute();
    }
}
