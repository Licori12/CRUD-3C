# Documentacao do App CRUD PHP + JavaScript + Axios + MySQL

Este projeto e um CRUD de **produtos**, dividido em duas partes dockerizadas:

- `crud-api`: API REST em PHP, Nginx, PHP-FPM e MySQL.
- `crud-frontend-axios`: frontend com Vite, Axios, HTML, CSS, Bootstrap e JavaScript modular.

O recurso principal e `Product`, com os campos:

- `name`: nome do produto.
- `price`: preco do produto.
- `stock`: quantidade em estoque.

## Tecnologias

- **Nginx**: servidor HTTP da API e do frontend em Docker.
- **PHP 8 FPM**: runtime que executa os arquivos PHP da API.
- **MySQL 8.4**: banco de dados real usado para persistir produtos.
- **PDO MySQL**: extensao PHP usada para conversar com o MySQL com prepared statements.
- **Docker + Docker Compose**: containers independentes para API, PHP, banco e frontend.
- **Vite**: servidor de desenvolvimento e build do frontend.
- **Axios**: camada HTTP do frontend.
- **HTML5 + CSS3**: estrutura e estilos.
- **Bootstrap 5 via CDN**: layout, cards, botoes, formulario e alertas.
- **ES Modules**: organizacao do JavaScript com `import` e `export`.

## Estrutura

```text
CRUD/
|-- crud-api/
|   |-- Dockerfile
|   |-- compose.yaml
|   |-- database/
|   |   `-- init.sql
|   |-- docker/
|   |   `-- nginx/
|   |       `-- default.conf
|   `-- src/
|       |-- config/
|       |   `-- config.php
|       |-- public/
|       |   `-- index.php
|       `-- src/
|           |-- api.php
|           |-- controllers.php
|           |-- services.php
|           |-- validation.php
|           |-- database.php
|           `-- data.php
|-- crud-frontend-axios/
|   |-- Dockerfile
|   |-- compose.yaml
|   |-- package.json
|   |-- package-lock.json
|   |-- vite.config.js
|   |-- dist/
|   `-- src/
|       |-- index.html
|       |-- app.js
|       |-- styles/
|       |   |-- reset.css
|       |   `-- style.css
|       `-- scripts/
|           |-- api/
|           |   |-- read.js
|           |   |-- create.js
|           |   |-- update.js
|           |   `-- delete.js
|           `-- dom/
|               |-- render.js
|               `-- form.js
`-- README.md
```

## Como Rodar

### API + MySQL

```powershell
cd C:\Users\guilh\Desktop\CRUD\crud-api
docker compose up --build
```

Servicos da API:

- Nginx: `http://localhost:8000`
- API: `http://localhost:8000/api/products`
- MySQL: `localhost:3306`

### Frontend com Docker

O container do frontend serve a pasta `dist/`, entao rode o build antes:

```powershell
cd C:\Users\guilh\Desktop\CRUD\crud-frontend-axios
npm install
npm run build
docker compose up --build
```

Frontend:

```text
http://localhost:8080
```

### Frontend em Desenvolvimento

```powershell
cd C:\Users\guilh\Desktop\CRUD\crud-frontend-axios
npm install
npm run dev
```

Vite:

```text
http://localhost:8080
```

## API REST

Base URL:

```text
http://localhost:8000/api/products
```

### GET `/api/products`

Lista produtos.

```json
{
  "products": [
    {
      "id": 1,
      "name": "Notebook",
      "price": 3499.9,
      "stock": 12
    }
  ]
}
```

### POST `/api/products`

Cria produto.

```json
{
  "name": "Teclado",
  "price": 149.9,
  "stock": 20
}
```

### PUT `/api/products?id=1`

Atualiza todos os campos do produto.

### PATCH `/api/products?id=1`

Atualiza somente os campos alterados.

### DELETE `/api/products?id=1`

Remove produto pelo ID.

## Banco de Dados

O banco fica em um container MySQL.

Credenciais usadas no `compose.yaml`:

```text
Host interno: db
Host externo: localhost
Porta: 3306
Database: crud_products
User: crud_user
Password: crud_pass
Root password: root
```

Tabela criada por `crud-api/database/init.sql`:

```sql
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  stock INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

O MySQL executa `init.sql` automaticamente na primeira criacao do volume `db_data`.

## O Que Cada Arquivo Faz

### Raiz do Projeto

#### `README.md`

Documenta estrutura, tecnologias, comandos, endpoints, banco e responsabilidades dos arquivos.

### Backend: `crud-api/`

#### `crud-api/Dockerfile`

Define a imagem PHP da API.

Ele usa `php:8.3-fpm` e instala:

```dockerfile
pdo_mysql
```

Por que existe:

- Prepara o PHP para executar a API.
- Permite conexao com MySQL via PDO.

#### `crud-api/compose.yaml`

Sobe tres servicos:

- `nginx`: recebe HTTP na porta `8000`.
- `php`: executa PHP-FPM e conecta no banco.
- `db`: MySQL 8.4.

Tambem cria o volume:

```text
db_data
```

Esse volume preserva os dados do MySQL mesmo se os containers forem recriados.

#### `crud-api/database/init.sql`

Script SQL inicial do banco.

Responsabilidades:

- Criar tabela `products`.
- Inserir produtos iniciais se ainda nao existirem.

Por que existe:

- Permite subir o banco ja pronto para uso.
- Evita criar tabela manualmente.

#### `crud-api/docker/nginx/default.conf`

Configura o Nginx da API.

Responsabilidades:

- Servir a pasta `src/public`.
- Usar `try_files` para mandar rotas como `/api/products` para `index.php`.
- Repassar arquivos `.php` para o PHP-FPM em `php:9000`.

Trecho principal:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

#### `crud-api/src/config/config.php`

Guarda configuracoes globais.

Principais itens:

- `$dbConfig`: host, nome, usuario e senha do MySQL.
- `$allowedOrigins`: origens liberadas pelo CORS.

Os dados do banco podem vir de variaveis de ambiente:

```text
DB_HOST
DB_NAME
DB_USER
DB_PASS
```

#### `crud-api/src/public/index.php`

E o ponto de entrada da API.

Responsabilidades:

- Carregar `config.php`.
- Carregar `database.php`.
- Criar conexao PDO com MySQL.
- Configurar CORS.
- Responder `OPTIONS`.
- Encaminhar `/api/products` para `api.php`.
- Retornar `404` para rotas inexistentes.

#### `crud-api/src/src/database.php`

Cria a conexao PDO.

Funcao:

- `getConnection(array $dbConfig): PDO`

Por que existe:

- Centraliza a criacao da conexao.
- Define modo de erro com `PDO::ERRMODE_EXCEPTION`.
- Define retorno padrao como array associativo.

#### `crud-api/src/src/api.php`

Faz dispatch pelo metodo HTTP.

Mapeamento:

- `GET` chama `handleGet`.
- `POST` chama `handlePost`.
- `PUT` chama `handlePut`.
- `PATCH` chama `handlePatch`.
- `DELETE` chama `handleDelete`.
- Outros metodos chamam `handleMethodNotAllowed`.

Agora ele passa `$pdo` para os controllers.

#### `crud-api/src/src/controllers.php`

Camada de entrada das operacoes HTTP.

Funcoes:

- `respond`: envia JSON com status HTTP.
- `handleGet`: responde listagem.
- `handlePost`: le JSON do corpo e cria produto.
- `handlePut`: le `id`, le JSON do corpo e atualiza produto inteiro.
- `handlePatch`: le `id`, le JSON do corpo e atualiza parte do produto.
- `handleDelete`: le `id` e remove produto.
- `handleMethodNotAllowed`: retorna `405`.

#### `crud-api/src/src/services.php`

Contem regras de negocio.

Funcoes:

- `getAllProducts`: carrega todos os produtos.
- `createProduct`: valida e cria produto.
- `editProduct`: valida e edita produto por `PUT` ou `PATCH`.
- `removeProduct`: remove produto pelo ID.

Essa camada nao escreve SQL. Ela chama `data.php`.

#### `crud-api/src/src/validation.php`

Valida dados recebidos.

Funcoes:

- `validateRequiredFields`: verifica campos obrigatorios.
- `validateProductFields`: valida `name`, `price` e `stock`.

Regras:

- `name` nao pode ser vazio.
- `name` deve ter no maximo 100 caracteres.
- `price` deve ser numerico e maior ou igual a 0.
- `stock` deve ser numerico e maior ou igual a 0.

#### `crud-api/src/src/data.php`

Camada de acesso ao banco.

Funcoes:

- `loadProducts`: executa `SELECT`.
- `findProductById`: busca por ID.
- `insertProduct`: executa `INSERT`.
- `updateProduct`: executa `UPDATE`.
- `deleteProduct`: executa `DELETE`.
- `normalizeProductRow`: converte tipos vindos do MySQL.

Por que existe:

- Mantem SQL separado das regras de negocio.
- Facilita trocar MySQL por outro banco no futuro.

### Frontend: `crud-frontend-axios/`

#### `crud-frontend-axios/package.json`

Declara scripts e dependencias do frontend.

Scripts:

- `npm run dev`: inicia Vite em modo desenvolvimento.
- `npm run build`: gera a pasta `dist`.
- `npm run preview`: serve o build localmente.

Dependencias:

- `axios`: cliente HTTP.
- `vite`: ferramenta de desenvolvimento e build.

#### `crud-frontend-axios/package-lock.json`

Trava as versoes exatas instaladas pelo npm.

#### `crud-frontend-axios/vite.config.js`

Configura o Vite.

- `root: 'src'`: fonte do frontend.
- `outDir: '../dist'`: build final.
- `port: 8080`: porta do servidor dev.

#### `crud-frontend-axios/Dockerfile`

Usa Nginx para servir o frontend compilado.

Ele copia:

```text
dist/ -> /usr/share/nginx/html/
```

#### `crud-frontend-axios/compose.yaml`

Sobe o frontend em Docker.

Mapeia:

```text
localhost:8080 -> container:80
```

#### `crud-frontend-axios/dist/`

Pasta gerada por `npm run build`.

Nao edite manualmente.

#### `crud-frontend-axios/src/index.html`

Pagina principal.

Contem:

- Header.
- Container `#products`.
- Formulario `#product-form`.
- Campos `name`, `price`, `stock`.
- Alerta `#form-error`.
- Botao `#cancel-edit`.
- Import do `app.js`.

#### `crud-frontend-axios/src/app.js`

Orquestra a aplicacao:

- Define `apiUrl`.
- Escuta cliques nos cards.
- Escuta submit do formulario.
- Chama funcoes de API.
- Chama funcoes de DOM.

#### `crud-frontend-axios/src/styles/reset.css`

Normaliza estilos padrao do navegador.

Faz coisas como:

- Remover margens padrao.
- Definir `box-sizing: border-box`.
- Fazer inputs e botoes herdarem fonte.
- Evitar midias estourando containers.

#### `crud-frontend-axios/src/styles/style.css`

Guarda estilos especificos do projeto.

Atualmente define altura minima da pagina e fundo claro.

### Frontend: Camada API

#### `src/scripts/api/read.js`

- `getProducts(apiUrl)`: faz `axios.get` e retorna `response.data.products`.

#### `src/scripts/api/create.js`

- `createProduct(apiUrl, product)`: faz `axios.post`, convertendo `price` e `stock` para numeros.

#### `src/scripts/api/update.js`

- `putProduct`: faz `PUT` com todos os campos.
- `patchProduct`: faz `PATCH` com campos parciais.
- `updateProduct`: decide entre `PUT`, `PATCH` ou nenhuma requisicao.

#### `src/scripts/api/delete.js`

- `deleteProduct(apiUrl, id)`: faz `axios.delete`.

### Frontend: Camada DOM

#### `src/scripts/dom/render.js`

- Busca produtos.
- Mantem cache local.
- Renderiza cards.
- Formata preco em BRL.

#### `src/scripts/dom/form.js`

- Controla modo de edicao.
- Guarda produto original.
- Preenche e limpa formulario.
- Mostra e esconde erros.

## Fluxo Atual

```text
Axios -> Nginx -> PHP-FPM -> PDO -> MySQL
```

Camadas PHP:

```text
controllers.php -> services.php -> data.php -> MySQL
```

## O Que Mudou na Migracao para MySQL

- `crud-api/compose.yaml` ganhou o servico `db` com MySQL 8.4.
- `crud-api/Dockerfile` passou a instalar `pdo_mysql`.
- `crud-api/database/init.sql` foi criado para criar e popular a tabela.
- `crud-api/src/config/config.php` passou a usar configuracoes de banco.
- `crud-api/src/src/database.php` foi criado para abrir conexao PDO.
- `crud-api/src/src/api.php` passou a usar `$pdo` em vez de `$dataFile`.
- `crud-api/src/src/controllers.php` agora recebe `PDO`.
- `crud-api/src/src/services.php` agora recebe `PDO`.
- `crud-api/src/src/data.php` deixou de ler JSON e passou a executar SQL.
- `crud-api/data/data.json` foi removido.
- A pasta `crud-api/data/` foi removida porque nao e mais usada.
- `crud-api/nginx.conf` foi organizado em `crud-api/docker/nginx/default.conf`.

## Comandos Uteis

Parar API e banco:

```powershell
cd C:\Users\guilh\Desktop\CRUD\crud-api
docker compose down
```

Parar e apagar volume do banco:

```powershell
docker compose down -v
```

Use `down -v` apenas quando quiser recriar o banco do zero e rodar `init.sql` novamente.

## Observacoes

- O frontend oficial e `crud-frontend-axios`.
- A antiga pasta `frontend` com Fetch foi removida.
- O antigo JSON foi substituido por MySQL.
- Se mudar a porta do frontend, adicione a origem em `crud-api/src/config/config.php`.
- `dist/` e gerado automaticamente; altere sempre os arquivos dentro de `src/`.
