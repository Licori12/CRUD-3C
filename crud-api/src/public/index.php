<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/database.php';

// Origem da requisicao usada para decidir se o CORS sera liberado.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
}

header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Preflight do navegador: responde sem abrir conexao com banco.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Remove query string para comparar apenas o caminho da rota.
$uri = strtok($_SERVER['REQUEST_URI'], '?');

if ($uri === '/api/products') {
    // Abre a conexao apenas quando a rota da API realmente sera executada.
    $pdo = getConnection($dbConfig);
    require __DIR__ . '/../src/api.php';
    exit;
}

notFound();

// Resposta padrao para rotas nao cadastradas.
function notFound(): void
{
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
