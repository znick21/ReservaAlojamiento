<?php
require_once 'config/database.php';
require_once 'modelos/Reserva.php';
require_once 'modelos/Alojamiento.php';

class ReservaController {
    private $db;
    private $reserva;
    private $alojamiento;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->reserva = new Reserva($this->db);
        $this->alojamiento = new Alojamiento($this->db);
    }

    // Listar todas las reservas
    public function index() {
        $stmt = $this->reserva->read();
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include_once 'vistas/reserva/index.php';
    }

    // Crear una nueva reserva
    public function create() {
        if ($_POST) {
            $this->reserva->fechaInicio = $_POST['fechaInicio'];
            $this->reserva->estado = $_POST['estado'];
            $this->reserva->precioTotal = $_POST['precioTotal'];
            $this->reserva->numeroHuespedes = $_POST['numeroHuespedes'];
            $this->reserva->idAlojamiento = $_POST['idAlojamiento'];

            if ($this->reserva->create()) {
                header("Location: index.php?controller=reserva&action=index");
                exit;
            } else {
                echo "Error al crear la reserva.";
            }
        }
        // Obtener lista de alojamientos para el formulario
        $stmt = $this->alojamiento->read();
        $alojamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include_once 'vistas/reserva/crear.php';
    }
}
?>