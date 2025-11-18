<?php


header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


require_once("../conexion/conexion.php");


if (!isset($_GET["entidad"])) {
    echo json_encode(["error" => "Debe especificar la entidad (chofer, taxi, cliente, factura)"]);
    exit;
}

$entidad = strtolower($_GET["entidad"]); 
$modelo_path = "../modelo/" . $entidad . ".php";

if (!file_exists($modelo_path)) {
    echo json_encode(["error" => "No existe el modelo solicitado: " . $entidad]);
    exit;
}

require_once($modelo_path);


$clase = ucfirst($entidad); 
$modelo = new $clase();


$body = json_decode(file_get_contents("php://input"), true);

$metodo = $_SERVER['REQUEST_METHOD'];

try {
    switch ($metodo) {
        case 'GET':
            
            if (isset($_GET['id'])) {
                $resultado = $modelo->obtener_por_id($_GET['id']);
            } else {
                $resultado = $modelo->listar();
            }
            echo json_encode($resultado);
            break;

        case 'POST':
            
            $resultado = $modelo->insertar($body);
            if (is_array($resultado) && isset($resultado['error'])) {
                http_response_code(500);
                echo json_encode(["error" => "Error al insertar", "detalle" => $resultado['error']]);
            } else {
                echo json_encode(["mensaje" => "Registro insertado correctamente", "data" => $resultado]);
            }
            break;

        case 'PUT':
            
            $resultado = $modelo->actualizar($body);
            if (is_array($resultado) && isset($resultado['error'])) {
                http_response_code(500);
                echo json_encode(["error" => "Error al actualizar", "detalle" => $resultado['error']]);
            } else {
                echo json_encode(["mensaje" => "Registro actualizado correctamente", "data" => $resultado]);
            }
            break;

        case 'DELETE':
            
            if (!isset($body['id'])) {
                echo json_encode(["error" => "Debe enviar un ID para eliminar"]);
                exit;
            }
            $resultado = $modelo->eliminar($body['id']);
            if (is_array($resultado) && isset($resultado['error'])) {
                http_response_code(500);
                echo json_encode(["error" => "Error al eliminar", "detalle" => $resultado['error']]);
            } else {
                echo json_encode(["mensaje" => "Registro eliminado correctamente", "data" => $resultado]);
            }
            break;

        default:
            echo json_encode(["error" => "Método HTTP no permitido"]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        "error" => "Ocurrió un error en el servidor",
        "detalle" => $e->getMessage()
    ]);
}
?>