## Backend project for Laravel

### Настройка
`composer install`

`php -r "file_exists('.env') || copy('.env.example', '.env');"`

`php artisan key:generate --ansi`

`php artisan jwt:secret`

### Миграции
`php artisan db:seed --class=DomainSeeder`

`php artisan db:seed --class=TechnologiesSeeder`

`php artisan db:seed --class=BenefitsSeeder`

`php artisan db:seed --class=WorldSeeder`
