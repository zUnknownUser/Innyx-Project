# INNYX Challenge — Sistema de Gestão de Produtos

## Sobre o Projeto

Este projeto consiste em um sistema completo de gerenciamento de produtos, desenvolvido como parte do desafio técnico da INNYX.

A aplicação é composta por:

* Frontend: Vue.js 3 com TypeScript (Composition API)
* Backend: Laravel (PHP 8.3)
* Banco de dados: MySQL 8
* Ambiente: Docker

---

## Objetivo

Implementar um sistema CRUD de produtos com:

* Autenticação por token
* Relacionamento com categorias
* Upload de imagem
* Paginação
* Busca por nome e descrição
* Interface responsiva

---

## Arquitetura

A aplicação é composta por três serviços principais:

| Serviço  | Tecnologia | Porta |
| -------- | ---------- | ----- |
| Frontend | Vue 3      | 5173  |
| Backend  | Laravel    | 8000  |
| Database | MySQL 8    | 3306  |

---

## Uso do Docker

O projeto foi containerizado utilizando Docker, permitindo execução com um único comando.

### Serviços Docker

* frontend → aplicação Vue.js
* backend → API Laravel
* db → banco MySQL

---

## Como executar o projeto

### 1. Clonar o repositório

```bash
git clone <URL_DO_REPOSITORIO>
cd backend
```

---

### 2. Subir os containers

```bash
docker compose up -d --build
```

---

### 3. Configurar o backend

```bash
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate
```

---

### 4. Acessar a aplicação

Frontend: http://localhost:5173
Backend: http://localhost:8000

---

## Fluxo de execução

Ao rodar o comando:

```bash
docker compose up -d
```

O Docker executa automaticamente:

1. Inicializa o banco de dados (MySQL)
2. Builda o backend Laravel
3. Instala dependências PHP (Composer)
4. Inicia a API Laravel
5. Builda o frontend Vue
6. Instala dependências Node (npm)
7. Executa o frontend com Vite

---

## Observações importantes

### Comunicação entre containers

Dentro do Docker, os serviços se comunicam pelo nome:

```
DB_HOST=db
```

Não é utilizado localhost, pois cada container é isolado.

---

### Execução automatizada do frontend

O frontend não precisa de execução manual (npm run dev), pois isso já é feito automaticamente via Docker.

---

## Estrutura do projeto

```
backend/
  app/
  routes/
  database/
  Dockerfile
  docker-compose.yml

frontend/
  src/
  public/
  Dockerfile
```

---

## Autenticação

O sistema utiliza autenticação baseada em token (JWT ou Sanctum), garantindo segurança no acesso às rotas protegidas.

---

## Funcionalidades

* CRUD de produtos
* CRUD de categorias
* Paginação
* Busca por nome e descrição
* Upload de imagem
* Validações de dados
* Interface responsiva
* Feedback visual (loading e erros)
* Autenticação por token

---

## Possíveis melhorias

* Testes automatizados
* Pipeline CI/CD
* Cache com Redis
* Upload em storage externo

---

## Diferenciais implementados

* Ambiente completo com Docker
* Execução com um único comando
* Separação clara de responsabilidades
* Estrutura escalável

---

## Autor

Desenvolvido por Jorge Gregório

---

## Observações finais

O projeto foi desenvolvido com foco em organização, boas práticas e facilidade de execução.

Para rodar o projeto:

```bash
docker compose up -d
```
