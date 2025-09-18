# API de E-commerce - Backend PHP

![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14-blue)
![Docker](https://img.shields.io/badge/Docker-Ready-blue)

API RESTful desenvolvida em PHP 8+ para um sistema de e-commerce fictício. O projeto é totalmente containerizado com Docker, garantindo um ambiente de desenvolvimento padronizado, rápido e consistente.

## Funcionalidades

- **API RESTful** para CRUD completo de Usuários e Pedidos.
- **Autenticação** via Token JWT.
- **Banco de Dados PostgreSQL** com schema gerenciado por migrations (Phinx).
- **Seeds** para popular o banco com dados iniciais (usuário admin).
- **Integração com API externa** para conversão de moedas (BRL/USD) em tempo real.
- **Ambiente de Desenvolvimento** completo com Docker (PHP-FPM, Nginx, PostgreSQL).
- **Boas Práticas**: Código-fonte seguindo PSR-12, senhas com hash seguro e timestamps automáticos (`created_at`/`updated_at`).

## Pré-requisitos

Para rodar este projeto, você precisará ter instalado na sua máquina:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)

---

## 🚀 Guia de Instalação e Execução

Siga os passos abaixo para ter a aplicação rodando localmente em poucos minutos.

### 1. Clonar o Repositório

### 2. Configurar Variáveis de Ambiente

Copie o arquivo de exemplo para criar sua configuração local.

```bash
cp .env.example .env
```

### 3. Construir e Iniciar os Containers

Este comando irá construir as imagens e iniciar todos os serviços em segundo plano.

```bash
docker-compose up -d --build
```

### 4. Instalar Dependências do PHP

Execute o Composer dentro do container para instalar as bibliotecas do projeto.

```bash
docker-compose exec php_app composer install
```

### 5. Rodar as Migrations

Crie a estrutura do banco de dados executando as migrations.

```bash
docker-compose exec php_app composer migrate
```

### Popular o Banco de Dados (Opcional)

Execute o seeder para criar um usuário administrador padrão:

- **E-mail:** `admin@ecommerce.com`
- **Senha:** `supersecret`

```bash
docker-compose exec php_app composer seed
```
