
Le projet nécessite l'installation des packages de apache, mysql,   phpyadmin, composer.
Avoir préparé un utilisateur avec des droits de vue, création, modification et suppression sur une base de données.

Créer le fichier .env avec la ligne de commande suivante "DATABASE_URL=mysql://[nom_utilisateur]:[mot_de_passe]@[adesse]:[port]/[nom_de_la_base_de_données]" pour indiqué au projet quelle base de données utiliser avec quelle utilisateur.

Lancer la commande "composer require doctrine/doctrine-fixtures-bundle" pour télécharger les fixtures nécessaires au site.

Pour créer la base de données il faut utiliser les commandes suivantes : 
Si la base de données n'a pas été créee utilisé la commande "php bin/console doctrine:database:create".
Si il n'y a pas de fichier "src/Migrations/Version* .php" utiliser la commande "php bin/console doctrine:make:migration", puis modifier le fichier dans la première méthode addSql de la fonction up, remplacer 'JSON NOT NULL' par "TEXT NOT NULL COMMENT \'(DC2Type:json_array)\'" pour l'attribut 'roles'.
"php bin/console doctrine:migrations:migrate"
"php bin/console doctrine:fixtures:load"

Pour activer le code js et css : 
Créer un dossier build dans le dossier public.
Déplacer les fichiers app.css et app.js des dossiers memo/memo_css/app.css et memo/memo_js/app.js dans le dossier public/build.

Pour récupérer les images : 
Ajouter la branche img/uploads/ dans le dossier public.
Puis copier les dossiers User et Products du dossier memo_image vers le dossier uploads.

