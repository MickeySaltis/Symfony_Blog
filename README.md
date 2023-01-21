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

## Environment_Docker

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

#### Trait (Evite la répétition de code dans les fichier entité / Avoid code repetition in entity files)
- Créer un fichier `project/src/Entity/Trait/CategoryTagTrait.php` / Create a file `project/src/Entity/Trait/CategoryTagTrait.php`
- Coder le fichier sur les paramètres identique à plusieurs entités / Coding the file on the same parameters to several entities. Example:
```
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait CategoryTagTrait {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank()]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank()]
    private string $slug = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descriptions = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->posts = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->slug = (new Slugify())->slugify($this->name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getDescriptions(): ?string
    {
        return $this->descriptions;
    }
    public function setDescriptions(?string $descriptions): self
    {
        $this->descriptions = $descriptions;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
```
- Insérer la ligne `use CategoryTagTrait;` dans les fichier entité en question / Insert the line `use CategoryTagTrait;` into the entity file in question. Example:
```
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use App\Entity\Trait\CategoryTagTrait;
use App\Repository\Post\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('slug', message: 'Ce slug existe déjà.')]
class Category
{
    use CategoryTagTrait;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'categories')]
    #[JoinTable(name: 'categories_posts')]
    private Collection $posts;

    public function getPosts(): Collection
    {
        return $this->posts;
    }
    public function addPost(Post $post): self
    {
        if(!$this->posts->contains($post))
        {
            $this->posts[] = $post;
        }
        return $this;
    }
    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);
        return $this;
    }
}
```

#### Event Subscriber
- Créer un dossier `EventSubscriber` dans le dossier `project/src` / Create an `EventSubscriber` folder in the `project/src` folder
- Créer un fichier comme `DropdownCategoriesSubscriber.php` dans le dossier `project/src/EventSubscriber` / Create a file like `DropdownCategoriesSubscriber.php` in the `project/src/EventSubscriber` folder
- Coder la fonction `getSubscribedEvents()` / Code the function `getSubscribedEvents()`
- Coder celon vos besoins comme injecter la table des catégories dans certain URL / Code according to your needs like injecting the category table in some URL.
Example:
```
use Twig\Environment;
use App\Repository\Post\CategoryRepository;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DropdownCategoriesSubscriber implements EventSubscriberInterface
{
    const ROUTES = ['post_index', 'category_index'];

    public function __construct(
        private CategoryRepository $categoryRepository,
        private Environment $twig
    )
    {}

    public function injectGlobalVariable(RequestEvent $event): void
    {
        $route = $event->getRequest()->get('_route');

        if(in_array($route, DropdownCategoriesSubscriber::ROUTES))
        {
            $categories = $this->categoryRepository->findAll();
            $this->twig->addGlobal('allCategories', $categories);
        }
    }
    
    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => 'injectGlobalVariable'];
    }
}
```

#### Fixture_&&_FakerPHP/Faker

##### Commands
- `docker exec -w /var/www/project www_symfony_blog composer req --dev orm-fixtures` (Installer Fixture / Install Fixture)
- `docker exec -w /var/www/project www_symfony_blog composer req --dev fakerphp/faker` (Installer FakerPHP/Faker / Install FakerPHP/Faker) [FakerPhp](https://fakerphp.github.io/)
- Coder pour générer les Fixtures dans le dossier `project/src/DataFixtures` / Code to generate Fixtures in the `project/src/DataFixtures` folder
- `make database-init` (Initier la base de donnée / Initiate the database)

#### UUID
- `composer require symfony/uid`

#### Security (Login/Logout)
- `project/config/packages/security.yaml`
```
providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: security_login
                check_path: security_login
            logout:
                path: security_logout
            remember_me:
                secret: '%kernel.secret'
                lifetime: 604800
```

## Twig

### Twig String-Extra
- `docker exec -w /var/www/project www_symfony_blog composer req twig/string-extra` (Installer Twig String-Extra / Install Twig String-Extra) [Twig String-Extra](https://github.com/twigphp/string-extra)

### Personaliser les pages d'erreurs

##### Commands
- `docker exec -w /var/www/project www_symfony_blog composer req symfony/twig-pack` (Installer Twig Pack / Install Twig Pack)
- Créer les dossiers comme ceci dans le dossier `project/templates` / Create folders like this in the `project/templates` folder: `bundles/TwigBundle/Exception`
- Créer ensuite les 3 fichiers `error.html.twig`, `error403.html.twig`, `error404.html.twig`dans le dossier `project/templates/bundles/TwigBundle/Exception` / Then create the 3 files `error.html.twig`, `error403.html.twig`, `error404.html.twig` in the folder `project/templates/bundles/TwigBundle/Exception`
- Créer un fichier `_error.html.twig` dans le dossier `project/templates/layouts` et le personaliser avec `{{ exception.statusCode }}` et `{{ exception.statusText }}` / Create a `_error.html.twig` file in the `project/templates/layouts` folder and customize it with `{{ exception.statusCode }}` and `{{ exception.statusText }}` 
- Rajouter la ligne `{% include "layouts/_error.html.twig" %}` dans les 3 fichiers situé dans le dossier `project/templates/bundles/TwigBundle/Exception` / Add the line `{% include "layouts/_error.html.twig" %}` in the 3 files located in the `project/templates/bundles/TwigBundle/Exception` folder


## Makefile

### Composition
- Makefile (Fichier contenant des groupes de commandes / File containing groups of commands)


## Webpack_Encore

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


## Database_with_the_ORM

### Steps
- Dans le fichier `project/.env` commenter `# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=14&charset=utf8"` et décommenter `DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"` / In the `project/.env` file comment `# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=14&charset=utf8"` and uncomment `DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"`
- Mettre à jour la ligne `DATABASE_URL="mysql://root:@db_symfony_blog:3306/symfony?serverVersion=5.7&charset=utf8mb4"` / Update the line `DATABASE_URL="mysql://root:@db_symfony_blog:3306/symfony?serverVersion=5.7&charset=utf8mb4"`
- Dans le terminal taper `make database-create` pour créer la base de donné symfony / In the terminal type `make database-create` to create the symfony database

#### Association mapping
- [Doctrine-project.org/association-mapping](https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/association-mapping.html)

##### Many-To-Many, Unidirectional
```
<?php
/** @Entity */
class User
{
    // ...

    /**
     * Many Users have Many Groups.
     * @ManyToMany(targetEntity="Group")
     * @JoinTable(name="users_groups",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    private $groups;

    // ...

    public function __construct() {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }
}

/** @Entity */
class Group
{
    // ...
}
```

##### Many-To-Many, Bidirectional
```
<?php
/** @Entity */
class Category
{
    // ...
    use Doctrine\Common\Collections\Collection;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\ORM\Mapping\JoinTable;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'categories')]
    #[JoinTable(name: 'categories_posts')]
    private Collection $posts;

    public function __construct()
    {
      $this->posts = new ArrayCollection();
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }
    
    public function addPost(Post $post): self
    {
        if(!$this->posts->contains($post))
        {
            $this->posts[] = $post;
        }
        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);
        return $this;
    }

    // ...
}

/** @Entity */
class Post
{
    // ...
    use Doctrine\Common\Collections\Collection;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'posts')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if(!$this->categories->contains($category))
        {
            $this->categories[] = $category;
            $category->addPost($this);
        }
        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if(!$this->categories->contains($category))
        {
            $category->removePost($this);
        }
        return $this;
    }

    // ...
}
```


## The_test_environment

### Steps
- Après avoir configurer la base de donnée, écrire dans le terminal `make database-init-test` pour créer la base de donnée symfony_test / After configuring the database, write in the terminal `make database-init-test` to create the symfony_test database
- Créer un dossier `Unit` et un dossier `Functional` dans le dossier `project/tests` / Create a `Unit` folder and a `Functional` folder in the `project/tests` folder
- Créer un fichier `BasicTest.php` dans le fichier `project/tests/Unit` avec comme classe `class BasicTest extends KernelTestCase{}` et une fonction qui comprend `$this->assertTrue(true);` / Create a `BasicTest.php` file in the `project/tests/Unit` file with the class `class BasicTest extends KernelTestCase{}` and a function that includes `$this->assertTrue(true);`
- Taper dans le terminal `docker exec -w /var/www/project www_symfony_blog php bin/phpunit` pour exécuter le test et vérifier si c'est Ok / Type in the terminal `docker exec -w /var/www/project www_symfony_blog php bin/phpunit` to run the test and check if it is Ok
- Créer un fichier `BasicTest.php` dans le fichier `project/tests/Functional` avec comme classe `class BasicTest extends WebTestCase{}` et une fonction qui comprend `$client = static::createClient(); $client->request(Request::METHOD_GET, '/'); $this->assertResponseIsSuccessful();` / Create a `BasicTest.php` file in the `project/tests/Functional` file with class `class BasicTest extends WebTestCase{}` and a function that includes `$client = static::createClient(); $client->request(Request::METHOD_GET, '/'); $this->assertResponseIs Successful();`
- Taper dans le terminal `docker exec -w /var/www/project www_symfony_blog php bin/phpunit` ou `docker exec -w /var/www/project www_symfony_blog php bin/phpunit --testdox` pour plus de détails / Type in the terminal `docker exec -w /var/www/project www_symfony_blog php bin/phpunit` or `docker exec -w /var/www/project www_symfony_blog php bin/phpunit --testdox` for more details

### Commands
- `php bin/console make:test`

### Unit testing

#### Test an entity
- Créer un fichier `project/tests/Unit/PostTest.php` / Create a file `project/tests/Unit/PostTest.php`. Example:
```
<?php

namespace App\Tests\Unit;

use App\Entity\Post\Post;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostTest extends KernelTestCase
{
    public function getEntity(): Post
    {
        return (new Post())
            ->setTitle('Post #1')
            ->setSlug('Post #1')
            ->setContent('Content #1')
            ->setUpdatedAt(new \DatetimeImmutable())
            ->setCreatedAt(new \DatetimeImmutable());
    }

    public function testPostEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $post = $this->getEntity();

        $errors = $container->get('validator')->validate($post);

        $this->assertCount(0, $errors);
    }

    public function testInvalidName()
    {
        self::bootKernel();
        $container = static::getContainer();

        $post = $this->getEntity();
        $post->setTitle('')
 
        $errors = $container->get('validator')->validate($post);

        $this->assertCount(1, $errors);
    }
}
```

### Functional testing

#### Testing a simple static page
- Créer un dossier `Post` avec un fichier `PostTest.php` dans le dossier `project/tests/Functional` et le coder / Create a `Post` folder with a `PostTest.php` file in the `project/tests/Functional` folder and code it
```
<?php

namespace App\Tests\Functional\Post;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostTest extends WebTestCase
{
    public function testBlogPageWorks(): void 
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', 'Un blog avec Symfony');
    }
}
```
- `make tests` (Exécuter tous les tests / Run all tests)

#### Test a pagination
- Sur le fichier `PostTest.php` dans le dossier `project/tests/Functional/Post` coder la fonction / On the file `PostTest.php` in the folder `project/tests/Functional/Post` code the function
```
public function testPaginationWorks(): void 
{
    $client = static::createClient();
    $crawler = $client->request(Request::METHOD_GET, '/');

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    $posts = $crawler->filter('div.card');
    $this->assertEquals(9, count($posts));

    $link = $crawler->selectLink('2')->extract(['href'])[0];
    $crawler = $client->request(Request::METHOD_GET, $link);

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    $posts = $crawler->filter('div.card');
    $this->assertGreaterThanOrEqual(1, count($posts));
}
```
- `make tests` (Exécuter tous les tests / Run all tests)


## Slugify_by_Cocur

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


## Tailwind_CSS_with_Postcss_&_Autoprefixer

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

## Tailwind_Elements

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


## Axios
- [Axios Doc](https://www.npmjs.com/package/axios)
- installation / install `docker exec -w /var/www/project www_symfony_blog npm install axios`


## Bonus

### Dicebear Avatars
- [Dicebear Avatars](https://avatars.dicebear.com/)
- [Documentation](https://avatars.dicebear.com/docs) 
