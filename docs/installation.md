# Instalação e Configuração do CoreCRM

Este guia detalha os passos necessários para instalar e configurar o CoreCRM em seu ambiente de desenvolvimento ou produção. Certifique-se de que seu sistema atende aos requisitos mínimos antes de prosseguir com a instalação.

## Requisitos do Sistema

Para executar o CoreCRM, você precisará dos seguintes componentes:

*   **PHP 8.x+**: O CoreCRM é desenvolvido em PHP e requer a versão 8.x ou superior para compatibilidade e desempenho.
*   **MySQL ou SQLite**: Um sistema de gerenciamento de banco de dados relacional. O CoreCRM suporta tanto MySQL quanto SQLite para armazenamento de dados.
*   **Servidor Web com Suporte a URL Rewriting**: Um servidor web como Apache ou Nginx é necessário, configurado para suportar reescrita de URL (mod_rewrite para Apache ou configurações equivalentes para Nginx). Isso é essencial para o roteamento dinâmico do CoreCRM.

## Passos de Instalação

Siga os passos abaixo para instalar o CoreCRM:

1.  **Clone o Repositório:**

    Abra seu terminal ou prompt de comando e clone o repositório do CoreCRM para o diretório desejado:

    ```bash
    git clone https://github.com/gdbarros94/CoreCRM.git
    cd CoreCRM
    ```

2.  **Instale as Dependências do Composer:**

    O CoreCRM utiliza o Composer para gerenciar suas dependências PHP. Certifique-se de ter o Composer instalado globalmente ou use o `composer.phar` que pode ser encontrado no repositório. Execute o seguinte comando no diretório raiz do projeto:

    ```bash
    composer install
    ```

    *Se você não tiver o Composer instalado globalmente, pode usar o `composer.phar` diretamente:* `php composer.phar install`

3.  **Configure o Banco de Dados:**

    O CoreCRM precisa de um banco de dados para armazenar seus dados. Você precisará configurar as credenciais de conexão no arquivo `config/database.config.php`. Este arquivo define o driver do banco de dados (MySQL ou SQLite), host, nome do banco de dados, usuário e senha.

    **Exemplo para MySQL (`config/database.config.php`):**

    ```php
    <?php
    return [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'corecrm_db',
        'username'  => 'root',
        'password'  => 'sua_senha',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ];
    ```

    **Exemplo para SQLite (`config/database.config.php`):**

    ```php
    <?php
    return [
        'driver'   => 'sqlite',
        'database' => 'database.sqlite', // Caminho para o arquivo SQLite
    ];
    ```

    *Crie o banco de dados (`corecrm_db` no exemplo MySQL) manualmente ou através de ferramentas como phpMyAdmin ou o cliente MySQL/SQLite.

4.  **Configure as Definições Globais do Sistema:**

    O arquivo `config/config.php` contém configurações globais para o CoreCRM, como modo de depuração, fuso horário e nome da aplicação. Edite este arquivo para ajustar as configurações conforme suas necessidades.

    **Exemplo (`config/config.php`):**

    ```php
    <?php
    return [
        'app_name' => 'Meu CoreCRM',
        'debug_mode' => true, // Defina como false em produção
        'timezone' => 'America/Sao_Paulo',
        // Outras configurações...
    ];
    ```

5.  **Acesse a Aplicação:**

    Após configurar o servidor web para apontar para o diretório raiz do CoreCRM (onde `index.php` está localizado), você pode acessar a aplicação através do seu navegador. Por exemplo, se você configurou um host virtual para `corecrm.local`, acesse `http://corecrm.local`.

    O `index.php` é o ponto de entrada principal que inicializa o sistema e o roteamento.

## Configuração do Servidor Web

### Apache

Para Apache, certifique-se de que o módulo `mod_rewrite` esteja habilitado e que seu arquivo `.htaccess` (já incluído no repositório) esteja sendo respeitado. Um exemplo de configuração de Virtual Host pode ser:

```apache
<VirtualHost *:80>
    ServerName corecrm.local
    DocumentRoot /caminho/para/seu/CoreCRM

    <Directory /caminho/para/seu/CoreCRM>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### Nginx

Para Nginx, você precisará de uma configuração similar para reescrever as URLs para `index.php`. Um exemplo de bloco `server` pode ser:

```nginx
server {
    listen 80;
    server_name corecrm.local;
    root /caminho/para/seu/CoreCRM;

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock; # Verifique a versão do PHP-FPM
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Após a instalação e configuração, seu CoreCRM estará pronto para uso e desenvolvimento.

