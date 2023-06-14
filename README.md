
# Test technique api commercial

Petite documentation sur l'installation et l'utilisation de l'api commercial




## Stack technique

* php7.4
* symfony 5.4
* mysql 8
* phpunit
## Modélisation de la base de données
Le projet est constitué de 4 entités:
Toutes les entités ont un attribut id, et tous les attributs de toutes les entités sont obligatoires.

* ### User:
  * nom: string
  * prénom: string
  * email: string
  * password: string
  * dateDeNaissance: date
* ### Societe:
  * nom: string
* ### TypeDeNote:
  * libelle: string
  * code: string
* ### NoteDeFrais:
  * dateDeLaNote: date
  * montant: float
  * dateDeCreation: datetime (généré automatiquement)
  * dateDeModification: datetime (généré automatiquement)
  * type: TypeDeNote (ManyToOne)
  * societe: Societe (ManyToOne)
  * user: User (ManyToOne)


## Structure

### Controleurs
Le projet est composé de 3 controleurs:

* #### NoteDeFraisController:
Utilisé comme point d'entrée pour l'api de gestion des notes de frais (lister toutes les notes de frais en GET, afficher une seule note de frais en GET, créer une note de frais en POST, modifier une note de frais en PUT et supprimer une note de frais en DELETE)

* #### TypeDeNoteController:
Utilisé pour lister les type de note de frais disponibles en GET

* #### AuthController:
Utilisé pour gérer l'authentification des utilisateurs
### Services

Le projet est composé d'un seul service:
* #### NoteDeFraisService:
Utilisé pour valider les données envoyées à l'api, pour formater l'output de l'api et pour gérer les erreurs

### L'authentification
L'authentification se fait par demande de token, sa gestion est faite par le bundle "lexik/jwt-authentication-bundle"


## Installation

Le projet a été réalisé et testé sur ubuntu 20.04

### Prérequis

* php 7.4 (https://www.digitalocean.com/community/tutorials/how-to-install-php-7-4-and-set-up-a-local-development-environment-on-ubuntu-20-04)
* composer (https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
* mysql 8 (https://dev.mysql.com/doc/mysql-apt-repo-quick-guide/en/#apt-repo-fresh-install)
* CLI de symfony (https://symfony.com/download)

note: l'installation de certaines extensions de php7.4 sera peut-être nécessaire

### Installation
1- Cloner le projet puis accéder au dossier cloné

2- Installer les dépendances à l'aide de composer

```bash
  composer install
```
3- Configurer le user et le password de mysql dans le fichier .env (remplacer 'mysqlUser' par votre user mysql et 'mysqlPassword' par votre mot de passe mysql)

4- Configurer le host et le port de mysql dans le fichier .env si nécessaire

5- Créer la base de données
```bash
  php bin/console doctrine:database:create
```

6- Jouer les migrations

```bash
  php bin/console doctrine:migration:migrate
```

7- Charger les data fixtures
```bash
  php bin/console fixtures:load
```

8- Lancer le serveur symfony
```bash
  symfony server:start
```
## Utilisation

L'api est accessible sur l'url affichée après le lancement du serveur symfony ( http://127.0.0.1:8000 par défaut si le port 8000 n'est pas utilisé)

Le chargement des data fixtures devrait créer un utilisateur ({"username": "commercial1@email.com", "password": "123"}), l'authentification se fait par token.

### Authentification
Pour récupérer un token il faudrait faire une requête POST sur la route localhost:8000/api/login_check avec les infos de l'utilisateur dans le body ({"username": "commercial1@email.com", "password": "123"}), exemple de requête de demande de token en wget:

```
wget --no-check-certificate --quiet \
  --method POST \
  --timeout=0 \
  --header 'Content-Type: application/json' \
  --body-data '{"username": "commercial1@email.com", "password": "123"}' \
   'localhost:8000/api/login_check'
```

L'api devrait renvoyer un token sous le format
``
{
"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODY2MTcwNjEsImV4cCI6MTY4NjYyMDY2MSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiY29tbWVyY2lhbDFAZW1haWwuY29tIn0.UoeJw0U-578mumMLlov6--S-GpcE7lAU3vLqI-gwtMR78z8e0BHkvI8xFBvkGNo5S3oe59-raquGDTlvXRKRxLj5o8306sp9hlooHCNmdAx1wBmlI6arjIvYgCZHCf7tXoLhh1ALl8BkwyROn5bA2dDRdMly-L8FzghAl71BcRhihnvhRMpd5sr1hJIU6UnT1lHMTJMZFBNGE0dI4SiVxracPaHGq5VgMAm73oLFAHUjKcWs1hBo17xNEsmhKGqr3FM1czmqXRz-wDipZajp4eGzb6akoc-rPXRXE0nW-LDup8OEM2AvkkRIwQzWbobyftvTQbDbrcT6XV0AHHrhlTp1LQgkh-boH8UKTZgl6WEhqC9lbmqsAvihaQq2tpfDAuVyq9vFKbGBQhj4Odh5F3Uwhw1kb8h7eXwsa4A-K2MlZdFbICHr1iJk5lqHey5eYs3y3ARm3-8LGK5Q9WCpgl18fSy2GamOc4PQyg4qqtMOJiULjGRopsuzSpVLfX0Gy1wcuIa5a3wL2yoVTzHKsHa-wSB72DVpASFZESLpwMTnD4o23OZzeDmpfLjFBQgsmfeGU8dii_mHYzqjAPp1ZBBIHBTJUVh5LUzfe75RQF__vgrXG2ZEtJjUSNBGAkSH8-kHuWndQBJcQKBoLIe0ud8JTP-4Zp78mzknQ_k2wdQ"
}
``

### Récupération de la liste des notes de frais

```
wget --no-check-certificate --quiet \
  --method GET \
  --timeout=0 \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer mettre_le_token_d_authentification_ici' \
   'localhost:8000/api/noteDeFrais'
```

exemple de response:
``
[
{
"id": 1,
"dateDeLaNote": "2022-12-10",
"montant": 830,
"typeDeNote": "repas",
"societe": "societe de test",
"commercial": {
"id": 1,
"nom": "nom commercial",
"prenom": "prenom commercial",
"email": "commercial1@email.com"
},
"dateDeCreation": "2023-06-13T02:34:32",
"dateDeModification": "2023-06-13T02:46:08"
},
{
"id": 2,
"dateDeLaNote": "2022-12-10",
"montant": 450.2,
"typeDeNote": "essence",
"societe": "societe de test",
"commercial": {
"id": 1,
"nom": "nom commercial",
"prenom": "prenom commercial",
"email": "commercial1@email.com"
},
"dateDeCreation": "2023-06-13T02:45:10",
"dateDeModification": "2023-06-13T02:45:10"
}
]
``

### Récupération d'une note de frais par id

```
wget --no-check-certificate --quiet \
  --method GET \
  --timeout=0 \
  --header 'Authorization: Bearer mettre_le_token_d_authentification_ici' \
   'localhost:8000/api/noteDeFrais/1'
```

exemple de response:
``
{
"id": 1,
"dateDeLaNote": "2022-12-10",
"montant": 450.2,
"typeDeNote": "péage",
"societe": "societe de test",
"commercial": {
"id": 1,
"nom": "nom commercial",
"prenom": "prenom commercial",
"email": "commercial1@email.com"
},
"dateDeCreation": "2023-06-13T02:34:32",
"dateDeModification": "2023-06-13T02:34:32"
}
``

### Création d'une note de frais

```
wget --no-check-certificate --quiet \
  --method POST \
  --timeout=0 \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer mettre_le_token_d_authentification_ici' \
  --body-data '{
    "codeTypeDeNote": "ESSENCE",
    "societe": 1,
    "montant": 450.20,
    "dateDeLaNote": "2022-12-10"
}' \
   'localhost:8000/api/noteDeFrais'
```

exemple de response:
``
{
"id": 3,
"dateDeLaNote": "2022-12-10",
"montant": 450.2,
"typeDeNote": "essence",
"societe": "societe de test",
"commercial": {
"id": 1,
"nom": "nom commercial",
"prenom": "prenom commercial",
"email": "commercial1@email.com"
},
"dateDeCreation": "2023-06-13T04:48:46",
"dateDeModification": "2023-06-13T04:48:46"
}
``

### Modification d'une note de frais

```
wget --no-check-certificate --quiet \
  --method PUT \
  --timeout=0 \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer mettre_le_token_d_authentification_ici' \
  --body-data '{
    "codeTypeDeNote": "REPAS",
    "societe": 1,
    "montant": 830,
    "dateDeLaNote": "2022-12-10"
}' \
   'localhost:8000/api/noteDeFrais/1'
```

exemple de response:

``
{
"id": 1,
"dateDeLaNote": "2022-12-10",
"montant": 830,
"typeDeNote": "repas",
"societe": "societe de test",
"commercial": {
"id": 1,
"nom": "nom commercial",
"prenom": "prenom commercial",
"email": "commercial1@email.com"
},
"dateDeCreation": "2023-06-13T02:34:32",
"dateDeModification": "2023-06-13T02:46:08"
}
``

### Suppression d'une note de frais

```
wget --no-check-certificate --quiet \
  --method DELETE \
  --timeout=0 \
  --header 'Authorization: Bearer mettre_le_token_d_authentification_ici' \
   'localhost:8000/api/noteDeFrais/2'
```

exemple de response:

``
"La note de frais à l'id 2 a été supprimée avec succès"
``

### Récupération de la liste des types des notes de frais:

```
wget --no-check-certificate --quiet \
  --method GET \
  --timeout=0 \
  --header 'Authorization: Bearer mettre_le_token_d_authentification_ici' \
   'http://localhost:8000/api/typeDeNote'
```

exemple de response:

``
[
{
"libellé": "essence",
"code": "ESSENCE"
},
{
"libellé": "péage",
"code": "PEAGE"
},
{
"libellé": "repas",
"code": "REPAS"
},
{
"libellé": "conférence",
"code": "CONFERENCE"
}
]
``
## Tests

Des tests unitaires et d'intégration sont mis en place pour le service NoteDeFraisService et pour les routes des controlleurs NoteDeFraisController et AuthController.

Pour que les tests fonctionnent il faut préparer l'environnement de test en suivant les étapes suivantes:

1- Créer la base de données de test:
```bash
php bin/console --env=test doctrine:database:create
```

2- Créer le schémas de la base de données de test
```bash
php bin/console --env=test doctrine:schema:create
```

3- Charger les data-fixtures dans la base de données de test
```bash
php bin/console --env=test doctrine:fixtures:load
```

Note: le chargement des data-fixtures ne doit pas se faire plus d'une seule fois au risque d'avoir des résultats de test peu fiables. Au cas où la commande a été éxécutée plus d'une fois il faudrait supprimer la base de données de test et répéter à partir de l'étape 1

L'environnement de test est prêt, les tests peuvent être lancés avec la commande:
```bash
php bin/phpunit
```

Un test spécifique peut être lancé via la commande:
```bash
php bin/phpunit --filter nom_du_test
```

## Note

Vu que c'est un projet de démonstration, tous les mots de passes/secrets sont mis dans .env et poussés sur git
