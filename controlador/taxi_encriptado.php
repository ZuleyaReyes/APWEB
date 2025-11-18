<?php

error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cedula");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

require_once("../conexion/conexion.php");
require_once("../modelo/taxi.php");
require_once("../modelo/usuariosapi.php");


$cedula = null;

if (function_exists('getallheaders')) {
    $headers = getallheaders();
    if (isset($headers["cedula"])) {
        $cedula = $headers["cedula"];
    } elseif (isset($headers["Cedula"])) {
        $cedula = $headers["Cedula"];
    }
} else {
   
    if (isset($_SERVER["HTTP_CEDULA"])) {
        $cedula = $_SERVER["HTTP_CEDULA"];
    }
}

if (!$cedula) {
    http_response_code(401);
    echo json_encode(["error" => "Debe enviar el header 'cedula'"]);
    exit();
}



$usuariosApi = new UsuariosAPI();
$usuario = $usuariosApi->obtenerPorCedula($cedula);

if (!$usuario) {
    http_response_code(401);
    echo json_encode(["error" => "Cédula no encontrada en usuariosapi"]);
    exit();
}


if (!isset($usuario["Clave"])) {
    echo json_encode(["error" => "El registro de usuariosapi no tiene columna 'Clave'"]);
    exit();
}



$clave_texto = $usuario["Clave"];        
$clave_sha256 = hash("sha256", $clave_texto, true);


$clave_texto = $usuario["Clave"];   



function decryptAES_ECB($base64Data, $keyText) {
    $raw = base64_decode($base64Data, true);
    if ($raw === false) {
        return false;
    }

    $plain = openssl_decrypt(
        $raw,
        "aes-256-ecb",
        $keyText,          
        OPENSSL_RAW_DATA   
    );

    return $plain;
}

$bodyCifrado = file_get_contents("php://input");
$bodyCifrado = trim($bodyCifrado); 

$body = [];

if ($bodyCifrado !== "") {
    $jsonPlano = decryptAES_ECB($bodyCifrado, $clave_texto);

    if ($jsonPlano === false) {
        echo json_encode(["error" => "No se pudo descifrar el JSON (Base64/AES)"]);
        exit();
    }

    $body = json_decode($jsonPlano, true);

    if (!is_array($body)) {
        echo json_encode(["error" => "JSON descifrado inválido"]);
        exit();
    }
}


$taxi = new Taxi();
$op   = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {
    case "ObtenerTodas":
        echo json_encode($taxi->listar());
        break;

    case "ObtenerPorId":
        echo json_encode($taxi->obtener_por_id($body["Placa"] ?? ""));
        break;

    case "Insertar":
        echo json_encode($taxi->insertar($body));
        break;

    case "Actualizar":
        echo json_encode($taxi->actualizar($body));
        break;

    case "Eliminar":
        echo json_encode($taxi->eliminar($body["Placa"] ?? ""));
        break;

    default:
        echo json_encode(["error" => "Operación inválida"]);
        break;
}
?>