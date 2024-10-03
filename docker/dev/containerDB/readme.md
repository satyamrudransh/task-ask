<div id="top"></div>

## Docker

### 1- First create a docker bridge network, so that individual containers connect to database(mysql) container seemlessly.

```
docker network create mysqlDB-network
```

<p align="right">(<a href="#top">back to top</a>)</p>

## Laravel

### 1- Create a laravel app or use existing app with dockerfile to create docker image.

```
sudo docker build -t testmachine:dev  -f docker/dev/containerDB/dockerfile .
```

### 2- Run created image as container.

```
sudo docker run -v ${PWD}:/app -d -p 8096:8096 --name testmachine --network mysqlDB-network --link mysqlLocalDB:db testmachine:dev
```

### 3- Bash into created container to start laravel app.

#### replace <container_id or container_name> in later command with <laravel_app> or with the container id to check container id execute

```
docker ps
```

```
docker exec -it <container_id or container_name> sh
```

#### used the docker id assigned to it.

### 4- When inside container shell create application key using.

```
php artisan key:generate
```

### 5- last serve the laravel app to localhost.

```
php artisan serve --host=0.0.0.0
```

<p align="right">(<a href="#top">back to top</a>)</p>

## MYSQL

#### 1- Pull mysql image from docker hub and run as container.

```
docker run -d -p 3306:3306 --name mysql --network mysql -e MYSQL_ROOT_PASSWORD=123456 mysql
```

#### 2- Bash into mysql container.

```
docker exec -it mysql bash
```

#### 3- Connect to mysql cli.

```
mysql -u root -p
```

#### when promted for password enter 123456 as stated in MYSQL step 1.

#### 4- create a test database.

```
CREATE DATABASE testdb;
```

<p align="right">(<a href="#top">back to top</a>)</p>

## Phpmyadmin

#### 1- pull phpmyadmin image from docker hub and run as container.

```
docker run -d -p 8080:80 --name phpmyadmin --network mysql --link mysql:db phpmyadmin
```

#### 2- once all the three containers are up and running,create a new user e.g "forethought" in phpmyadmin and then change the environment variables as stated below in laravel app to establish connection between laravel app and mysql.

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=testdb
DB_USERNAME=forethought
DB_PASSWORD=123456
```

#### 3- once the env variables are saved, bash into laravel app container clear config and cache then migrate database from mysql database.

```
docker exec -it laravel_app sh

php artisan config:cache

php artisan migrate
```

<p align="right">(<a href="#top">back to top</a>)</p>
