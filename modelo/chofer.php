<?php

require_once("../conexion/conexion.php");

class Chofer extends Conectar {

    private $tabla = "chofer";

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

 
    public function obtener_por_id($id) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "SELECT * FROM {$this->tabla} WHERE Cedula = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $resultados; 
            
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }


    public function insertar($data) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "INSERT INTO {$this->tabla} (Cedula, Licencia, Nombre, Apellidos, Estado)
                    VALUES (:Cedula, :Licencia, :Nombre, :Apellidos, :Estado)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ":Cedula" => $data["Cedula"],
                ":Licencia" => $data["Licencia"],
                ":Nombre" => $data["Nombre"],
                ":Apellidos" => $data["Apellidos"],
                ":Estado" => $data["Estado"]
            ]);
            return ["id_insertado" => $conexion->lastInsertId()];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }


    public function actualizar($data) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "UPDATE {$this->tabla} 
                    SET Licencia = :Licencia,
                        Nombre = :Nombre,
                        Apellidos = :Apellidos,
                        Estado = :Estado
                    WHERE Cedula = :Cedula";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ":Cedula" => $data["Cedula"],
                ":Licencia" => $data["Licencia"],
                ":Nombre" => $data["Nombre"],
                ":Apellidos" => $data["Apellidos"],
                ":Estado" => $data["Estado"]
            ]);
            return ["filas_afectadas" => $stmt->rowCount()];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

  
    public function eliminar($id) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "DELETE FROM {$this->tabla} WHERE Cedula = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return ["filas_afectadas" => $stmt->rowCount()];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
?>
