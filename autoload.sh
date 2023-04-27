#! /usr/bin/bash
docker info > /dev/null 2>&1

# Ensure that Docker is running...
if [ $? -ne 0 ]; then
    echo "Docker is not running."

    exit 1
fi

cp .env.example .env

docker run --rm \
    --pull=always \
    -v "$(pwd)":/opt \
    -w /opt \
    laravelsail/php82-composer:latest \
    composer require laravel/sail --dev

docker run --rm \
    --pull=always \
    -v "$(pwd)":/opt \
    -w /opt \
    laravelsail/php82-composer:latest \
    bash -c "php ./artisan sail:install --with=mysql,redis"

./vendor/bin/sail pull mysql redis
./vendor/bin/sail build --no-cache

CYAN='\033[0;36m'
LIGHT_CYAN='\033[1;36m'
BOLD='\033[1m'
NC='\033[0m'

echo ""

if sudo -n true 2>/dev/null; then
    sudo chown -R $USER: .
else
    echo -e "${BOLD}Please provide your password so we can make some final adjustments to your application's permissions.${NC}"
    echo ""
    sudo chown -R $USER: .
    echo ""
fi

./vendor/bin/sail up -d &
wait
./vendor/bin/sail php artisan key:generate &
wait
./vendor/bin/sail php artisan migrate &
wait

echo 'You can start use ./vendor/bin/sail up -d'
