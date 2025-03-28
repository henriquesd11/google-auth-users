# Google Auth Users - Backend

## 📌 Sobre o Projeto
Este é o backend de uma aplicação full stack desenvolvida com Laravel para gerenciar autenticação via Google OAuth e cadastro de usuários.

## 🚀 Tecnologias Utilizadas
- PHP (Laravel 12.x)
- MySQL
- Docker
- Laravel Socialite (para autenticação com Google OAuth)
- Filas do Laravel (envio de e-mail assíncrono)

## 🔧 Configuração do Ambiente

### 📌 Pré-requisitos
- Docker e Docker Compose instalados
- Git instalado

### 📥 Clonando o Repositório
```sh
# Clone o repositório
$ git clone https://github.com/seuusuario/google-auth-users.git
$ cd google-auth-users
```

### ⚙️ Configuração do .env
1. Copie o arquivo `.env.example` para `.env`
2. Configure as variáveis do Google OAuth:
   ```env
   GOOGLE_CLIENT_ID=seu_client_id
   GOOGLE_CLIENT_SECRET=seu_client_secret
   GOOGLE_REDIRECT=http://localhost:8000/api/google/callback
   ```
2. Configure as variáveis MAIL:
   ```env
    MAIL_MAILER=smtp
    MAIL_HOST=seu_host
    MAIL_PORT=port
    MAIL_USERNAME=seu_usuario
    MAIL_PASSWORD=sua_senha
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS="seu_email"
    MAIL_FROM_NAME="${APP_NAME}"
   ```
3. As configurações do banco já estão prontas no `.env.example`
4. Configure as credenciais do serviço de e-mail, se necessário

### 🐳 Rodando com Docker
```sh
# Construir e subir os containers
$ docker-compose up --build

# Instalar dependências
$ docker exec laravel_app composer install

# Gerar chave da aplicação
$ docker exec laravel_app php artisan key:generate

# Rodar migrações e seeds
$ docker exec laravel_app php artisan migrate --seed
```

### ✅ Rodando Testes
```sh
$ docker exec laravel_app php artisan test
```

## 🏗 Estrutura e Funcionalidades
- **Autenticação com Google OAuth** usando Laravel Socialite
- **Cadastro de usuários** com nome, CPF e data de nascimento
- **Envio de e-mail assíncrono** na conclusão do cadastro
- **Filtros otimizados** por nome e CPF com índices no banco
- **Estrutura em camadas** Service/Repository
- **Suporte a filas** para processos assíncronos

## 🔗 Endpoints Principais
- `POST /api/google/login` → Retorna a URL de autenticação do Google
- `GET /api/google/callback` → Processa a autenticação e armazena o token
- `POST /api/users` → Cadastra novos usuários
- `GET /api/users` → Lista usuários com filtros de nome e CPF

## 🖥 URLs Padrão
- Backend: [http://localhost:8000](http://localhost:8000)

---
### 📌 Observação
O `supervisord.conf` já está configurado para rodar o `queue:work` automaticamente no container.

---

Feito por Luiz Henrique 🚀
