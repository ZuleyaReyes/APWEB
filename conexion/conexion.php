<?php
class Conectar {
protected $conexion_bd;

protected function conectar_bd() {
try {
// Variables correctas de Railway
$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');
$port = getenv('DB_PORT');

// DSN
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

// Conexión PDO
$this->conexion_bd = new PDO($dsn, $user, $pass, [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

return $this->conexion_bd;

} catch (Exception $e) {
echo json_encode([
"error" => "Error de conexión",
"details" => $e->getMessage(),
"host" => $host,
"db" => $db,
"user" => $user,
"port" => $port
]);
die();
}
}
}