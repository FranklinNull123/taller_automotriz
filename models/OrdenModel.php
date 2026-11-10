<?php
require_once __DIR__ . "/../config/conexion.php";

class OrdenModel {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
    }

    public function listarOrdenes() {
        $sql =         "SELECT o.id, o.vehiculo_id, v.placa, o.descripcion_falla, o.estado, o.fecha_ingreso, o.total,
                   COALESCE((SELECT SUM(p.monto) FROM pagos p WHERE p.orden_id = o.id), 0) AS total_pagado,
                   GREATEST(o.total - COALESCE((SELECT SUM(p2.monto) FROM pagos p2 WHERE p2.orden_id = o.id), 0), 0) AS saldo,
                   COALESCE(c.estado, 'Pendiente') AS cotizacion_estado,
                   (SELECT COUNT(*) FROM orden_detalles od WHERE od.orden_id = o.id) AS repuestos_asignados
            FROM ordenes_servicio o
            INNER JOIN vehiculos v ON o.vehiculo_id = v.id
            LEFT JOIN cotizaciones c ON c.orden_id = o.id
            ORDER BY o.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarOrden($vehiculoId, $descripcionFalla, $repuestoId = null, $cantidad = 0) {
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare(
                "INSERT INTO ordenes_servicio (vehiculo_id, descripcion_falla)
                 VALUES (:vehiculo_id, :descripcion_falla)"
            );
            $stmt->execute([
                ':vehiculo_id' => (int) $vehiculoId,
                ':descripcion_falla' => $descripcionFalla
            ]);
            $ordenId = (int) $this->conn->lastInsertId();
            $this->registrarHistorial($ordenId, null, 'Pendiente');
            $quote = $this->conn->prepare(
                "INSERT INTO cotizaciones (orden_id, subtotal, total) VALUES (:orden_id, 0, 0)"
            );
            $quote->execute([':orden_id' => $ordenId]);

            if ($repuestoId !== null && (int) $repuestoId > 0 && (int) $cantidad > 0) {
                $stmt = $this->conn->prepare(
                    "SELECT precio_unitario FROM repuestos WHERE id = :id FOR UPDATE"
                );
                $stmt->execute([':id' => (int) $repuestoId]);
                $precio = $stmt->fetchColumn();
                if ($precio === false) {
                    throw new RuntimeException('El repuesto seleccionado no existe.');
                }
                $stmt = $this->conn->prepare(
                    "INSERT INTO orden_detalles (orden_id, repuesto_id, cantidad, precio_unitario)
                     VALUES (:orden_id, :repuesto_id, :cantidad, :precio)"
                );
                $stmt->execute([
                    ':orden_id' => $ordenId,
                    ':repuesto_id' => (int) $repuestoId,
                    ':cantidad' => (int) $cantidad,
                    ':precio' => $precio
                ]);
                $this->actualizarTotal($ordenId);
            }
            $this->conn->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function actualizarEstado($id, $estado) {
        $estadosPermitidos = ['Pendiente', 'En Proceso', 'Terminado', 'Entregado'];
        if (!in_array($estado, $estadosPermitidos, true)) {
            return false;
        }

        $transiciones = [
            'Pendiente' => ['Pendiente', 'En Proceso'],
            'En Proceso' => ['En Proceso', 'Terminado'],
            'Terminado' => ['Terminado', 'Entregado'],
            'Entregado' => ['Entregado']
        ];

        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("SELECT estado FROM ordenes_servicio WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => (int) $id]);
            $estadoAnterior = $stmt->fetchColumn();
            if ($estadoAnterior === false || !in_array($estado, $transiciones[$estadoAnterior], true)) {
                throw new RuntimeException('Transición de estado no permitida.');
            }

            if ($estado === 'Entregado' && $estadoAnterior !== 'Entregado') {
                $this->consumirRepuestos((int) $id);
            }
            $stmt = $this->conn->prepare("UPDATE ordenes_servicio SET estado = :estado WHERE id = :id");
            $stmt->execute([':estado' => $estado, ':id' => (int) $id]);
            if ($estadoAnterior !== $estado) {
                $this->registrarHistorial((int) $id, $estadoAnterior, $estado);
            }
            $this->conn->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function listarHistorial($ordenId) {
        $stmt = $this->conn->prepare(
            "SELECT h.*, u.usuario
             FROM orden_historial h
             LEFT JOIN usuarios u ON u.id = h.usuario_id
             WHERE h.orden_id = :orden_id ORDER BY h.creado_en ASC, h.id ASC"
        );
        $stmt->execute([':orden_id' => (int) $ordenId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarPago($ordenId, $monto, $metodo, $referencia = '') {
        $metodosPermitidos = ['efectivo', 'tarjeta', 'transferencia', 'otro'];
        $monto = round((float) $monto, 2);
        if ($monto <= 0 || !in_array($metodo, $metodosPermitidos, true)) {
            return false;
        }

        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare(
                "SELECT o.total, COALESCE(SUM(p.monto), 0) AS pagado
                 FROM ordenes_servicio o
                 LEFT JOIN pagos p ON p.orden_id = o.id
                 WHERE o.id = :id
                 GROUP BY o.id, o.total
                 FOR UPDATE"
            );
            $stmt->execute([':id' => (int) $ordenId]);
            $orden = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$orden || $monto > max(0, (float) $orden['total'] - (float) $orden['pagado'])) {
                throw new RuntimeException('El abono supera el saldo pendiente.');
            }

            $stmt = $this->conn->prepare(
                "INSERT INTO pagos (orden_id, usuario_id, monto, metodo, referencia)
                 VALUES (:orden_id, :usuario_id, :monto, :metodo, :referencia)"
            );
            $stmt->execute([
                ':orden_id' => (int) $ordenId,
                ':usuario_id' => !empty($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null,
                ':monto' => $monto,
                ':metodo' => $metodo,
                ':referencia' => trim($referencia) !== '' ? trim($referencia) : null
            ]);
            $this->conn->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function listarPagos($ordenId) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, u.usuario
             FROM pagos p
             LEFT JOIN usuarios u ON u.id = p.usuario_id
             WHERE p.orden_id = :orden_id ORDER BY p.creado_en DESC, p.id DESC"
        );
        $stmt->execute([':orden_id' => (int) $ordenId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerOrdenCompleta($ordenId) {
        $stmt = $this->conn->prepare(
            "SELECT o.*, v.placa, v.marca, v.modelo, v.anio,
                    c.nombre AS cliente, c.email, c.telefono,
                    COALESCE((SELECT SUM(p.monto) FROM pagos p WHERE p.orden_id = o.id), 0) AS total_pagado,
                    GREATEST(o.total - COALESCE((SELECT SUM(p2.monto) FROM pagos p2 WHERE p2.orden_id = o.id), 0), 0) AS saldo
             FROM ordenes_servicio o
             INNER JOIN vehiculos v ON v.id = o.vehiculo_id
             INNER JOIN clientes c ON c.id = v.cliente_id
             WHERE o.id = :id"
        );
        $stmt->execute([':id' => (int) $ordenId]);
        $orden = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$orden) {
            return null;
        }
        $stmt = $this->conn->prepare(
            "SELECT od.*, r.codigo, r.descripcion
             FROM orden_detalles od INNER JOIN repuestos r ON r.id = od.repuesto_id
             WHERE od.orden_id = :id"
        );
        $stmt->execute([':id' => (int) $ordenId]);
        $orden['detalles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $orden;
    }

    public function actualizarCotizacion($ordenId, $estado) {
        if (!in_array($estado, ['Aprobada', 'Rechazada'], true)) {
            return false;
        }
        $stmt = $this->conn->prepare(
            "UPDATE cotizaciones
             SET estado = :estado, aprobada_en = CASE WHEN :estado2 = 'Aprobada' THEN NOW() ELSE NULL END
             WHERE orden_id = :orden_id"
        );
        return $stmt->execute([
            ':estado' => $estado,
            ':estado2' => $estado,
            ':orden_id' => (int) $ordenId
        ]);
    }

    private function registrarHistorial($ordenId, $estadoAnterior, $estadoNuevo) {
        $stmt = $this->conn->prepare(
            "INSERT INTO orden_historial (orden_id, usuario_id, estado_anterior, estado_nuevo)
             VALUES (:orden_id, :usuario_id, :estado_anterior, :estado_nuevo)"
        );
        $stmt->execute([
            ':orden_id' => $ordenId,
            ':usuario_id' => !empty($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null,
            ':estado_anterior' => $estadoAnterior,
            ':estado_nuevo' => $estadoNuevo
        ]);
    }

    private function actualizarTotal($ordenId) {
        $stmt = $this->conn->prepare(
            "UPDATE ordenes_servicio o
             SET total = (SELECT COALESCE(SUM(cantidad * precio_unitario), 0)
                          FROM orden_detalles WHERE orden_id = o.id)
             WHERE o.id = :id"
        );
        $stmt->execute([':id' => $ordenId]);
    }

    private function consumirRepuestos($ordenId) {
        $stmt = $this->conn->prepare(
            "SELECT od.id, od.repuesto_id, od.cantidad, od.consumido, r.stock
             FROM orden_detalles od
             INNER JOIN repuestos r ON r.id = od.repuesto_id
             WHERE od.orden_id = :orden_id FOR UPDATE"
        );
        $stmt->execute([':orden_id' => $ordenId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $detalle) {
            if ((int) $detalle['consumido'] === 1) {
                continue;
            }
            if ((int) $detalle['stock'] < (int) $detalle['cantidad']) {
                throw new RuntimeException('Stock insuficiente para cerrar la orden.');
            }
            $nuevoStock = (int) $detalle['stock'] - (int) $detalle['cantidad'];
            $update = $this->conn->prepare("UPDATE repuestos SET stock = :stock WHERE id = :id");
            $update->execute([':stock' => $nuevoStock, ':id' => (int) $detalle['repuesto_id']]);
            $movement = $this->conn->prepare(
                "INSERT INTO movimientos_inventario
                 (repuesto_id, orden_id, usuario_id, tipo, cantidad, stock_anterior, stock_nuevo)
                 VALUES (:repuesto_id, :orden_id, :usuario_id, 'salida', :cantidad, :anterior, :nuevo)"
            );
            $movement->execute([
                ':repuesto_id' => (int) $detalle['repuesto_id'],
                ':orden_id' => $ordenId,
                ':usuario_id' => !empty($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null,
                ':cantidad' => (int) $detalle['cantidad'],
                ':anterior' => (int) $detalle['stock'],
                ':nuevo' => $nuevoStock
            ]);
            $mark = $this->conn->prepare("UPDATE orden_detalles SET consumido = 1 WHERE id = :id");
            $mark->execute([':id' => (int) $detalle['id']]);
        }
    }
}
?>