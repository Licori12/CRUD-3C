<?php

require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/data.php';

// Regra de listagem: retorna todos os produtos no contrato esperado pelo frontend.
function getAllProducts(PDO $pdo): array
{
    return ['products' => loadProducts($pdo)];
}

// Regra de criacao: valida entrada e delega o INSERT para data.php.
function createProduct(PDO $pdo, ?array $input): array
{
    if (!is_array($input)) {
        return ['error' => 'Invalid JSON body', 'status' => 400];
    }

    $error = validateRequiredFields($input, ['name', 'price', 'stock']);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    $error = validateProductFields($input);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    // Monta um produto limpo e tipado antes de persistir.
    $product = insertProduct($pdo, [
        'name' => trim($input['name']),
        'price' => (float) $input['price'],
        'stock' => (int) $input['stock'],
    ]);

    return ['data' => $product, 'status' => 201];
}

// Regra de edicao: suporta PUT completo e PATCH parcial.
function editProduct(PDO $pdo, ?int $id, ?array $input, bool $partial = false): array
{
    if ($id === null) {
        return ['error' => 'Product id is required', 'status' => 400];
    }

    if (!is_array($input)) {
        return ['error' => 'Invalid JSON body', 'status' => 400];
    }

    if (!$partial) {
        $error = validateRequiredFields($input, ['name', 'price', 'stock']);
        if ($error) {
            return ['error' => $error, 'status' => 400];
        }
    }

    $error = validateProductFields($input);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    // Garante que apenas campos permitidos sejam enviados ao banco.
    $allowed = ['name', 'price', 'stock'];
    $fields = array_intersect_key($input, array_flip($allowed));

    if (isset($fields['name'])) {
        $fields['name'] = trim($fields['name']);
    }

    if (isset($fields['price'])) {
        $fields['price'] = (float) $fields['price'];
    }

    if (isset($fields['stock'])) {
        $fields['stock'] = (int) $fields['stock'];
    }

    $product = updateProduct($pdo, $id, $fields);

    if ($product === null) {
        return ['error' => 'Product not found', 'status' => 404];
    }

    return ['data' => $product, 'status' => 200];
}

// Regra de remocao: valida ID e delega o DELETE para data.php.
function removeProduct(PDO $pdo, ?int $id): array
{
    if ($id === null) {
        return ['error' => 'Product id is required', 'status' => 400];
    }

    $product = deleteProduct($pdo, $id);

    if ($product === null) {
        return ['error' => 'Product not found', 'status' => 404];
    }

    return ['data' => ['deleted' => $product], 'status' => 200];
}
