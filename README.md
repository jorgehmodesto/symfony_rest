# symfony_rest
Just a simple example of a rest api with no authentication.

# List of endpoints

Endpoint  | URL | Method
------------- | ------------- | -------------
List  | http://localhost:8080/api/ticket | GET
Show  | http://localhost:8080/api/ticket/show/{id} | GET
New  | http://localhost:8080/api/ticket | POST
Edit  | http://localhost:8080/api/ticket/{id} | PUT
Delete  | http://localhost:8080/api/ticket{id} | DELETE
Classify  | http://localhost:8080/api/ticket/classify/{id} | PUT

# Environment
Docker is awesome, so it was used for this example. To have your environment up and running, 
you gotta just use the `docker-compose up --build` (if first time run), or `docker-compose up` 
command if you already have it built in your machine. 

# First time access
To have your access to the endpoints, you should run the following command:
```
docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
```
Or if you prefer to run it inside the docker container, you should just hit:
```
docker exec -it php-container bash
```
And then, inside the container:
```
cd api
php bin/console doctrine:migrations:migrate --no-interaction
```
Then, the initial structure is gonna be created!
