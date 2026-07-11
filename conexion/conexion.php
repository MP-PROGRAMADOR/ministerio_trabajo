<?php
// Configuración de las credenciales de la Base de Datos
$host     = 'localhost';
$db_name  = 'ministerio_trabajo'; // Reemplázalo por el nombre real de tu BD
$username = 'root';                     // Tu usuario de MySQL
$password = '';                         // Tu contraseña de MySQL

try {
    // 1. Configurar la cadena de conexión (DSN) con codificación UTF-8
    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    
    // 2. Crear la instancia PDO con opciones de seguridad
    $pdo = new PDO($dsn, $username, $password, [
        // Activa el modo de excepciones para capturar errores fácilmente
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        // Configura el modo de obtención por defecto a Array Asociativo (ej. $usuario['nombre'])
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // Desactiva emulación de consultas preparadas para mitigar ataques de Inyección SQL
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Opcional: Puedes descomentar la siguiente línea durante tus pruebas locales para saber que funciona
    // echo "Conexión institucional exitosa.";

} catch (PDOException $e) {
    // En producción, es crucial NO mostrar $e->getMessage() directamente al público
    // ya que podría revelar contraseñas o rutas del servidor.
    error_log("Error de conexión a la Base de Datos: " . $e->getMessage());
    
    die("Lo sentimos, ha ocurrido un problema técnico de conexión con el Sistema Nacional de Empleo. Por favor, inténtelo más tarde.");
}
?>