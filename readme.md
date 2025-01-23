# E-sport Website - Déploiement Local

Ce projet permet de créer un site web pour la gestion d'événements e-sport avec des fonctionnalités de connexion, de gestion de comptes utilisateurs et d'administration. Ce guide vous montre comment déployer ce projet en local.

## Prérequis

Avant de déployer le projet en local, vous devez avoir les éléments suivants installés :

- [XAMPP](https://www.apachefriends.org/index.html) ou [MAMP](https://www.mamp.info/en/) (pour gérer Apache et MySQL en local)
- [PHP](https://www.php.net/downloads.php)
- [MySQL](https://www.mysql.com/fr/)
- [Composer](https://getcomposer.org/) (si nécessaire pour la gestion des dépendances PHP)
- Un éditeur de code comme [VS Code](https://code.visualstudio.com/) ou [PHPStorm](https://www.jetbrains.com/phpstorm/)

## Étapes de Déploiement

### 1. Cloner le repository

Si vous n'avez pas encore cloné le projet, vous pouvez le faire avec la commande suivante :

```bash
git clone https://github.com/sandro-rm/esportify2

2. Placer les fichiers dans le dossier htdocs
Dans votre dossier XAMPP (par défaut C:\xampp\htdocs pour Windows ou /Applications/XAMPP/htdocs/ pour Mac), créez un dossier pour votre projet (par exemple esport_website), puis placez tous les fichiers du projet à l'intérieur de ce dossier.

3. Configurer la base de données
Ouvrez phpMyAdmin (en accédant à http://localhost/phpmyadmin/ dans votre navigateur).
Créez une nouvelle base de données avec le nom de votre choix (par exemple esport_db).
Importez le fichier .sql de votre base de données (si vous en avez un) en sélectionnant la base de données dans phpMyAdmin et en utilisant l'option Importer.

4. Configurer le fichier config.php
Dans le fichier config.php, vous devrez renseigner vos informations de connexion à la base de données. 
Assurez-vous que ces informations correspondent à votre configuration locale. Si vous utilisez MAMP, le nom d'utilisateur peut être root et le mot de passe peut être root par défaut.

5. Démarrer les services
Ouvrez XAMPP ou MAMP et démarrez les services Apache et MySQL.
Accédez à http://localhost/votre-dossier dans votre navigateur pour voir le site.

Connexion au site
Une fois les services Apache et MySQL démarrés, vous pouvez accéder à votre site localement via votre navigateur en allant à l'URL suivante :
http://localhost:3000/index.html ou depuis l'application VS CODE.

6. Connexion au site
Lors de la première connexion, vous devrez utiliser un compte administrateur (si configuré dans votre base de données) pour accéder à l'interface d'administration.
Si vous êtes un utilisateur classique, vous serez redirigé vers le tableau de bord des événements.


7. Dépendances
Si votre projet utilise des dépendances PHP via Composer, vous pouvez les installer en exécutant la commande suivante dans le répertoire du projet :
composer install
Cela installera toutes les dépendances nécessaires pour faire fonctionner votre projet.


