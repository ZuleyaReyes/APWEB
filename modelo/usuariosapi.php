<?php
require_once("../conexion/conexion.php");

class UsuariosAPI extends Conectar {

    public function obtenerPorCedula($cedula) {
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();

        $sql = "SELECT * FROM usuariosapi WHERE cedula = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $cedula);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado === false) {
            return false;
        }
        return $resultado;
    }
}
?>
