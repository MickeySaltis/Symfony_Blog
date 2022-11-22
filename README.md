# Symfony_Blog


## Environment Docker

### Composition
- docker-compose.yml (Configuration des services de l'application / Configuration of the application services)
- Docker/Dockerfile (Instruction à exécuter pour construire une image Docker / Instruction to execute to build a Docker image)
- Docker/vhost/vhost.conf (Configuration de l'hébergement virtuel / Virtual hosting configuration)
    
### Commands
- `docker-compose build` (Construire l'environnement / Building the environment)
- `docker-compose up` (Démarrer les containers / Start the containers)
- `docker-compose stop` (Arrêter les containers / Stop the containers)
- `docker-compose ps` (Afficher les containers qui tournent actuellement / Display the containers that are currently running)


## Symfony

## Composition
- project (Dossier racine du projet Symfony / Symfony project root folder)

### Commands
- `docker exec www_symfony_blog composer create-project symfony/website-skeleton project` (Créer un projet Symfony dans le container www sous le nom de projet / Create a Symfony project in the www container under the project name)
- `docker exec -ti www_symfony_blog bash` (Ouvrir le terminal du container www dans docker / Open the www container terminal in docker)
- `sudo chown -R $USER ./` | `sudo chown -R project` (Changer le propriétaire du dossier / Change the owner of the file)

#### Entity (Create / Migration / Migrate)
- `php bin/console make:entity [name of the entity]` (Créer une entitée / Create a entity)
- `make migration` (Créer un fichier SQL destiné à mettre à jour la base de donnée / Create a SQL file to update the database)
- `make migrate` (Mettre à jour la base de donnée avec le fichier de migration / Update the database with the migration file)

##### Composition of the entity
Une entité est une classe PHP, qui peut être connectée à une table de la base de donnée via l'ORM, elle est accompagné d'une fichier "Repository". Une entité contient plusieurs champs dont un qui est son "identifiant"(Id, clé primaire et auto-incrémenté).
Un champ est caractérisé par:
- son type
- sa taille
- s'il peut être null ou non
- s'il doit être unique ou non

An entity is a PHP class, which can be connected to a table of the database via the ORM, it is accompanied by a "Repository" file. An entity contains several fields, one of which is its "identifier" (Id, primary key and auto-incremented).
A field is characterized by:
- its type
- its size
- whether it can be null or not
- whether it must be unique or not


## Makefile

### Composition
- Makefile (Fichier contenant des groupes de commandes / File containing groups of commands)


## Webpack Encore

### Steps
- Installer Webpack Encore / Install Webpack Encore
- Installer Npm / Install Npm
- Dans le fichier 'project/assets/app.js' inscrire `console.log(`Webpack Encore is working`);` / In the file 'project/assets/app.js' write `console.log(`Webpack Encore is working`);`
- Lancer un script Npm / Run an Npm script
- Regarder dans la console si le message 'Webpack Encore is working' remonte / Look in the console if the 'Webpack Encore is working' message comes up

### Commands
- `docker exec -w /var/www/project www_symfony_blog composer req symfony/webpack-encore-bundle` (Installer Webpack Encore dans le dossier racine de Symfony dans le container www / Install Webpack Encore in the root folder of Symfony in the www container)
- `docker exec -w /var/www/project www_symfony_blog npm install` (Installer Npm dans le dossier racine de Symfony dans le container www / Install Npm in the root folder of Symfony in the www container)
- `sudo chown -R $USER ./` | `sudo chown -R project` (Changer le propriétaire du dossier / Change the owner of the file)
- `docker exec -w /var/www/project www_symfony_blog npm run dev` (Lancer le script dev de Npm dans le dossier racine de Symfony dans le container www / Run the dev Npm script in the root folder of Symfony in the www container)
- `docker exec -w /var/www/project www_symfony_blog npm run watch` (Lancer le script watch de Npm dans le dossier racine de Symfony dans le container www / Run the watch Npm script in the root folder of Symfony in the www container)
- `docker exec -w /var/www/project www_symfony_blog npm run build` (Lancer le script build de Npm dans le dossier racine de Symfony dans le container www / Run the build Npm script in the root folder of Symfony in the www container)


## Database with the ORM

### Steps
- Dans le fichier `project/.env` commenter `# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=14&charset=utf8"` et décommenter `DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"` / In the `project/.env` file comment `# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=14&charset=utf8"` and uncomment `DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"`
- Mettre à jour la ligne `DATABASE_URL="mysql://root:@db_symfony_blog:3306/symfony?serverVersion=5.7&charset=utf8mb4"` / Update the line `DATABASE_URL="mysql://root:@db_symfony_blog:3306/symfony?serverVersion=5.7&charset=utf8mb4"`
- Dans le terminal taper `make database-create` pour créer la base de donné symfony / In the terminal type `make database-create` to create the symfony database


## The test environment

### Steps
- Après avoir configurer la base de donnée, écrire dans le terminal `make database-init-test` pour créer la base de donnée symfony_test / After configuring the database, write in the terminal `make database-init-test` to create the symfony_test database
- Créer un dossier `Unit` et un dossier `Functional` dans le dossier `project/tests` / Create a `Unit` folder and a `Functional` folder in the `project/tests` folder
- Créer un fichier `BasicTest.php` dans le fichier `project/tests/Unit` avec comme classe `class BasicTest extends KernelTestCase{}` et une fonction qui comprend `$this->assertTrue(true);` / Create a `BasicTest.php` file in the `project/tests/Unit` file with the class `class BasicTest extends KernelTestCase{}` and a function that includes `$this->assertTrue(true);`
- Taper dans le terminal `docker exec -w /var/www/project www_symfony_blog php bin/phpunit` pour exécuter le test et vérifier si c'est Ok / Type in the terminal `docker exec -w /var/www/project www_symfony_blog php bin/phpunit` to run the test and check if it is Ok
- Créer un fichier `BasicTest.php` dans le fichier `project/tests/Functional` avec comme classe `class BasicTest extends WebTestCase{}` et une fonction qui comprend `$client = static::createClient(); $client->request(Request::METHOD_GET, '/'); $this->assertResponseIsSuccessful();` / Create a `BasicTest.php` file in the `project/tests/Functional` file with class `class BasicTest extends WebTestCase{}` and a function that includes `$client = static::createClient(); $client->request(Request::METHOD_GET, '/'); $this->assertResponseIs Successful();`
- Taper dans le terminal `docker exec -w /var/www/project www_symfony_blog php bin/phpunit` ou `docker exec -w /var/www/project www_symfony_blog php bin/phpunit --testdox` pour plus de détails / Type in the terminal `docker exec -w /var/www/project www_symfony_blog php bin/phpunit` or `docker exec -w /var/www/project www_symfony_blog php bin/phpunit --testdox` for more details


## Slugify by Cocur

### Commands
- `docker exec -w /var/www/project www_symfony_blog composer req cocur/slugify` (Installer Cocur/Slugify / Install Cocur/Slugify) ``https://github.com/cocur/slugify``


## VichUploader
- `docker exec -w /var/www/project www_symfony_blog composer req vich/uploader-bundle` (Installer VichUploader / Install VichUploader) ``https://github.com/dustin10/VichUploaderBundle`
- Vérifier dans le fichier `project/config/bundles.php` si la ligne `Vich\UploaderBundle\VichUploaderBundle::class => ['all' => true],` sinon la rajouter / Check in the file `project/config/bundles.php` if the line `Vich\UploaderBundle::class => ['all' => true],` otherwise add it
- Vérifier dans le dossier `project/config/packages` s'il y a le fichier `vich_uploader.yaml` sinon le créer manuellement avec comme contenu: 
`vich_uploader:
  db_driver: orm
  
  metadata:
    type: attribute

  mappings:
    post_thumbnail:
      uri_prefix: /images/posts
      upload_destination: "%kernel.project_dir%/public/images/posts"
      namer: Vich\UploaderBundle\Naming\SmartUniqueNamer`

Check in the folder `project/config/packages` if there is the file `vich_uploader.yaml` otherwise create it manually with the content:
`vich_uploader:
  db_driver: orm
  
  metadata:
    type: attribute

  mappings:
    post_thumbnail:
      uri_prefix: /images/posts
      upload_destination: "%kernel.project_dir%/public/images/posts"
      namer: Vich\UploaderBundle\Naming\SmartUniqueNamer`
- Créer une nouvelle entité `php bin/console make:entity Thumbnail` / Create a new entity `php bin/console make:entity Thumbnail`
