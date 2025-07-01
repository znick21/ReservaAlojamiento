<?php
class Hotel {
    private $conn;
    private $table_name = "hotel";

    public $idAlojamiento;
    public $estrellas;
    public $maximoPersonasPorHabitacion; // si usas o no, depende
    public $habitacionesLujo;
    public $habitacionesEstandar;
    public $telefonoContacto;
    public $precioLujo;
    public $precioEstandar;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        // Validaciones simples
        if ($this->estrellas < 1 || $this->estrellas > 5) return false;
        if ($this->habitacionesLujo < 0 || $this->habitacionesEstandar < 0) return false;
        if (empty($this->telefonoContacto)) return false;
        if ($this->precioLujo <= 0 || $this->precioEstandar <= 0) return false;

        $query = "INSERT INTO " . $this->table_name . " 
                  SET idAlojamiento=:idAlojamiento, estrellas=:estrellas, 
                      habitacionesLujo=:habitacionesLujo, habitacionesEstandar=:habitacionesEstandar,
                      telefonoContacto=:telefonoContacto,
                      precioLujo=:precioLujo, precioEstandar=:precioEstandar";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":idAlojamiento", $this->idAlojamiento, PDO::PARAM_INT);
        $stmt->bindParam(":estrellas", $this->estrellas, PDO::PARAM_INT);
        $stmt->bindParam(":habitacionesLujo", $this->habitacionesLujo, PDO::PARAM_INT);
        $stmt->bindParam(":habitacionesEstandar", $this->habitacionesEstandar, PDO::PARAM_INT);
        $stmt->bindParam(":telefonoContacto", $this->telefonoContacto, PDO::PARAM_STR);
        $stmt->bindParam(":precioLujo", $this->precioLujo);
        $stmt->bindParam(":precioEstandar", $this->precioEstandar);

        return $stmt->execute();
    }
}

?>
