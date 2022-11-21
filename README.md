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