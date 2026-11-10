<?php
require_once __DIR__ . '/../config/conexion.php';

class ClienteModel {
    private $conn;

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    public function listarClientes() {
        $stmt = $this->conn->prepare(
            "SELECT c.*, v.id AS vehiculo_id, v.placa, v.marca, v.modelo, v.anio
             FROM clientes c
             LEFT JOIN vehiculos v ON v.cliente_id = c.id
             ORDER BY c.id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarClienteYVehiculo($nombre, $documento, $telefono, $placa, $marca, $modelo, $anio) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT
                    (SELECT COUNT(*) FROM clientes WHERE documento = :documento) AS documento_existente,
                    (SELECT COUNT(*) FROM vehiculos WHERE placa = :placa) AS placa_existente"
            );
            $stmt->execute([
                ':documento' => $documento,
                ':placa' => $placa
            ]);
            $duplicados = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($duplicados['documento_existente'] > 0 || $duplicados['placa_existente'] > 0) {
                return false;
            }

            $stmt = $this->conn->prepare(
                "INSERT INTO clientes (nombre, documento, telefono)
                 VALUES (:nombre, :documento, :telefono)"
            );
            $stmt->execute([
                ':nombre' => $nombre,
                ':documento' => $documento,
                ':telefono' => $telefono ?: null
            ]);

            $clienteId = $this->conn->lastInsertId();
            $stmt = $this->conn->prepare(
                "INSERT INTO vehiculos (cliente_id, placa, marca, modelo, anio)
                 VALUES (:cliente_id, :placa, :marca, :modelo, :anio)"
            );
            return $stmt->execute([
                ':cliente_id' => $clienteId,
                ':placa' => $placa,
                ':marca' => $marca ?: null,
                ':modelo' => $modelo ?: null,
                ':anio' => $anio !== '' ? $anio : null
            ]);
        } catch (PDOException $exception) {
            return false;
        }
    }
}
?>