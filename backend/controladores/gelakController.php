<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once '../controladores/conexion.php';
require_once '../klaseak/gela.php';

try {
// Detectar acción correctamente (tanto GET como JSON)
$action = $_GET['action'] ?? ($_POST['action'] ?? 'GET');
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch (strtoupper($action)) {

    // =================================================
    // LISTAR GELAS
    // =================================================
    case 'GET':
        $gelak = Gela::getAll();
        $data = array_map(fn($g) => $g->toArray(), $gelak);
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
        break;

    // =================================================
    // CREAR GELA
    // =================================================
    case 'POST':
        $izena = $input['izena'] ?? null;
        $taldea = $input['taldea'] ?? null;
        if (!$izena) throw new Exception("Falta el nombre de la gela");

        $nueva = Gela::create($izena, $taldea);
        echo json_encode([
            'success' => true,
            'data' => $nueva ? $nueva->toArray() : null,
            'message' => 'Gela creada correctamente'
        ]);
        break;

    // =================================================
    // ELIMINAR GELA
    // =================================================
    case 'DELETE':
        $id = $input['id'] ?? ($_GET['id'] ?? null);
        if (!$id) throw new Exception("Falta el ID de la gela");
        $gela = Gela::getById((int)$id);
        if (!$gela) throw new Exception("Gela no encontrada");
        $ok = $gela->delete();
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Gela eliminada' : 'Error al eliminar'
        ]);
        break;

    case 'INSERT': // <-- Nuevo caso para la inserción
        // Obtener los IDs enviados por el frontend
        $ekipamendu_id = $input['ekipamendu_id'] ?? null;
        $gela_id = $input['gela_id'] ?? null;

        if (!$ekipamendu_id || !$gela_id) {
            throw new Exception("Faltan IDs para la ubicación.");
        }
        
        // Llamar al método estático insert
        $ok = Gestioa::insert((int)$ekipamendu_id, (int)$gela_id);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Ubicación insertada correctamente' : 'Error al insertar ubicación'
        ]);
        break;

    default:
        throw new Exception("Acción no reconocida: $action");
}

} catch (Throwable $e) {
echo json_encode([
    'success' => false,
    'message' => 'Errorea: ' . $e->getMessage()
]);
}
