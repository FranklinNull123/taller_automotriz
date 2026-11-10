<?php
require_once __DIR__ . '/../config/conexion.php';

class ReporteModel {
    private $conn;

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    public function obtenerEstadisticas() {
        $stmt1 = $this->conn->prepare("SELECT COUNT(*) as total FROM ordenes_servicio");
        $stmt1->execute();
        $ordenes_mes = $stmt1->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt2 = $this->conn->prepare("SELECT COUNT(*) as total FROM ordenes_servicio WHERE estado != 'Terminado'");
        $stmt2->execute();
        $en_taller = $stmt2->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'ordenes_mes' => $ordenes_mes,
            'en_taller' => $en_taller,
            'ingresos' => 0
        ];
    }
}
?>