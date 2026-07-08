<?php

require_once __DIR__ . '/services.php';

// Padroniza respostas HTTP em JSON para sucesso e erro.
function respond(array $result): void
{
    http_response_code($result['status']);

    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
    } else {
        echo json_encode($result['data']);
    }
}

// GET /api/products: lista todos os produtos.
function handleGet(PDO $pdo): void
{
    try {
        echo json_encode(getAllProducts($pdo));
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

// POST /api/products: le JSON do corpo e cria produto.
function handlePost(PDO $pdo): void
{
    try {
        // php://input contem o corpo bruto enviado pelo Axios.
        $input = json_decode(file_get_contents('php://input'), true);
        respond(createProduct($pdo, $input));
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

// PUT /api/products?id=1: substitui todos os campos do produto.
function handlePut(PDO $pdo): void
{
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        respond(editProduct($pdo, $id, $input));
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

// PATCH /api/products?id=1: atualiza somente os campos enviados.
function handlePatch(PDO $pdo): void
{
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        respond(editProduct($pdo, $id, $input, partial: true));
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

// DELETE /api/products?id=1: remove produto pelo ID.
function handleDelete(PDO $pdo): void
{
    try {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        respond(removeProduct($pdo, $id));
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

// Retorna 405 quando o metodo HTTP nao faz parte do contrato da API.
function handleMethodNotAllowed(): void
{
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
