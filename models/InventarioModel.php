<?php
require_once __DIR__ . '/../config/conexion.php';

class InventarioModel {
    private $conn;

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    public function listarRepuestos() {
        $stmt = $this->conn->prepare("SELECT * FROM repuestos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>