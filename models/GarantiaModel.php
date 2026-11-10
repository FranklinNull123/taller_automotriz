<?php
require_once __DIR__ . '/../config/conexion.php';

class GarantiaModel {
    private $conn;

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    public function listarGarantias() {
        $stmt = $this->conn->prepare("
            SELECT g.*, o.id as orden_id, c.nombre as cliente, c.telefono 
            FROM garantias g 
            INNER JOIN ordenes_servicio o ON g.orden_id = o.id 
            INNER JOIN vehiculos v ON o.vehiculo_id = v.id 
            INNER JOIN clientes c ON v.cliente_id = c.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>