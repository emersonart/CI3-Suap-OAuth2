# CI3 Cliente Suap OAuth2

## Sobre

O **CI Cliente Suap OAuth2** implementa a integração com o SUAP, tendo 2 principais funcionalidades:

- Logar com SUAP via OAuth2
- Consumir API (via OAuth2) obtendo recursos em nome do usuário

## Requisitos

- cURL;
- [CodeIgniter 3.x](https://github.com/bcit-ci/CodeIgniter);
- $config['base_url'] precisa estar setado em */application/config/config.php*

---

## Instalação

 Antes da instalação verifique se atende aos resquisitos.

> Mova os arquivos deste pacote seguindo a estrutura:

```shell
CI                          # → Root Directory
└── application/
    ├── config/
    │   └── suap_auth.php
    ├── controllers/
    │   └── Suap_auth.php
    └── libraries
        └── Suap_OAuth2.php
    
```

---

## Instruções

### Crie sua Aplicação no SUAP

Crie sua aplicação em https://suap.ifrn.edu.br/api/ com as seguintes informações:

- **Client Type:** Confidential
- **Authorization Grant Type:** authorization-code
- **Redicert URIs**: **SEU_HOST**/suap_auth/ (Alterar para o seu servidor)
- Configure o **Client_id** e **Client_secret** no arquivo */application/config/suap_auth.php*


