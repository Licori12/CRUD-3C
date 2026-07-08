-- Cria a tabela principal do CRUD de produtos.
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  stock INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insere um produto inicial apenas se ele ainda nao existir.
INSERT INTO products (name, price, stock)
SELECT 'Notebook', 3499.90, 12
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Notebook');

-- Segundo registro inicial para a lista nao comecar vazia.
INSERT INTO products (name, price, stock)
SELECT 'Mouse sem fio', 89.90, 35
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Mouse sem fio');
