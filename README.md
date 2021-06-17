# Mahabharata Retail Server

This is restful-API of Mahabharata Retail
This project using Lumen Framework (PHP), go there for further information [here](https://lumen.laravel.com/docs).

Full project documentation [here](https://www.notion.so/Final-Project-Mahabharata-Retail-dcb555d645804a17ac5bd98c5129f847)

## Requirement

### Server

-   PHP >= 7.3
-   OpenSSL PHP Extension
-   PDO PHP Extension
-   Mbstring PHP Extension
-   MYSQL Database
-   For more information [link](https://lumen.laravel.com/docs/8.x/installation#server-requirements)

### Package Manager

-   [Composer](https://getcomposer.org/)

## Installation

1. create your MYSQL database table
2. rename env.example to .env
3. update database settings in .env based on your database server configuration. For more information [here](https://lumen.laravel.com/docs/8.x/database)
4. config **UPLOAD_IMAGE_TOKEN** using **imgbb**.

    For more information [here](https://api.imgbb.com/)

5. Install required package
    ```bash
    $ composer install
    ```
6. migrate database
    ```bash
    $ php artisan migrate:fresh --seed
    ```
7. run server
    ```bash
    $ php -S localhost:8000 -t public
    ```

## Default Account

email

-   retail1@mail.com
-   store1@mail.com
-   store2@mail.com
-   customer1@mail.com

password: 12345678
