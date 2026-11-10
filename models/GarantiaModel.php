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
            SELECT g.*, o.id as orden_id, c.nombre as cliente, c.telefono,
                   CASE WHEN g.fecha_vencimiento < CURRENT_DATE THEN 'Vencida'
                        WHEN g.estado = 'Reclamada' THEN 'Reclamada'
                        ELSE 'Activa' END AS estado_actual
            FROM garantias g 
            INNER JOIN ordenes_servicio o ON g.orden_id = o.id 
            INNER JOIN vehiculos v ON o.vehiculo_id = v.id 
            INNER JOIN clientes c ON v.cliente_id = c.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPorVencer($dias = 30) {
        $dias = max(0, (int) $dias);
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM garantias
             WHERE estado = 'Activa'
             AND fecha_vencimiento BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL {$dias} DAY)"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function registrarGarantia($ordenId, $servicioRealizado, $fechaVencimiento) {
        $stmt = $this->conn->prepare(
            "INSERT INTO garantias (orden_id, servicio_realizado, fecha_vencimiento)
             VALUES (:orden_id, :servicio_realizado, :fecha_vencimiento)"
        );
        return $stmt->execute([
            ':orden_id' => (int) $ordenId,
            ':servicio_realizado' => $servicioRealizado,
            ':fecha_vencimiento' => $fechaVencimiento
        ]);
    }
}
?>