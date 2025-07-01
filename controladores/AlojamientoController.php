<?php
require_once 'config/database.php';
require_once 'modelos/Alojamiento.php';
require_once 'modelos/Hotel.php';
require_once 'modelos/Hostal.php';
require_once 'modelos/DepartamentoTuristico.php';

class AlojamientoController {
    private $db;
    private $alojamiento;
    private $hotel;
    private $hostal;
    private $departamento;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->alojamiento = new Alojamiento($this->db);
        $this->hotel = new Hotel($this->db);
        $this->hostal = new Hostal($this->db);
        $this->departamento = new DepartamentoTuristico($this->db);
    }

    public function index() {
        $stmt = $this->alojamiento->read();
        $alojamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include_once 'vistas/alojamiento/index.php';
    }

    public function create() {
        if ($_POST) {
            $this->alojamiento->nombre = $_POST['nombre'];
            $this->alojamiento->tipo = $_POST['tipo'];
            $this->alojamiento->precioPorNoche = $_POST['precioPorNoche'];
            $this->alojamiento->capacidad = $_POST['capacidad'];

            $idAlojamiento = $this->alojamiento->create();

            if ($idAlojamiento) {
                if ($_POST['tipo'] == 'Hotel') {
                    $this->hotel->idAlojamiento = $idAlojamiento;
                    $this->hotel->estrellas = $_POST['estrellas'];
                    $this->hotel->telefonoContacto = $_POST['telefonoContacto'];
                    $this->hotel->create();
                } elseif ($_POST['tipo'] == 'Hostal') {
                    $this->hostal->idAlojamiento = $idAlojamiento;
                    $this->hostal->horaCheckIn = $_POST['horaCheckIn'];
                    $this->hostal->create();
                } elseif ($_POST['tipo'] == 'DepartamentoTuristico') {
                    $this->departamento->idAlojamiento = $idAlojamiento;
                    $this->departamento->numeroHabitaciones = $_POST['numeroHabitaciones'];
                    $this->departamento->capacidadMaxima = $_POST['capacidadMaxima'];
                    $this->departamento->amueblado = isset($_POST['amueblado']) ? 1 : 0;
                    $this->departamento->cocinaEquipada = isset($_POST['cocinaEquipada']) ? 1 : 0;
                    $this->departamento->create();
                }
                header("Location: index.php?controller=alojamiento&action=index");
                exit;
            } else {
                echo "Error al crear el alojamiento.";
            }
        }
        include_once 'vistas/alojamiento/crear.php';
    }
}
?>