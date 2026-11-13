<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

session_start();

if (!isset($_SESSION['platform_contents'])) {
    $_SESSION['platform_contents'] = [
        [
            "id" => 1,
            "author_id" => 1,
            "title" => "Curso Básico de API RESTful",
            "category" => "Tecnología",
            "url" => "https://plataforma.com/ver/1",
            "likes" => 12
        ]
    ];
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "count" => count($_SESSION['platform_contents']),
            "data" => $_SESSION['platform_contents']
        ]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        $authorId = isset($data['author_id']) ? filter_var($data['author_id'], FILTER_VALIDATE_INT) : false;
        $title = isset($data['title']) ? trim($data['title']) : '';
        $category = isset($data['category']) ? trim($data['category']) : 'General';
        $url = isset($data['url']) ? filter_var($data['url'], FILTER_VALIDATE_URL) : false;

        if ($authorId === false || empty($title) || $url === false) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Validación fallida: 'author_id' (entero), 'title' y 'url' (formato URL válido) son requeridos."
            ]);
            exit();
        }

        $newContent = [
            "id" => count($_SESSION['platform_contents']) + 1,
            "author_id" => $authorId,
            "title" => $title,
            "category" => $category,
            "url" => $url,
            "likes" => 0
        ];

        $_SESSION['platform_contents'][] = $newContent;

        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Contenido publicado exitosamente.",
            "data" => $newContent
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido."]);
        break;
}