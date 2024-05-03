#!/bin/bash

if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running. Please start Docker and try again."
    exit 1
fi

CHECKSUM_FILE=".dockerfile_checksum"
IMAGE_NAME="app/php:latest"

NEW_CHECKSUM=$(sha256sum Dockerfile | cut -d " " -f 1)
if [ -f $CHECKSUM_FILE ]; then
    OLD_CHECKSUM=$(cat $CHECKSUM_FILE)
else
    OLD_CHECKSUM=""
fi

if [ "$NEW_CHECKSUM" != "$OLD_CHECKSUM" ]; then
    echo "Dockerfile has changed. Removing old image."
    echo $NEW_CHECKSUM >$CHECKSUM_FILE
    docker image rm -f $IMAGE_NAME >/dev/null 2>&1
fi

IMAGE_EXISTS=$(docker images -q $IMAGE_NAME)

if [ -z "$IMAGE_EXISTS" ]; then
    echo "Image does not exist. Building image."
    docker build -t $IMAGE_NAME . || exit 1
else
    echo "Image exists. Using existing image."
fi

docker rm -f php >/dev/null 2>&1

if [ ! -f .env.local ]; then
    cp .env.local.dist .env.local
    echo "Created .env.local based on .env.local.dist"
fi

docker run -d --name php \
    --user "$(id -u):$(id -g)" \
    --env-file .env.local \
    -v $(pwd):/app \
    -w /app \
    $IMAGE_NAME bash -c "tail -f /dev/null"

echo -e "Container started.";

vendorExists=false
if [ ! -d vendor ]; then
    echo -e "Directory \033[34;1mvendor\033[0m doesn't exist. Installing dependencies."
    docker exec php composer install
    echo "Dependencies installed."
else
    vendorExists=true
fi
nodeModulesExists=false
if [ ! -d node_modules ]; then
    echo -e "Directory \033[34;1mnode_modules\033[0m doesn't exist. Installing dependencies."
    docker run --rm -v $(pwd):/app -w /app node:latest npm install
    echo "Dependencies installed."
else
    nodeModulesExists=true
fi

echo -e "Available commands:"
if [ "$vendorExists" = true ]; then
    echo -e "- Install dependencies: \033[33;1mdocker exec php composer install\033[0m"
fi
if [ "$nodeModulesExists" = true ]; then
    echo -e "- Install dependencies: \033[33;1mnpm install\033[0m"
fi
echo -e "- Enter container: \033[32;1mdocker exec -it php bash\033[0m" \
    "\n- Rebuild app: \033[32;1mdocker exec php composer cache:clear\033[0m" \
    "\n- Run tests: \033[32;1mdocker exec php composer test:ci\033[0m" \
    "\n- Release new version: \033[32;1mnpm run version:{level}\033[0m - {level}: major.minor.patch or, if it is the first release of the app: first-release.\n If You don't have npm installed, use: \033[32;1mdocker run --rm -v \$(pwd):/app -w /app node:latest npm run version:{level}\033[0m"

rm -rf develop.sh

# If you have problems with permissions, uncomment the following line and run the script again:
# sudo chown -R $USER .
