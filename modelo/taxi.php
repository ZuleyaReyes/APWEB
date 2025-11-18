<?php

require_once("../conexion/conexion.php");

class Taxi extends Conectar {

    private $tabla = "taxi";
    public function listar() {
        try {
            $conexion = parent::conectar_bd();
            $sql = "SELECT * FROM {$this->tabla}";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultados;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function obtener_por_id($placa) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "SELECT * FROM {$this->tabla} WHERE Placa = :placa";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":placa", $placa);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: ["mensaje" => "No se encontró el taxi con esa placa"];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function insertar($data) {
        try {
            $conexion = parent::conectar_bd();
            
            if (!isset($data["CedulaChofer"]) || empty($data["CedulaChofer"])) {
                return ["error" => "Debe especificar la cédula del chofer"];
            }
            $sqlCheck = "SELECT 1 FROM chofer WHERE Cedula = :cedula LIMIT 1";
            $stmtCheck = $conexion->prepare($sqlCheck);
            $stmtCheck->execute([':cedula' => $data["CedulaChofer"]]);
            $exists = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if (!$exists) {
                return ["error" => "No existe un chofer con la cédula indicada: " . $data["CedulaChofer"]];
            }

            $sql = "INSERT INTO {$this->tabla} (Placa, CedulaChofer, Capacidad, Estado)
                    VALUES (:Placa, :CedulaChofer, :Capacidad, :Estado)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ":Placa" => $data["Placa"],
                ":CedulaChofer" => $data["CedulaChofer"],
                ":Capacidad" => $data["Capacidad"],
                ":Estado" => $data["Estado"]
            ]);
            return ["mensaje" => "Taxi insertado correctamente"];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function actualizar($data) {
        try {
            $conexion = parent::conectar_bd();
           
            if (isset($data["CedulaChofer"]) && !empty($data["CedulaChofer"])) {
                $sqlCheck = "SELECT 1 FROM chofer WHERE Cedula = :cedula LIMIT 1";
                $stmtCheck = $conexion->prepare($sqlCheck);
                $stmtCheck->execute([':cedula' => $data["CedulaChofer"]]);
                $exists = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                if (!$exists) {
                    return ["error" => "No existe un chofer con la cédula indicada: " . $data["CedulaChofer"]];
                }
            }

            $sql = "UPDATE {$this->tabla} 
                    SET CedulaChofer = :CedulaChofer,
                        Capacidad = :Capacidad,
                        Estado = :Estado
                    WHERE Placa = :Placa";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ":Placa" => $data["Placa"],
                ":CedulaChofer" => $data["CedulaChofer"],
                ":Capacidad" => $data["Capacidad"],
                ":Estado" => $data["Estado"]
            ]);
            return ["filas_afectadas" => $stmt->rowCount()];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    public function eliminar($placa) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "DELETE FROM {$this->tabla} WHERE Placa = :placa";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":placa", $placa);
            $stmt->execute();
            return ["filas_afectadas" => $stmt->rowCount()];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
?>
