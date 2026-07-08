<?php

// Verifica se todos os campos obrigatorios foram enviados.
function validateRequiredFields(array $input, array $fields): ?string
{
    $missing = [];

    foreach ($fields as $field) {
        if (!isset($input[$field])) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        return implode(', ', $missing) . ' are required';
    }

    return null;
}

// Valida regras de negocio dos campos do produto.
function validateProductFields(array $input): ?string
{
    // Nome e opcional no PATCH, mas se vier nao pode ser vazio.
    if (isset($input['name'])) {
        $name = trim($input['name']);

        if ($name === '') {
            return 'Name cannot be empty';
        }

        if (strlen($name) > 100) {
            return 'Name must be at most 100 characters';
        }
    }

    // Preco deve ser numerico e nao pode ser negativo.
    if (isset($input['price'])) {
        if (!is_numeric($input['price'])) {
            return 'Price must be a number';
        }

        if ((float) $input['price'] < 0) {
            return 'Price must be greater than or equal to 0';
        }
    }

    // Estoque deve ser numerico e nao pode ser negativo.
    if (isset($input['stock'])) {
        if (!is_numeric($input['stock'])) {
            return 'Stock must be a number';
        }

        if ((int) $input['stock'] < 0) {
            return 'Stock must be greater than or equal to 0';
        }
    }

    return null;
}
