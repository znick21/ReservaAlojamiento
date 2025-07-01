<?php
ob_start();
?>
<h2>Nuevo Alojamiento</h2>
<form method="post" onsubmit="mostrarCampos()">
    <label>Nombre: <input type="text" name="nombre" required></label><br>
    <label>Tipo: 
        <select name="tipo" id="tipoAlojamiento" onchange="mostrarCampos()" required>
            <option value="">Seleccione...</option>
            <option value="Hotel">Hotel</option>
            <option value="Hostal">Hostal</option>
            <option value="DepartamentoTuristico">Departamento Turístico</option>
        </select>
    </label><br>
    <label>Precio por noche: <input type="number" step="0.01" name="precioPorNoche" required></label><br>
    <label>Capacidad: <input type="number" name="capacidad" required></label><br>

    <!-- Campos específicos para Hotel -->
    <div id="camposHotel" style="display: none;">
        <label>Estrellas: 
            <select name="estrellas">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </label><br>
        <label>Teléfono de contacto: <input type="text" name="telefonoContacto"></label><br>
    </div>

    <!-- Campos específicos para Hostal -->
    <div id="camposHostal" style="display: none;">
        <label>Hora de Check-in: <input type="text" name="horaCheckIn" placeholder="Ej: 14:00"></label><br>
    </div>

    <!-- Campos específicos para Departamento Turístico -->
    <div id="camposDepartamento" style="display: none;">
        <label>Número de habitaciones: <input type="number" name="numeroHabitaciones"></label><br>
        <label>Capacidad máxima: <input type="number" name="capacidadMaxima"></label><br>
        <label>Amueblado: <input type="checkbox" name="amueblado" value="1"></label><br>
        <label>Cocina equipada: <input type="checkbox" name="cocinaEquipada" value="1"></label><br>
    </div>

    <button type="submit">Crear</button>
</form>

<script>
function mostrarCampos() {
    const tipo = document.getElementById('tipoAlojamiento').value;
    document.getElementById('camposHotel').style.display = tipo === 'Hotel' ? 'block' : 'none';
    document.getElementById('camposHostal').style.display = tipo === 'Hostal' ? 'block' : 'none';
    document.getElementById('camposDepartamento').style.display = tipo === 'DepartamentoTuristico' ? 'block' : 'none';
}
</script>
<?php
$content = ob_get_clean();
include_once 'vistas/plantillas/principal.php';
?>