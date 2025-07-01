<?php if (!empty($alojamientos)): ?>
    <h2>Lista de Alojamientos</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($alojamientos as $alojamiento): ?>
            <tr>
                <td><?php echo htmlspecialchars($alojamiento['idAlojamiento']); ?></td>
                <td><?php echo htmlspecialchars($alojamiento['nombre']); ?></td>
                <td><?php echo htmlspecialchars($alojamiento['tipo']); ?></td>
                <td>
                    <a href="index.php?controller=alojamiento&action=editar&id=<?php echo $alojamiento['idAlojamiento']; ?>">Editar</a>
                    <a href="index.php?controller=alojamiento&action=eliminar&id=<?php echo $alojamiento['idAlojamiento']; ?>" onclick="return confirm('¿Seguro?');">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No hay alojamientos registrados.</p>
<?php endif; ?>