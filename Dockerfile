RUN docker volume create slv-mysql

RUN docker run \
    -d \
    -p 3336:3306 \
    --name=slv-mysql \
    -e MYSQL_ROOT_PASSWORD="Not24get" \
    -v slv-mysql:/var/lib/mysql:rw \
mysql:8.0.39