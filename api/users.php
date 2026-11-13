<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

session_start();

if (!isset($_SESSION['platform_users'])) {
    $_SESSION['platform_users'] = [
        ["id" => 1, "username" => "admin_platform", "email" => "admin@plataforma.com", "role" => "creador"],
        ["id" => 2, "username" => "user_viewer", "email" => "viewer@plataforma.com", "role" => "espectador"]
    ];
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "count" => count($_SESSION['platform_users']),
            "data" => $_SESSION['platform_users']
        ]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        $username = isset($data['username']) ? trim($data['username']) : '';
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) : false;
        $role = isset($data['role']) && in_array($data['role'], ['creador', 'espectador']) ? $data['role'] : 'espectador';

        if (empty($username) || $email === false) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Se requiere un 'username' válido y un correo electrónico ('email') con formato correcto."
            ]);
            exit();
        }

        $newUser = [
            "id" => count($_SESSION['platform_users']) + 1,
            "username" => $username,
            "email" => $email,
            "role" => $role
        ];

        $_SESSION['platform_users'][] = $newUser;

        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Usuario de plataforma creado con éxito.",
            "data" => $newUser
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido."]);
        break;
}