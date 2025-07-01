<?php
ob_start();
?>
<h2>Nueva Reserva</h2>
<form method="post">
    <label>Fecha de Inicio: <input type="date" name="fechaInicio" required></label><br>
    <label>Estado: 
        <select name="estado" required>
            <option value="Pendiente">Pendiente</option>
            <option value="Confirmada">Confirmada</option>
            <option value="Cancelada">Cancelada</option>
        </select>
    </label><br>
    <label>Precio Total: <input type="number" step="0.01" name="precioTotal" required></label><br>
    <label>Número de Huespedes: <input type="number" name="numeroHuespedes" required></label><br>
    <label>Alojamiento: 
        <select name="idAlojamiento" required>
            <option value="">Seleccione...</option>
            <?php foreach($alojamientos as $alo): ?>
                <option value="<?php echo $alo['idAlojamiento']; ?>">
                    <?php echo $alo['nombre'] . " (" . $alo['tipo'] . ")"; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <button type="submit">Crear</button>
</form>
<?php
$content = ob_get_clean();
include_once 'vistas/plantillas/principal.php';
?>