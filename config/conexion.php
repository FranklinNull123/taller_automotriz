<?php
require_once __DIR__ . '/bootstrap.php';

class Conexion {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $this->host = envValue('DB_HOST', '127.0.0.1');
        $this->port = envValue('DB_PORT', '3306');
        $this->db_name = envValue('DB_NAME', 'taller_automotriz');
        $this->username = envValue('DB_USERNAME', 'root');
        $this->password = envValue('DB_PASSWORD', '');
    }

    public function conectar() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            appLogger()->error('Error de conexión a la base de datos', [
                'exception' => $exception->getMessage()
            ]);
            throw new RuntimeException('No se pudo conectar con la base de datos.');
        }

        return $this->conn;
    }
}
?>