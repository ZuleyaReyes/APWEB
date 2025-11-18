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

  
    // En /modelo/chofer.php

public function eliminar($id) {
    $conexion = null;
    try {
        $conexion = parent::conectar_bd();
        
        // Iniciar transacción (opcional pero buena práctica)
        $conexion->beginTransaction();

        // 1. Desactivar temporalmente las restricciones de clave foránea (CRÍTICO)
        $conexion->exec("SET FOREIGN_KEY_CHECKS = 0;");
        
        // 2. Ejecutar la eliminación del chofer
        $sql = "DELETE FROM {$this->tabla} WHERE Cedula = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        $filas_afectadas = $stmt->rowCount();

        // 3. Volver a activar las restricciones
        $conexion->exec("SET FOREIGN_KEY_CHECKS = 1;");
        
        $conexion->commit(); // Confirmar la transacción
        
        return ["filas_afectadas" => $filas_afectadas];

    } catch (PDOException $e) {
        if ($conexion && $conexion->inTransaction()) {
             $conexion->rollBack(); // Revertir si hay error
        }
        // Asegurarse de reactivar las claves foráneas en caso de error
        if ($conexion) {
            $conexion->exec("SET FOREIGN_KEY_CHECKS = 1;");
        }
        // Devolver el error real de MySQL (ej: 'Cannot delete or update...')
        return ["error" => $e->getMessage()];
    }
}
}
?>

