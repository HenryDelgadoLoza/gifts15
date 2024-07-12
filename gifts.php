<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

file_put_contents('debug.log', "Inicio de la ejecución\n", FILE_APPEND);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gift_registry";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    file_put_contents('debug.log', "Error de conexión: " . $conn->connect_error . "\n", FILE_APPEND);
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

file_put_contents('debug.log', "Conexión exitosa\n", FILE_APPEND);

$sql = "SELECT id, name, reserved FROM gifts";
$result = $conn->query($sql);

if ($result === FALSE) {
    file_put_contents('debug.log', "Error en la consulta: " . $conn->error . "\n", FILE_APPEND);
    die(json_encode(["success" => false, "message" => "Error en la consulta: " . $conn->error]));
}

$gifts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $encodedRow = array_map(function($item) {
            return mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }, $row);
        // Convierte 'reserved' a booleano
        $encodedRow['reserved'] = $encodedRow['reserved'] == '1' || $encodedRow['reserved'] == 'true' || $encodedRow['reserved'] === true;
        $gifts[] = $encodedRow;
    }
    file_put_contents('debug.log', "Se encontraron " . count($gifts) . " regalos\n", FILE_APPEND);
} else {
    file_put_contents('debug.log', "No se encontraron regalos\n", FILE_APPEND);
    echo json_encode(["success" => false, "message" => "No se encontraron regalos"]);
    exit;
}

$jsonGifts = json_encode($gifts, JSON_UNESCAPED_UNICODE);
if ($jsonGifts === false) {
    file_put_contents('debug.log', "Error al codificar JSON: " . json_last_error_msg() . "\n", FILE_APPEND);
    die(json_encode(["success" => false, "message" => "Error al codificar JSON: " . json_last_error_msg()]));
}

file_put_contents('debug.log', "JSON codificado correctamente\n", FILE_APPEND);

echo $jsonGifts;
file_put_contents('debug.log', "JSON enviado correctamente\n", FILE_APPEND);

$conn->close();