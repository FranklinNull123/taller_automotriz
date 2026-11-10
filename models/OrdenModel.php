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

    public function registrarOrden($vehiculoId, $descripcionFalla) {
        $stmt = $this->conn->prepare(
            "INSERT INTO ordenes_servicio (vehiculo_id, descripcion_falla)
             VALUES (:vehiculo_id, :descripcion_falla)"
        );
        return $stmt->execute([
            ':vehiculo_id' => (int) $vehiculoId,
            ':descripcion_falla' => $descripcionFalla
        ]);
    }

    public function actualizarEstado($id, $estado) {
        $estadosPermitidos = ['Pendiente', 'En Proceso', 'Terminado', 'Entregado'];
        if (!in_array($estado, $estadosPermitidos, true)) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "UPDATE ordenes_servicio
             SET estado = :estado
             WHERE id = :id"
        );
        return $stmt->execute([
            ':estado' => $estado,
            ':id' => (int) $id
        ]);
    }
}
?>