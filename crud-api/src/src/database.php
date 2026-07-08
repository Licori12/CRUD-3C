<?php

// Cria a conexao PDO com MySQL usando as configuracoes da aplicacao.
function getConnection(array $dbConfig): PDO
{
    // DSN informa driver, host, banco e charset da conexao.
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $dbConfig['host'],
        $dbConfig['name']
    );

    // ERRMODE_EXCEPTION faz erros SQL virarem excecoes trataveis.
    return new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
