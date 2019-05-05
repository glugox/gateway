
if [ ! -f "database/db.sqlite" ]; then touch database/db.sqlite; fi

php artisan migrate:refresh
php artisan db:seed
