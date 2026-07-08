<?php

// Configuracoes do banco vindas do Docker Compose, com fallback para desenvolvimento.
$dbConfig = [
    'host' => getenv('DB_HOST') ?: 'db',
    'name' => getenv('DB_NAME') ?: 'crud_products',
    'user' => getenv('DB_USER') ?: 'crud_user',
    'pass' => getenv('DB_PASS') ?: 'crud_pass',
];

// Origens permitidas para o frontend acessar a API via CORS.
$allowedOrigins = [
    'http://0.0.0.0:8000', 'http://localhost:8000', 'http://127.0.0.1:8000',
    'http://0.0.0.0:8080', 'http://localhost:8080', 'http://127.0.0.1:8080',
    'http://0.0.0.0:5173', 'http://localhost:5173', 'http://127.0.0.1:5173',
    'http://0.0.0.0:5500', 'http://localhost:5500', 'http://127.0.0.1:5500',
];
