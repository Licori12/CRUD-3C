<?php

require_once __DIR__ . '/controllers.php';

// Metodo HTTP usado para escolher a operacao do CRUD.
$method = $_SERVER['REQUEST_METHOD'];

// Encaminha a requisicao para o controller correto.
match ($method) {
    'GET' => handleGet($pdo),
    'POST' => handlePost($pdo),
    'PUT' => handlePut($pdo),
    'PATCH' => handlePatch($pdo),
    'DELETE' => handleDelete($pdo),
    default => handleMethodNotAllowed(),
};
