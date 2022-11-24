# Symfony_Blog

## Table
1. [Environment Docker](#Environment_Docker)
2. [Symfony](#Symfony)
3. [Twig](#Twig)
4. [Webpack Encore](#Webpack_Encore)
5. [Database with the ORM](#Database_with_the_ORM)
6. [The test environment](#The_test_environment)
7. [Slugify by Cocur](#Slugify_by_Cocur)
8. [VichUploader](#VichUploader)
9. [Tailwind CSS with Postcss & Autoprefixer](#Tailwind_CSS_with_Postcss_&_Autoprefixer)
10. [Tailwind Elements](#Tailwind_Elements)
11. [KNP Paginator](#KNP_Paginator)

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

### Composition
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

#### Fixture && FakerPHP/Faker

##### Commands
- `docker exec -w /var/www/project www_symfony_blog composer req --dev orm-fixtures` (Installer Fixture / Install Fixture)
- `docker exec -w /var/www/project www_symfony_blog composer req --dev fakerphp/faker` (Installer FakerPHP/Faker / Install FakerPHP/Faker) [FakerPhp](https://fakerphp.github.io/)
- Coder pour générer les Fixtures dans le dossier `project/src/DataFixtures` / Code to generate Fixtures in the `project/src/DataFixtures` folder
- `make database-init` (Initier la base de donnée / Initiate the database)


## Twig

### Twig String-Extra
- `docker exec -w /var/www/project www_symfony_blog composer req twig/string-extra` (Installer Twig String-Extra / Install Twig String-Extra) [Twig String-Extra](https://github.com/twigphp/string-extra)


## Makefile

### Composition
- Makefile (Fichier contenant des groupes de commandes / File containing groups of commands)


## Webpack Encore

### Steps
- Installer Webpack Encore / Install Webpack Encore [Webpack Encore](https://symfony.com/doc/current/frontend.html)
- Installer Npm / Install Npm [Npm](https://www.npmjs.com/)
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

### Installer
- `docker exec -w /var/www/project www_symfony_blog composer req cocur/slugify` (Installer Cocur/Slugify / Install Cocur/Slugify) [Cocur Slugify](https://github.com/cocur/slugify)


## VichUploader

### Install and configure
- `docker exec -w /var/www/project www_symfony_blog composer req vich/uploader-bundle` (Installer VichUploader / Install VichUploader) [Vich Uploader](https://github.com/dustin10/VichUploaderBundle)
- Vérifier dans le fichier `project/config/bundles.php` si la ligne `Vich\UploaderBundle\VichUploaderBundle::class => ['all' => true],` sinon la rajouter / Check in the file `project/config/bundles.php` if the line `Vich\UploaderBundle::class => ['all' => true],` otherwise add it
- Vérifier dans le dossier `project/config/packages` s'il y a le fichier `vich_uploader.yaml` sinon le créer manuellement avec comme contenu: 
```
vich_uploader:
  db_driver: orm
  
  metadata:
    type: attribute

  mappings:
    post_thumbnail:
      uri_prefix: /images/posts
      upload_destination: "%kernel.project_dir%/public/images/posts"
      namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
```  

Check in the folder `project/config/packages` if there is the file `vich_uploader.yaml` otherwise create it manually with the content:
```
vich_uploader:
  db_driver: orm
  
  metadata:
    type: attribute

  mappings:
    post_thumbnail:
      uri_prefix: /images/posts
      upload_destination: "%kernel.project_dir%/public/images/posts"
      namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
``` 
- Créer une nouvelle entité `php bin/console make:entity Thumbnail` / Create a new entity `php bin/console make:entity Thumbnail`


## Tailwind CSS with Postcss & Autoprefixer

### Install and configure
- `docker exec -ti www_symfony_blog bash` (Ouvrir le terminal du container www / Open the container terminal www )
-`cd project` (Se déplacer dans le dossier Project / Move to the Project folder)
- `npm install -D tailwindcss postcss autoprefixer` (Installer [Tailwind CSS](https://tailwindcss.com/), [Postcss](https://postcss.org/) et [Autoprefixer](https://www.npmjs.com/package/autoprefixer) / Install Tailwind CSS, Postcss and Autoprefixer)
-`npx tailwindcss init` (Initier Tailwind CSS / Initiate Tailwind CSS)
- Dans le dossier `project/tailwind.config.js` rajouter les chemins des templates et scripts js dans le `content` (exemple: `'templates/**/*.html.twig'`, `'assets/scripts/*.js'`) / In the `project/tailwind.config.js` folder add the paths to the templates and js scripts in the `content` (example: `'templates/**/*.html.twig'`, `'assets/scripts/*.js'`)
- `npm install -D postcss-loader` (Installer les packages de postcss / Install the postcss packages)
- Dans le fichier `project/webpack.config.js` ajouter la ligne `.enablePostCssLoader()` / In the file `project/webpack.config.js` add the line `.enablePostCssLoader()`
- Créer un fichier `postcss.config.js` à la racine du dossier `project`. Et rajouter les lignes suivantes:
```
  module.exports = {
      plugins: {
          tailwindcss: {},
          autoprefixer: {}
      }
  };
```
Create a `postcss.config.js` file in the root of the `project` folder. And add the following lines:
```
  module.exports = {
      plugins: {
          tailwindcss: {},
          autoprefixer: {}
      }
  };
```
- Dans le fichier ``project/assets/styles/app.css`` ajouter les lignes suivante:
```
  @tailwind base;
  @tailwind components;
  @tailwind utilities;
```
  In the file ``project/assets/styles/app.css`` add the following lines:
```
  @tailwind base;
  @tailwind components;
  @tailwind utilities;
```
- `make npm-watch`

## Tailwind Elements

### Install and configure
- `docker exec -ti www_symfony_blog bash` (Ouvrir le terminal du container www / Open the container terminal www )
- `cd project` (Se déplacer dans le dossier Project / Move to the Project folder)
- `npm install tw-elements` (Installer [Tailwind Elements](https://tailwind-elements.com/quick-start/) / Install Tailwind Elements)
- Dans le fichier `project/tailwind.config.json` rajouter la ligne `'node_modules/tw-elements/dist/js/**/*.js'` dans `content` et `require('tw-elements/dist/plugin')` dans `plugins` / In the file `project/tailwind.config.json` add the line `'node_modules/tw-elements/dist/js/**/*.js'` in `content` and `require('tw-elements/dist/plugin')` in `plugins`
- Dans le fichier `project/assets/app.js` ajouter la ligne `import 'tw-elements';` / In the file `project/assets/app.js` add the line `import 'tw-elements';`
- `make npm-watch`


## KNP_Paginator

### Install and configure
- `docker exec -w /var/www/project www_symfony_blog composer req knplabs/knp-paginator-bundle` (Installer [KNP Paginator](https://github.com/KnpLabs/KnpPaginatorBundle) / Install KNP Paginator)
- Dans le dossier `project/config` créer un fichier `knp_paginator.yaml` / In the `project/config` folder create a `knp_paginator.yaml` file
- Indiquer dans ce fichier / Indicate in this file:
```
knp_paginator:
    page_range: 5 
    default_options:
        page_name: page                
        sort_field_name: sort           
        sort_direction_name: direction  
        distinct: true                  
        filter_field_name: filterField  
        filter_value_name: filterValue  
    template:
        pagination: '@KnpPaginator/Pagination/sliding.html.twig'     
        sortable: '@KnpPaginator/Pagination/sortable_link.html.twig' 
        filtration: '@KnpPaginator/Pagination/filtration.html.twig'  
```

### Personaliser la pagination
- Dans le fichier `project/config/packages/knp_paginator.yaml` modifier la ligne `pagination` de `template` avec les noms des fichiers situé dans le dossier `project/vendor/knplabs/knp-paginator-bundle/templates/Pagination`. Exemple: `@KnpPaginator/Pagination/tailwindcss_pagination.html.twig`. Ou créer un fichier et mettre à jour le fichier `project/config/packages/knp_paginator.yaml`. Exemple: `components/_pagination.html.twig` / In the `project/config/packages/knp_paginator.yaml` file modify the `pagination` line of `template` with the names of the files located in the `project/vendor/knplabs/knp-paginator-bundle/templates/Pagination` folder. Example: `@KnpPaginator/Pagination/tailwindcss_pagination.html.twig`. Or create a file and update the file `project/config/packages/knp_paginator.yaml`. Example: `components/_pagination.html.twig`

