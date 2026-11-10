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

    public function registrarRepuesto($codigo, $descripcion, $categoria, $stock, $precioUnitario) {
        $stmt = $this->conn->prepare(
            "INSERT INTO repuestos (codigo, descripcion, categoria, stock, precio_unitario)
             VALUES (:codigo, :descripcion, :categoria, :stock, :precio_unitario)"
        );
        return $stmt->execute([
            ':codigo' => $codigo,
            ':descripcion' => $descripcion,
            ':categoria' => $categoria ?: null,
            ':stock' => max(0, (int) $stock),
            ':precio_unitario' => $precioUnitario
        ]);
    }

    public function eliminarRepuesto($id) {
        $stmt = $this->conn->prepare("DELETE FROM repuestos WHERE id = :id");
        return $stmt->execute([':id' => (int) $id]);
    }
}
?>