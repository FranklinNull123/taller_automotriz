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

    public function obtenerDashboard() {
        $estadisticas = $this->obtenerEstadisticas();
        $stmt = $this->conn->prepare(
            "SELECT estado, COUNT(*) AS total
             FROM ordenes_servicio
             GROUP BY estado
             ORDER BY FIELD(estado, 'Pendiente', 'En Proceso', 'Terminado', 'Entregado')"
        );
        $stmt->execute();
        $porEstado = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $porEstado[$fila['estado']] = (int) $fila['total'];
        }

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM repuestos
             WHERE stock <= 5"
        );
        $stmt->execute();
        $stockBajo = (int) $stmt->fetchColumn();

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM garantias
             WHERE estado = 'Activa'
               AND fecha_vencimiento BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)"
        );
        $stmt->execute();
        $garantiasPorVencer = (int) $stmt->fetchColumn();

        return [
            'estadisticas' => $estadisticas,
            'por_estado' => $porEstado,
            'stock_bajo' => $stockBajo,
            'garantias_por_vencer' => $garantiasPorVencer
        ];
    }
}
?>