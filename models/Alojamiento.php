<?php
require_once 'config/Database.php';

class Alojamiento {
    private $conn;
    private $table = 'alojamiento';

    public $idAlojamiento;
    public $nombre;
    public $tipo;
    public $direccion;
    public $ciudad;
    public $pais;
    public $telefono;
    public $email;
    public $estrellas;
    public $descripcion;
    public $estado;
    public $cantLujo;
    public $cantEstandar;
    public $cantSuite;
    public $precioLujo;
    public $precioEstandar;
    public $precioSuite;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, tipo, direccion, ciudad, pais, telefono, email, estrellas, descripcion, estado, cantLujo, cantEstandar, cantSuite, precioLujo, precioEstandar, precioSuite) 
                  VALUES (:nombre, :tipo, :direccion, :ciudad, :pais, :telefono, :email, :estrellas, :descripcion, :estado, :cantLujo, :cantEstandar, :cantSuite, :precioLujo, :precioEstandar, :precioSuite)";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->ciudad = htmlspecialchars(strip_tags($this->ciudad));
        $this->pais = htmlspecialchars(strip_tags($this->pais));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->estrellas = (int)$this->estrellas;
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->cantLujo = (int)$this->cantLujo;
        $this->cantEstandar = (int)$this->cantEstandar;
        $this->cantSuite = $this->cantSuite !== null ? (int)$this->cantSuite : null;
        $this->precioLujo = $this->precioLujo !== null ? (float)$this->precioLujo : null;
        $this->precioEstandar = $this->precioEstandar !== null ? (float)$this->precioEstandar : null;
        $this->precioSuite = $this->precioSuite !== null ? (float)$this->precioSuite : null;

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':ciudad', $this->ciudad);
        $stmt->bindParam(':pais', $this->pais);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':estrellas', $this->estrellas, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':cantLujo', $this->cantLujo, PDO::PARAM_INT);
        $stmt->bindParam(':cantEstandar', $this->cantEstandar, PDO::PARAM_INT);
        $stmt->bindParam(':cantSuite', $this->cantSuite, PDO::PARAM_INT);
        $stmt->bindParam(':precioLujo', $this->precioLujo, PDO::PARAM_STR);
        $stmt->bindParam(':precioEstandar', $this->precioEstandar, PDO::PARAM_STR);
        $stmt->bindParam(':precioSuite', $this->precioSuite, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $this->idAlojamiento = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, tipo = :tipo, direccion = :direccion, ciudad = :ciudad, 
                      pais = :pais, telefono = :telefono, email = :email, estrellas = :estrellas, 
                      descripcion = :descripcion, estado = :estado, cantLujo = :cantLujo, 
                      cantEstandar = :cantEstandar, cantSuite = :cantSuite, precioLujo = :precioLujo, 
                      precioEstandar = :precioEstandar, precioSuite = :precioSuite 
                  WHERE idAlojamiento = :id";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->ciudad = htmlspecialchars(strip_tags($this->ciudad));
        $this->pais = htmlspecialchars(strip_tags($this->pais));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->estrellas = (int)$this->estrellas;
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->cantLujo = (int)$this->cantLujo;
        $this->cantEstandar = (int)$this->cantEstandar;
        $this->cantSuite = $this->cantSuite !== null ? (int)$this->cantSuite : null;
        $this->precioLujo = $this->precioLujo !== null ? (float)$this->precioLujo : null;
        $this->precioEstandar = $this->precioEstandar !== null ? (float)$this->precioEstandar : null;
        $this->precioSuite = $this->precioSuite !== null ? (float)$this->precioSuite : null;
        $this->idAlojamiento = (int)$this->idAlojamiento;

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':ciudad', $this->ciudad);
        $stmt->bindParam(':pais', $this->pais);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':estrellas', $this->estrellas, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':cantLujo', $this->cantLujo, PDO::PARAM_INT);
        $stmt->bindParam(':cantEstandar', $this->cantEstandar, PDO::PARAM_INT);
        $stmt->bindParam(':cantSuite', $this->cantSuite, PDO::PARAM_INT);
        $stmt->bindParam(':precioLujo', $this->precioLujo, PDO::PARAM_STR);
        $stmt->bindParam(':precioEstandar', $this->precioEstandar, PDO::PARAM_STR);
        $stmt->bindParam(':precioSuite', $this->precioSuite, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->idAlojamiento, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>