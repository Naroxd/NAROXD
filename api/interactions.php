<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

session_start();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $contentId = isset($data['content_id']) ? filter_var($data['content_id'], FILTER_VALIDATE_INT) : false;
    $action = isset($data['action']) ? trim($data['action']) : ''; // 'like'

    if ($contentId === false || $action !== 'like') {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Acción de interacción no válida. Se requiere 'content_id' y action = 'like'."
        ]);
        exit();
    }

    $found = false;
    if (isset($_SESSION['platform_contents'])) {
        foreach ($_SESSION['platform_contents'] as &$content) {
            if ($content['id'] === $contentId) {
                $content['likes']++;
                $found = true;
                break;
            }
        }
    }

    if ($found) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Interacción registrada (Like agregado)."
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "El contenido especificado no existe."
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}