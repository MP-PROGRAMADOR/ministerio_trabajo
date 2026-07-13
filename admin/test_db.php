<?php
session_start();
echo "<h1>Test Simple</h1>";
echo "ID Usuario: " . ($_SESSION['id_usuario'] ?? 'NO') . "<br>";
echo "Rol: " . ($_SESSION['rol'] ?? 'NO') . "<br>";

include_once '../conexion/conexion.php';

if (isset($pdo)) {
    echo "✅ Conexión OK<br>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM buscadores_empleo");
    $row = $stmt->fetch();
    echo "Total buscadores: " . $row['total'] . "<br>";
} else {
    echo "❌ Sin conexión<br>";
}
?>