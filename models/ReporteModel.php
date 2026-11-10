<?php
require_once __DIR__ . '/../config/conexion.php';

class ReporteModel {
    private $conn;

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    public function obtenerEstadisticas() {
        $stmt1 = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM ordenes_servicio
             WHERE fecha_ingreso >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')
               AND fecha_ingreso < DATE_ADD(LAST_DAY(CURRENT_DATE), INTERVAL 1 DAY)"
        );
        $stmt1->execute();
        $ordenes_mes = $stmt1->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt2 = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM ordenes_servicio
             WHERE estado <> 'Entregado'"
        );
        $stmt2->execute();
        $en_taller = $stmt2->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt3 = $this->conn->prepare(
            "SELECT COALESCE(SUM(total), 0) AS total
             FROM ordenes_servicio
             WHERE fecha_ingreso >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')
               AND fecha_ingreso < DATE_ADD(LAST_DAY(CURRENT_DATE), INTERVAL 1 DAY)"
        );
        $stmt3->execute();
        $ingresos = $stmt3->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'ordenes_mes' => (int) $ordenes_mes,
            'en_taller' => (int) $en_taller,
            'ingresos' => (float) $ingresos
        ];
    }
}
?>