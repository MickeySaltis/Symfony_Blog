# Symfony_Blog


## Environment Docker

### Composition
- docker-compose.yml (Configuration des services de l'application / Configuration of the application services)
- Docker/Dockerfile (Instruction à exécuter pour construire une image Docker / Instruction to execute to build a Docker image)
- Docker/vhost/vhost.conf (Configuration de l'hébergement virtuel / Virtual hosting configuration)
    
### Command
- `docker-compose build` (Construire l'environnement / Building the environment)
- `docker-compose up` (Démarrer les containers / Start the containers)
- `docker-compose stop` (Arrêter les containers / Stop the containers)
- `docker-compose ps` (Afficher les containers qui tournent actuellement / Display the containers that are currently running)


## Symfony

## Composition
- project (Dossier racine du projet Symfony / Symfony project root folder)

### Command
- `docker exec www_symfony_blog composer create-project symfony/website-skeleton project` (Créer un projet Symfony dans le container www sous le nom de projet / Create a Symfony project in the www container under the project name)
- `docker exec -ti www_symfony_blog bash` (Ouvrir le terminal du container www dans docker / Open the www container terminal in docker)
- `sudo chown -R $USER ./` | `sudo chown -R project` (Changer le propriétaire du dossier / Change the owner of the file)


## Makefile

### Composition
- Makefile (Fichier contenant des groupes de commandes / File containing groups of commands)
