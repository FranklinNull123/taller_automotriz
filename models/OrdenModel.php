<?php
require_once __DIR__ . "/../config/conexion.php";

class OrdenModel {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
    }

    public function listarOrdenes() {
        $sql = "SELECT o.id, v.placa, o.descripcion_falla, o.estado, o.fecha_ingreso 
                FROM ordenes_servicio o
                INNER JOIN vehiculos v ON o.vehiculo_id = v.id
                ORDER BY o.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>