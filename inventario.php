<?php
// 1. Iniciar sesión y aplicar el candado de seguridad
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// 2. Incluir la conexión a la base de datos
require_once 'conexion.php';

// 3. Capturar el término de búsqueda si existe
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// 4. Preparar la consulta SQL con o sin filtro de búsqueda
if (!empty($buscar)) {
    $buscar_escapado = $conn->real_escape_string($buscar);
    $sql = "SELECT p.id, p.nombre_producto, c.nombre_categoria, p.stock, p.precio
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            WHERE p.nombre_producto LIKE '%$buscar_escapado%' 
               OR c.nombre_categoria LIKE '%$buscar_escapado%'
            ORDER BY p.id ASC";
} else {
    $sql = "SELECT p.id, p.nombre_producto, c.nombre_categoria, p.stock, p.precio
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            ORDER BY p.id ASC";
}

// 5. Ejecutar la consulta
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Sistema de Ventas</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { color: #0f172a; margin: 0; }
        .btn-salir { background-color: #ef4444; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: bold; }
        .btn-salir:hover { background-color: #dc2626; }
        
        .toolbar { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .btn-nuevo { background: #3b82f6; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-nuevo:hover { background: #2563eb; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f1f5f9; color: #334155; font-weight: bold; }
        tr:hover { background-color: #f8fafc; }
        
        .stock-bajo { color: #dc2626; font-weight: bold; }
        
        .btn-editar { background-color: #f59e0b; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; margin-right: 5px; }
        .btn-editar:hover { background-color: #d97706; }
        
        .btn-eliminar { background-color: #ef4444; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; }
        .btn-eliminar:hover { background-color: #b91c1c; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Catálogo de Inventario</h2>
        <div>
            <span>Usuario: <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></strong></span>
            <a href="logout.php" class="btn-salir">Cerrar Sesión</a>
        </div>
    </div>

    <!-- Barra de Herramientas: Botón Nuevo + Buscador -->
    <div class="toolbar">
        <a href="nuevo_producto.php" class="btn-nuevo">+ Nuevo Producto</a>

        <!-- Formulario de Búsqueda -->
        <form method="GET" action="inventario.php" style="display: flex; gap: 10px;">
            <input type="text" name="buscar" placeholder="Buscar producto o categoría..." 
                   value="<?php echo htmlspecialchars($buscar); ?>" 
                   style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; width: 250px;">
            <button type="submit" style="background: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">🔍 Buscar</button>
            <a href="inventario.php" style="background: #64748b; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; display: inline-block;">Limpiar</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre del Producto</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Precio Unitario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($resultado && $resultado->num_rows > 0) {
            while($fila = $resultado->fetch_assoc()) {
                // Determinar si el stock es bajo para aplicar estilo rojo
                $claseStock = ($fila['stock'] < 10) ? 'stock-bajo' : '';
        ?>
            <tr>
                <td><?php echo $fila['id']; ?></td>
                <td><?php echo htmlspecialchars($fila['nombre_producto']); ?></td>
                <td><?php echo htmlspecialchars($fila['nombre_categoria']); ?></td>
                <td class="<?php echo $claseStock; ?>"><?php echo $fila['stock']; ?> unds.</td>
                <td>$<?php echo number_format($fila['precio'], 2); ?></td>
                <td>
                    <a href="editar_producto.php?id=<?php echo $fila['id']; ?>" class="btn-editar">✏️ Editar</a>
                    <a href="eliminar_producto.php?id=<?php echo $fila['id']; ?>" class="btn-eliminar" 
                       onclick="return confirm('¿Estás absolutamente seguro de eliminar el producto: <?php echo htmlspecialchars($fila['nombre_producto']); ?>?');">🗑️ Eliminar</a>
                </td>
            </tr>
        <?php 
            } // Fin del bucle while
        } else { 
        ?>
            <tr>
                <td colspan="6" style="text-align:center;">No se encontraron productos en el sistema.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php
// Liberar la memoria de la consulta
if ($resultado) {
    $resultado->free();
}
?>

</body>
</html>