
Le projet nécessite l'installation des packages de apache, mysql, phpyadmin, composer ainsi que yarn ou npm dans le projet en question.
Avoir préparé un utilisateur avec des droits de vue, création, modification et suppression sur une base de données.

Ajouter un fichier .env.local avec la ligne suivante "DATABASE_URL=mysql://[nom_utilisateur]:[mot_de_passe]@[adesse]:[port]/[nom_de_la_base_de_données]" pour indiqué au projet quelle base de données utiliser avec quelle utilisateur.
Placez aussi "APP_ENV=dev" dans le fichier.


Lancer la commande "composer require" pour télécharger les fixtures nécessaires au site.

Pour créer la base de données il faut utiliser les commandes suivantes : 
Si la base de données n'a pas été créee utilisé la commande "php bin/console doctrine:database:create".
Si il n'y a pas de fichier "src/Migrations/Version* .php" utiliser la commande "php bin/console doctrine:make:migration", puis modifier le fichier dans la première méthode addSql de la fonction up, remplacer 'JSON NOT NULL' par "TEXT NOT NULL COMMENT \'(DC2Type:json_array)\'" pour l'attribut 'roles' de la class User et pour l'attribut 'area' de la class CompanyDelivery.
"php bin/console doctrine:migrations:migrate"
"php bin/console doctrine:fixtures:load"

Pour activer le code js et css utiliser la commande "yarn install" puis "yarn encore dev" ou "npm install" puis "npm run dev" en fonction du gestionnaire de paquets que vous avez installé pour créer ou modifier le fichier app.css et app.js dans le dossier public/build.

Pour récupérer les images : 
Copier ou déplacer le dossier uploads contenu dans le chemin suivant memo/memo_img/ à public/img/ .

Les fonctionnalité demandant d'envoyer des mails ne fonctionnent pas car les configurations et l'adresse mail du serveur ne sont pas fait.

Les noms de comptes et les mots de passes sont écrits dans le fichier UserFixtures.php dans le dossier src/DataFixtures.
