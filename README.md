# CI3 Cliente Suap OAuth2

## Sobre

O **CI Cliente Suap OAuth2** implementa a integração com o SUAP, tendo 2 principais funcionalidades:

- Logar com SUAP via OAuth2
- Consumir API (via OAuth2) obtendo recursos em nome do usuário

## Requisitos

- cURL
- [CodeIgniter 3.x](https://github.com/bcit-ci/CodeIgniter);

## Instalação

 - Baixe ou Clone este repositório
 - Copie a pasta 


## Instruções

### Crie sua Aplicação no SUAP

Crie sua aplicação em https://suap.ifrn.edu.br/api/ com as seguintes informações:

- **Client Type:** Confidential
- **Authorization Grant Type:** authorization-code
- **Redicert URIs**: http://localhost/ci3-suap-oauth2/ (Alterar para o seu servidor)

### Instalando, Configurando e Rodando o Cliente SUAP Javascript

Considerando que você já tenha clonado o repositório **cliente_suap_javascript**. abra o terminal:

	cd cliente_suap_javascript
	cp settings.sample.js settings.js

Faça os ajustes necessários, definindo a variável **CLIENT_ID**.

É necessário rodar a aplicação cliente num servidor local (usamos o Python SimpleHTTPServer):

	python -m SimpleHTTPServer 8888

Abra seu browser em http://localhost:8888/
