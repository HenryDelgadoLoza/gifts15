<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    die(json_encode(["success" => false, "message" => "Acceso no autorizado"]));
}

// ... (resto del código de add_gift.php)

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gift_registry";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Conexión fallida: " . $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    
    $sql = "INSERT INTO gifts (name, reserved) VALUES ('$name', 0)";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Regalo agregado exitosamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al agregar el regalo: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
}

$conn->close();