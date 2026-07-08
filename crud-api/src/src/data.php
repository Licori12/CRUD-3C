<?php

// Busca todos os produtos no MySQL, ordenados pelo ID.
function loadProducts(PDO $pdo): array
{
    $statement = $pdo->query(
        'SELECT id, name, CAST(price AS DECIMAL(10,2)) AS price, stock
         FROM products
         ORDER BY id'
    );

    return array_map('normalizeProductRow', $statement->fetchAll());
}

// Busca um produto especifico usando prepared statement para proteger o parametro.
function findProductById(PDO $pdo, int $id): ?array
{
    $statement = $pdo->prepare(
        'SELECT id, name, CAST(price AS DECIMAL(10,2)) AS price, stock
         FROM products
         WHERE id = :id'
    );

    $statement->execute(['id' => $id]);
    $product = $statement->fetch();

    return $product ? normalizeProductRow($product) : null;
}

// Insere um novo produto e retorna o registro completo criado.
function insertProduct(PDO $pdo, array $product): array
{
    $statement = $pdo->prepare(
        'INSERT INTO products (name, price, stock)
         VALUES (:name, :price, :stock)'
    );

    $statement->execute([
        'name' => $product['name'],
        'price' => $product['price'],
        'stock' => $product['stock'],
    ]);

    return findProductById($pdo, (int) $pdo->lastInsertId());
}

// Atualiza dinamicamente apenas os campos recebidos em PUT/PATCH.
function updateProduct(PDO $pdo, int $id, array $fields): ?array
{
    if (empty($fields)) {
        return findProductById($pdo, $id);
    }

    $assignments = [];
    $parameters = ['id' => $id];

    // Monta SET name = :name, price = :price, ... com campos ja filtrados.
    foreach ($fields as $field => $value) {
        $assignments[] = "$field = :$field";
        $parameters[$field] = $value;
    }

    $sql = sprintf(
        'UPDATE products SET %s WHERE id = :id',
        implode(', ', $assignments)
    );

    $statement = $pdo->prepare($sql);
    $statement->execute($parameters);

    // Se nada foi alterado e o produto nao existe, retorna null para virar 404.
    if ($statement->rowCount() === 0 && findProductById($pdo, $id) === null) {
        return null;
    }

    return findProductById($pdo, $id);
}

// Remove um produto, retornando seus dados antigos para confirmar a exclusao.
function deleteProduct(PDO $pdo, int $id): ?array
{
    $product = findProductById($pdo, $id);

    if ($product === null) {
        return null;
    }

    $statement = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);

    return $product;
}

// Converte tipos vindos do MySQL para JSON consistente no frontend.
function normalizeProductRow(array $product): array
{
    return [
        'id' => (int) $product['id'],
        'name' => $product['name'],
        'price' => (float) $product['price'],
        'stock' => (int) $product['stock'],
    ];
}
