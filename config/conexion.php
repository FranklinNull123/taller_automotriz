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
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Configurar codificación para caracteres especiales y manejo de errores
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>