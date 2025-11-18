<?php


require_once("../conexion/conexion.php");

class Cliente extends Conectar {

    private $tabla = "cliente";
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

    public function obtener_por_id($cedula) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "SELECT * FROM {$this->tabla} WHERE Cedula = :cedula";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":cedula", $cedula);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: ["mensaje" => "No se encontró el cliente"];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function insertar($data) {
        try {
            $conexion = parent::conectar_bd();
               
                $sql = "INSERT INTO {$this->tabla} (Cedula, Nombre, Apellidos, Telefono)
                    VALUES (:Cedula, :Nombre, :Apellidos, :Telefono)";
            $stmt = $conexion->prepare($sql);
          
            $ced = isset($data["Cedula"]) ? $data["Cedula"] : null;
            $nom = isset($data["Nombre"]) ? $data["Nombre"] : null;
            $ape = isset($data["Apellidos"]) ? $data["Apellidos"] : null;
            $tel = isset($data["Telefono"]) ? $data["Telefono"] : null;
            $ok = $stmt->execute([
                ":Cedula" => $ced,
                ":Nombre" => $nom,
                ":Apellidos" => $ape,
                ":Telefono" => $tel
            ]);

            if ($ok) {
                return ["filas_afectadas" => $stmt->rowCount()];
            } else {
                $err = $stmt->errorInfo();
                return ["error" => isset($err[2]) ? $err[2] : 'Error desconocido al ejecutar INSERT'];
            }
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function actualizar($data) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "UPDATE {$this->tabla} 
                    SET Nombre = :Nombre,
                        Apellidos = :Apellidos,
                        Telefono = :Telefono
                    WHERE Cedula = :Cedula";
            $stmt = $conexion->prepare($sql);
            $ced = isset($data["Cedula"]) ? $data["Cedula"] : null;
            $nom = isset($data["Nombre"]) ? $data["Nombre"] : null;
            $ape = isset($data["Apellidos"]) ? $data["Apellidos"] : null;
            $tel = isset($data["Telefono"]) ? $data["Telefono"] : null;
            $ok = $stmt->execute([
                ":Cedula" => $ced,
                ":Nombre" => $nom,
                ":Apellidos" => $ape,
                ":Telefono" => $tel
            ]);

            if ($ok) {
                return ["filas_afectadas" => $stmt->rowCount()];
            } else {
                $err = $stmt->errorInfo();
                return ["error" => isset($err[2]) ? $err[2] : 'Error desconocido al ejecutar UPDATE'];
            }
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    public function eliminar($cedula) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "DELETE FROM {$this->tabla} WHERE Cedula = :cedula";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":cedula", $cedula);
            $stmt->execute();
            return ["filas_afectadas" => $stmt->rowCount()];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
?>
