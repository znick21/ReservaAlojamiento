<?php
require_once 'models/Alojamiento.php';

class AlojamientoController {
    private $alojamiento;

    public function __construct() {
        $this->alojamiento = new Alojamiento();
    }

    public function create($data) {
        $this->alojamiento->nombre = $data['nombre'] ?? '';
        $this->alojamiento->tipo = $data['tipo'] ?? '';
        $this->alojamiento->direccion = $data['direccion'] ?? '';
        $this->alojamiento->ciudad = $data['ciudad'] ?? null;
        $this->alojamiento->pais = $data['pais'] ?? null;
        $this->alojamiento->telefono = $data['telefono'] ?? null;
        $this->alojamiento->email = $data['email'] ?? null;
        $this->alojamiento->estrellas = $data['estrellas'] ?? 1;
        $this->alojamiento->descripcion = $data['descripcion'] ?? null;
        $this->alojamiento->estado = $data['estado'] ?? 'Activo';
        $this->alojamiento->cantLujo = $data['cantLujo'] ?? 0;
        $this->alojamiento->cantEstandar = $data['cantEstandar'] ?? 0;
        $this->alojamiento->cantSuite = $data['cantSuite'] ?? null;
        $this->alojamiento->precioLujo = $data['precioLujo'] ?? null;
        $this->alojamiento->precioEstandar = $data['precioEstandar'] ?? null;
        $this->alojamiento->precioSuite = $data['precioSuite'] ?? null;

        if ($this->alojamiento->create()) {
            $db = new Database();
            $conn = $db->getConnection();

            // Crear habitaciones automáticamente
            $tipos = [
                ['tipo' => 'Lujo', 'cantidad' => $this->alojamiento->cantLujo, 'precio' => $this->alojamiento->precioLujo],
                ['tipo' => 'Estándar', 'cantidad' => $this->alojamiento->cantEstandar, 'precio' => $this->alojamiento->precioEstandar],
            ];
            if ($this->alojamiento->cantSuite && $this->alojamiento->precioSuite) {
                $tipos[] = ['tipo' => 'Suite', 'cantidad' => $this->alojamiento->cantSuite, 'precio' => $this->alojamiento->precioSuite];
            }

            $habitacionQuery = "INSERT INTO habitacion (idAlojamiento, tipoHabitacion, numeroHabitacion, precio, estado) 
                               VALUES (:idAlojamiento, :tipoHabitacion, :numeroHabitacion, :precio, 'Disponible')";
            $stmt = $conn->prepare($habitacionQuery);

            $numeroBase = 101; // Empezar desde 101
            foreach ($tipos as $tipoData) {
                for ($i = 0; $i < $tipoData['cantidad']; $i++) {
                    $numeroHabitacion = $numeroBase + $i;
                    $stmt->bindParam(':idAlojamiento', $this->alojamiento->idAlojamiento, PDO::PARAM_INT);
                    $stmt->bindParam(':tipoHabitacion', $tipoData['tipo']);
                    $stmt->bindParam(':numeroHabitacion', $numeroHabitacion);
                    $stmt->bindParam(':precio', $tipoData['precio'], PDO::PARAM_STR);
                    $stmt->execute();
                }
                $numeroBase += $tipoData['cantidad']; // Incrementar para el siguiente tipo
            }

            return ['status' => 'success', 'id' => $this->alojamiento->idAlojamiento];
        }
        return ['status' => 'error', 'message' => 'No se pudo crear el alojamiento'];
    }

    public function get($id) {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM alojamiento WHERE idAlojamiento = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $alojamiento = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($alojamiento) {
            return ['status' => 'success', 'data' => $alojamiento];
        }
        return ['status' => 'error', 'message' => 'Alojamiento no encontrado'];
    }

    public function getAll() {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT * FROM alojamiento");
        $alojamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['status' => 'success', 'data' => $alojamientos];
    }

    public function update($id, $data) {
        $this->alojamiento->idAlojamiento = $id;
        $this->alojamiento->nombre = $data['nombre'] ?? '';
        $this->alojamiento->tipo = $data['tipo'] ?? '';
        $this->alojamiento->direccion = $data['direccion'] ?? '';
        $this->alojamiento->ciudad = $data['ciudad'] ?? null;
        $this->alojamiento->pais = $data['pais'] ?? null;
        $this->alojamiento->telefono = $data['telefono'] ?? null;
        $this->alojamiento->email = $data['email'] ?? null;
        $this->alojamiento->estrellas = $data['estrellas'] ?? 1;
        $this->alojamiento->descripcion = $data['descripcion'] ?? null;
        $this->alojamiento->estado = $data['estado'] ?? 'Activo';
        $this->alojamiento->cantLujo = $data['cantLujo'] ?? 0;
        $this->alojamiento->cantEstandar = $data['cantEstandar'] ?? 0;
        $this->alojamiento->cantSuite = $data['cantSuite'] ?? null;
        $this->alojamiento->precioLujo = $data['precioLujo'] ?? null;
        $this->alojamiento->precioEstandar = $data['precioEstandar'] ?? null;
        $this->alojamiento->precioSuite = $data['precioSuite'] ?? null;

        if ($this->alojamiento->update()) {
            $db = new Database();
            $conn = $db->getConnection();

            // Eliminar habitaciones existentes
            $deleteQuery = "DELETE FROM habitacion WHERE idAlojamiento = :idAlojamiento";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bindParam(':idAlojamiento', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Crear nuevas habitaciones
            $tipos = [
                ['tipo' => 'Lujo', 'cantidad' => $this->alojamiento->cantLujo, 'precio' => $this->alojamiento->precioLujo],
                ['tipo' => 'Estándar', 'cantidad' => $this->alojamiento->cantEstandar, 'precio' => $this->alojamiento->precioEstandar],
            ];
            if ($this->alojamiento->cantSuite && $this->alojamiento->precioSuite) {
                $tipos[] = ['tipo' => 'Suite', 'cantidad' => $this->alojamiento->cantSuite, 'precio' => $this->alojamiento->precioSuite];
            }

            $habitacionQuery = "INSERT INTO habitacion (idAlojamiento, tipoHabitacion, numeroHabitacion, precio, estado) 
                               VALUES (:idAlojamiento, :tipoHabitacion, :numeroHabitacion, :precio, 'Disponible')";
            $stmt = $conn->prepare($habitacionQuery);

            $numeroBase = 101;
            foreach ($tipos as $tipoData) {
                for ($i = 0; $i < $tipoData['cantidad']; $i++) {
                    $numeroHabitacion = $numeroBase + $i;
                    $stmt->bindParam(':idAlojamiento', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':tipoHabitacion', $tipoData['tipo']);
                    $stmt->bindParam(':numeroHabitacion', $numeroHabitacion);
                    $stmt->bindParam(':precio', $tipoData['precio'], PDO::PARAM_STR);
                    $stmt->execute();
                }
                $numeroBase += $tipoData['cantidad'];
            }

            return ['status' => 'success', 'message' => 'Alojamiento actualizado'];
        }
        return ['status' => 'error', 'message' => 'No se pudo actualizar el alojamiento'];
    }

    public function delete($id) {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("DELETE FROM alojamiento WHERE idAlojamiento = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Alojamiento eliminado'];
        }
        return ['status' => 'error', 'message' => 'No se pudo eliminar el alojamiento'];
    }
}
?>