<?php


require_once("../conexion/conexion.php");

class Factura extends Conectar {

    private $tablaEncabezado = "facturaencabezado";
    private $tablaDetalle = "facturadetalle";
    public function listar() {
        try {
            $conexion = parent::conectar_bd();
            $sql = "SELECT e.Id, e.Fecha, e.PlacaTaxi, e.CedulaCliente, e.Total,
                           d.Monto, d.Descripcion
                    FROM {$this->tablaEncabezado} e
                    LEFT JOIN {$this->tablaDetalle} d ON e.Id = d.IdEncabezado";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $facturas;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function obtener_por_id($id) {
        try {
            $conexion = parent::conectar_bd();
            $sql = "SELECT e.Id, e.Fecha, e.PlacaTaxi, e.CedulaCliente, e.Total,
                           d.Monto, d.Descripcion
                    FROM {$this->tablaEncabezado} e
                    LEFT JOIN {$this->tablaDetalle} d ON e.Id = d.IdEncabezado
                    WHERE e.Id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $factura = $stmt->fetch(PDO::FETCH_ASSOC);
            return $factura ?: ["mensaje" => "No se encontró la factura con ese ID"];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function insertar($data) {
        try {
            $conexion = parent::conectar_bd();
            $conexion->beginTransaction();

           
            $sqlEncabezado = "INSERT INTO {$this->tablaEncabezado}
                              (Fecha, PlacaTaxi, CedulaCliente, Total)
                              VALUES (:Fecha, :PlacaTaxi, :CedulaCliente, :Total)";
            $stmtEncabezado = $conexion->prepare($sqlEncabezado);
            $stmtEncabezado->execute([
                ":Fecha" => $data["Fecha"],
                ":PlacaTaxi" => $data["PlacaTaxi"],
                ":CedulaCliente" => $data["CedulaCliente"],
                ":Total" => $data["Total"]
            ]);
            $idEncabezado = $conexion->lastInsertId();

           
            $sqlDetalle = "INSERT INTO {$this->tablaDetalle}
                           (IdEncabezado, Monto, Descripcion)
                           VALUES (:IdEncabezado, :Monto, :Descripcion)";
            $stmtDetalle = $conexion->prepare($sqlDetalle);
            $stmtDetalle->execute([
                ":IdEncabezado" => $idEncabezado,
                ":Monto" => $data["Monto"],
                ":Descripcion" => $data["Descripcion"]
            ]);

            $conexion->commit();
            return ["mensaje" => "Factura insertada correctamente", "id_insertado" => $idEncabezado];
        } catch (PDOException $e) {
            $conexion->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
    public function actualizar($data) {
        try {
            $conexion = parent::conectar_bd();
            $conexion->beginTransaction();

           
            $sqlEncabezado = "UPDATE {$this->tablaEncabezado}
                              SET Fecha = :Fecha,
                                  PlacaTaxi = :PlacaTaxi,
                                  CedulaCliente = :CedulaCliente,
                                  Total = :Total
                              WHERE Id = :Id";
            $stmtEncabezado = $conexion->prepare($sqlEncabezado);
            $stmtEncabezado->execute([
                ":Id" => $data["Id"],
                ":Fecha" => $data["Fecha"],
                ":PlacaTaxi" => $data["PlacaTaxi"],
                ":CedulaCliente" => $data["CedulaCliente"],
                ":Total" => $data["Total"]
            ]);

            
            $sqlDetalle = "UPDATE {$this->tablaDetalle}
                           SET Monto = :Monto, Descripcion = :Descripcion
                           WHERE IdEncabezado = :IdEncabezado";
            $stmtDetalle = $conexion->prepare($sqlDetalle);
            $stmtDetalle->execute([
                ":IdEncabezado" => $data["Id"],
                ":Monto" => $data["Monto"],
                ":Descripcion" => $data["Descripcion"]
            ]);

            $conexion->commit();
            return ["mensaje" => "Factura actualizada correctamente"];
        } catch (PDOException $e) {
            $conexion->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
    public function eliminar($id) {
        try {
            $conexion = parent::conectar_bd();
            $conexion->beginTransaction();

            
            $sqlDetalle = "DELETE FROM {$this->tablaDetalle} WHERE IdEncabezado = :id";
            $stmtDetalle = $conexion->prepare($sqlDetalle);
            $stmtDetalle->bindParam(":id", $id);
            $stmtDetalle->execute();

            $sqlEncabezado = "DELETE FROM {$this->tablaEncabezado} WHERE Id = :id";
            $stmtEncabezado = $conexion->prepare($sqlEncabezado);
            $stmtEncabezado->bindParam(":id", $id);
            $stmtEncabezado->execute();

            $conexion->commit();
            return ["mensaje" => "Factura eliminada correctamente"];
        } catch (PDOException $e) {
            $conexion->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
}
?>
