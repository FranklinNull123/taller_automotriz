<?php
class Conexion {
    private $host = "localhost";
    private $db_name = "taller_automotriz";
    private $username = "root"; // Cambia si tu usuario local es diferente
    private $password = "";     // Añade tu contraseña si configuraste una en tu servidor local
    public $conn;

    public function conectar() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log('Error de conexión a la base de datos: ' . $exception->getMessage());
            throw new RuntimeException('No se pudo conectar con la base de datos.');
        }

        return $this->conn;
    }
}
?>