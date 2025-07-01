<?php
$editar = isset($editar) && $editar;
$alojamiento = isset($alojamiento) ? $alojamiento : [];
$action = $editar ? "index.php?controller=alojamiento&action=editar&id=" . $alojamiento['idAlojamiento'] : "index.php?controller=alojamiento&action=crear";
?>

<h2><?php echo $editar ? 'Editar Alojamiento' : 'Crear Alojamiento'; ?></h2>
<form action="<?php echo $action; ?>" method="POST" id="alojamientoForm">
    <label>Nombre: <input type="text" name="nombre" value="<?php echo htmlspecialchars($alojamiento['nombre'] ?? ''); ?>" required></label><br>
    <label>Tipo:
        <select name="tipo" id="tipoAlojamiento" required>
            <option value="Hotel" <?php echo ($alojamiento['tipo'] ?? '') === 'Hotel' ? 'selected' : ''; ?>>Hotel</option>
            <option value="Hostal" <?php echo ($alojamiento['tipo'] ?? '') === 'Hostal' ? 'selected' : ''; ?>>Hostal</option>
            <option value="DepartamentoTuristico" <?php echo ($alojamiento['tipo'] ?? '') === 'DepartamentoTuristico' ? 'selected' : ''; ?>>Departamento Turístico</option>
        </select>
    </label><br>
    <label>Dirección: <input type="text" name="direccion" value="<?php echo htmlspecialchars($alojamiento['direccion'] ?? ''); ?>" required></label><br>
    <label>Ciudad: <input type="text" name="ciudad" value="<?php echo htmlspecialchars($alojamiento['ciudad'] ?? ''); ?>"></label><br>
    <label>País: <input type="text" name="pais" value="<?php echo htmlspecialchars($alojamiento['pais'] ?? ''); ?>"></label><br>
    <label>Teléfono: <input type="text" name="telefono" value="<?php echo htmlspecialchars($alojamiento['telefono'] ?? ''); ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($alojamiento['email'] ?? ''); ?>"></label><br>
    <label>Estrellas: <input type="number" name="estrellas" min="1" max="5" value="<?php echo htmlspecialchars($alojamiento['estrellas'] ?? '1'); ?>" required></label><br>
    <label>Descripción: <textarea name="descripcion"><?php echo htmlspecialchars($alojamiento['descripcion'] ?? ''); ?></textarea></label><br>
    <label>Estado:
        <select name="estado" required>
            <option value="Activo" <?php echo ($alojamiento['estado'] ?? '') === 'Activo' ? 'selected' : ''; ?>>Activo</option>
            <option value="Inactivo" <?php echo ($alojamiento['estado'] ?? '') === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
            <option value="Mantenimiento" <?php echo ($alojamiento['estado'] ?? '') === 'Mantenimiento' ? 'selected' : ''; ?>>Mantenimiento</option>
        </select>
    </label><br>

    <!-- Cantidades y precios de habitaciones -->
    <h3>Habitaciones</h3>
    <label>Cantidad Lujo: <input type="number" name="cantLujo" value="<?php echo htmlspecialchars($alojamiento['cantLujo'] ?? 0); ?>" min="0" required></label><br>
    <label>Cantidad Estándar: <input type="number" name="cantEstandar" value="<?php echo htmlspecialchars($alojamiento['cantEstandar'] ?? 0); ?>" min="0" required></label><br>
    <label>Cantidad Suite: <input type="number" name="cantSuite" value="<?php echo htmlspecialchars($alojamiento['cantSuite'] ?? ''); ?>" min="0"></label><br>
    <label>Precio Lujo: <input type="number" step="0.01" name="precioLujo" value="<?php echo htmlspecialchars($alojamiento['precioLujo'] ?? ''); ?>" min="0" required></label><br>
    <label>Precio Estándar: <input type="number" step="0.01" name="precioEstandar" value="<?php echo htmlspecialchars($alojamiento['precioEstandar'] ?? ''); ?>" min="0" required></label><br>
    <label>Precio Suite: <input type="number" step="0.01" name="precioSuite" value="<?php echo htmlspecialchars($alojamiento['precioSuite'] ?? ''); ?>" min="0"></label><br>

    <button type="submit"><?php echo $editar ? 'Actualizar' : 'Crear'; ?></button>
</form>

<?php if (isset($response) && $response['status'] === 'error') { ?>
    <p class="error"><?php echo htmlspecialchars($response['message']); ?></p>
<?php } ?>