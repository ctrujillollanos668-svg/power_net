<?php
require __DIR__ . '/database.php';

$db = new Database();
$conn = $db->getConnection(); // ✅ correcto
// 🔹 Ruta correcta al SQL
$ruta = __DIR__ . '/../sql/power_net.sql';

// 🔹 Validar que exista el archivo
if (!file_exists($ruta)) {
    die("❌ No existe el archivo SQL en: " . $ruta);
}

// 🔹 Leer archivo
$sql = file_get_contents($ruta);

// 🔹 Validar contenido
if (!$sql) {
    die("❌ El archivo SQL está vacío o no se pudo leer");
}

// 🔹 Ejecutar
try {
    $conn->exec($sql);
    echo "✅ Migración ejecutada correctamente";
} catch (PDOException $e) {
    echo "❌ Error en SQL: " . $e->getMessage();
}