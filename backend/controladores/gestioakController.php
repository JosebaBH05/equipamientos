<?php
declare(strict_types=1);
header("Content-Type: application/json");

// Asegúrate de que esta ruta sea correcta y la clase Gestioa incluya el método insert
require_once "../klaseak/gestioak.php"; 

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? []; // Obtener el body JSON

    if ($method === 'GET') {
        // =================================================
        // LISTAR (GET)
        // =================================================
        $gestioak = Gestioa::getAll();
        echo json_encode([
            "success" => true,
            "data" => array_map(fn($g) => $g->toArray(), $gestioak)
        ]);
        exit;
    }

    if ($method === 'POST') {
        // =================================================
        // INSERTAR (POST)
        // =================================================
        $action = $input['action'] ?? null;

        if (strtoupper($action) === 'INSERT') {
            $ekipamendu_id = $input['ekipamendu_id'] ?? null;
            $gela_id = $input['gela_id'] ?? null;

            if (!$ekipamendu_id || !$gela_id) {
                http_response_code(400); // Bad Request
                throw new Exception("Faltan los IDs del equipamiento o de la gela.");
            }
            
            // Llamar a la función de inserción (asumiendo que Gestioa::insert existe y es correcto)
            $ok = Gestioa::insert((int)$ekipamendu_id, (int)$gela_id);

            echo json_encode([
                'success' => $ok,
                'message' => $ok ? 'Kokapena gordeta!' : 'Errorea kokalekua gordetzean (DB error).'
            ]);
            exit;
        }
        
        // Si es POST pero no es INSERT, podría ser otra acción, pero por ahora...
        http_response_code(400); // Bad Request
        throw new Exception("Acción '{$action}' no reconocida para el método POST.");
    }

    // Si el método no es GET ni POST
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Metodo ez onartua"
    ]);
    
} catch (Exception $e) {
    // Si la respuesta HTTP no se ha enviado (es 200), cambiarla a 500
    if (http_response_code() === 200) {
        http_response_code(500);
    }
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>