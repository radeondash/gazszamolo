<?php
// gaz_api.php
header('Content-Type: application/json; charset=utf-8');

// Egyszerű "adatbázis" – egy JSON fájl a szerveren
$file = __DIR__ . '/gaz_data.json';

$action = $_GET['action'] ?? '';

if ($action === 'load') {
    if (!file_exists($file)) {
        echo json_encode(['cfg' => [], 'history' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $raw = file_get_contents($file);
    if ($raw === false || $raw === '') {
        echo json_encode(['cfg' => [], 'history' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        echo json_encode(['cfg' => [], 'history' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }
    // Biztosítsuk a mezőket
    if (!isset($data['cfg']) || !is_array($data['cfg'])) $data['cfg'] = [];
    if (!isset($data['history']) || !is_array($data['history'])) $data['history'] = [];

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'save') {
    $raw = file_get_contents('php://input');
    if ($raw === false) {
        http_response_code(400);
        echo json_encode(['error' => 'No input'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Minimális formaellenőrzés
    if (!isset($data['cfg']) || !is_array($data['cfg'])) $data['cfg'] = [];
    if (!isset($data['history']) || !is_array($data['history'])) $data['history'] = [];

    // Fájlba írás
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

// Ismeretlen akció
http_response_code(400);
echo json_encode(['error' => 'Unknown action'], JSON_UNESCAPED_UNICODE);
