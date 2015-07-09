# Twedis (Twitter avec Redis)

----------

Le but du TP était de coder un équivalent de Twitter (seulement certaines fonctionnalités) à l'aide de Redis comme base de données.

L'utilisateur final devait pouvoir :

 - Afficher ses tweets avec une pagination
 - Afficher les derniers tweets des personnes qu'il suit
 - Afficher les personnes qu'il suit
 - Afficher les personnes qui le suivent
 - Afficher le nombre de tweet / followers / following
 - Écrire un tweet
 - Récupérer tout les tweets possédant un hashtag

Nous développerons le back-end à l'aide d'un framework **PHP** personnel, et le front-end avec **AngularJS**.

## 1. Étude de la "structure" de la base de données

Notre premier axe de recherche a été d’appréhender Redis, n'ayant jamais travaillé avec du NoSQL avant.

Le NoSQL a pour avantage d'être "schema-less", c'est à dire que le schéma de la base est "dynamique", il peut être changé à tout moment.

Redis possède seulement quelques types de données stockable :

> - String : Une simple chaine de caractère :
> `"abcde"`
> - Hash : Un tableau associatif de clé-valeur :
 `['clé' => 'valeur', 'key' => 'value']`
> - Set : Un tableau de valeurs sans clés :
 `['valeur', 'value']`
> - List : Un tableau de valeurs triées par ordre d'insertion :
> `[1 => 'valeur', 2 => 'value']`
> - Sorted Set : Un tableau de valeurs associé à un score (le score pouvant être répété, le membre lui ne peut pas)
> `[283 => 'valeur', 94 => 'value', 283 => 'example']`

Nous avons donc du modéliser notre base de données seulement avec ces différents type de données.

>**Note :** Les différents types de données ne peuvent pas être intégrées dans les autres types.

>**Exemple :** Il est impossible d'intégrer un set comme valeur d'un hash.

### 1.1. Structure de la base Redis de Twedis

> **Note :**
>  - Notre "base" Redis est préfixé par "twedis".
> - Les chaines de caractères précédés d'un **$** sont considéré comme des variables.

----------

#### Utilisateur :

> **(Hash)** - Représentation d'un utilisateur

    user:$uuid :
    [
        'uuid' => '110e8400-e29b-11d4-a716-446655440000',
        'username' => 'antoine',
        'password' => 'antoine1234'
        'tweets' => 25
    ]

> L'UUID de l'utilisateur est placé dans "l'objet" user pour simplifier l'usage de celui-ci en backend, lors par exemple de l'écriture d'un tweet.

> Nous avons choisi de compter les tweets avec un entier incrémental plutôt que de compter les tweets dans user:$uuid:tweets, car cela reste plus rapide.

> Nous n'avons pas fait la même chose pour les followers et les following car il est plus probable que les utilisateurs aient un nombre de tweets plus important que de followers/following. De plus, pour déterminer si nous suivons un utilisateur ou s'il nous suit, nous avons fait le choix de récupérer tout les followers/following pour faire le test coté client, nous pouvions donc les compter directement coté client.

----------

> **(Hash)** - Liens des noms d'utilisateur et de leur UUID

    users :
    [
        'antoine' => '110e8400-e29b-11d4-a716-446655440000',
        'thomas' => '8c651c1a-3e42-4e15-9dbe-f3a76e796f27'
    ]

> Cette "table" permet de faire le lien entre un nom d'utilisateur et son UUID pour obtenir toutes ses informations. Elle est utile notamment lors du login ou lors de la recherche des tweets d'un utilisateur (qui est faite via le nom d'utilisateur).

----------

> **(Sorted Set)** - Followers d'un utilisateur

    user:$uuid:followers :
    [
        'thomas',
        'jacques'
    ]

> Nous avons choisi un **Sorted Set** avec comme score le timestamp auquel le followers à décidé de suivre l'utilisateur. Même si nous n'en avons pas eu besoin lors du projet, cette option peut toujours s'avérer utile plus tard.
> 
> Nous avons préférés les noms d'utilisateurs plutôt que les UUID car lors de l'affichage des followers, nous avons seulement besoin des noms d'utilisateurs et il nous est possible de retrouver au besoin un utilisateur par son nom avec la "table" **users**.

----------

> **(Sorted Set)** - Following d'un utilisateur

    user:$uuid:following :
    [
        'rémi',
        'vincent'
    ]

> **Note :** Le fonctionnement étant le même que pour les followers, vous pouvez vous référer aux notes sur les followers plus haut.

----------
#### Tweets :

> **(Hash)** - Tweet

    tweet:$uuid :
    [
        'author' => 'thomas',
        'time' => 1429717477,
        'message' => 'Bonjour, ceci est un exemple de tweet ! #ceProjetVautUn20sur20'
    ]

> Un tweet contient le nom d'utilisateur de son auteur, la date et l'heure à laquelle l'utilisateur a posté (en timestamp) ainsi que le contenu du message.

> Nous avons choisis d'inclure le nom d'utilisateur et non l'UUID car, lors de l'affichage nous avons seulement besoin du nom d'utilisateur.

----------

> **(Sorted Set)** - Tweets d'un utilisateur

    user:$uuid:tweets :
    [
        '3e12a66c-bb28-467e-ba80-029555172855',
        'e828896b-6ddb-441d-abdf-c9e6e2af1074'
    ]

> Nous plaçons ici les UUID des tweets d'un utilisateur identifié par son UUID.

----------

> **(Sorted Set)** - Hashtag

    hashtag:$hashtag :
    [
        '062eb67f-088f-4e26-9a13-4666d5a8ec20',
        '7375ad22-41ae-4c8f-be9b-4cc3170680b4'
    ]

> Nous stockons ici les UUID des tweets par hashtag. Le hashtag devant être vérifié par la Regex **[a-zA-Z0-9]+**, la clé ne peux être qu'une chaine de caractère valide pour Redis.

----------

## 2. Structure Back-End

Nous avons choisi de développer le back-end en PHP avec un "framework" personnel.

> **Note :** Celui-ci est encore en phase de développement, certaines implémentations sont amenées à changer.

Il est basé sur une architecture MVC simple :

    app/
        config/
            -- Configuration de l'application
        Controllers/
           -- Les différents Controllers
            API/
                Auth.php
                Template.php
                Tweets.php
                User.php
        Core/
            -- Coeur du Framework
        Middlewares/
            -- Traitement avant les requêtes
            Auth/
                Logged.php
        Models/
            -- Les différents Modèles de données
            Tweet.php
            Tweets.php
            User.php
        routes/
            -- Les routes de l'application
            api/
                auth.php
                tweets.php
                user.php


Il implémente différents projet disponible sur Github :

 - [AltoRouter](https://github.com/dannyvankooten/AltoRouter) - Routing
 - [Twig](https://github.com/twigphp/Twig) - Templates
 - [Eloquent ORM](http://laravel.com/docs/4.2/eloquent) - ORM
 - [Assetic](https://github.com/kriswallsmith/assetic) - Assets

> **Note :** Il inclus aussi AngularJS pour la partie front-end, cf. partie suivante.

### 2.1. Middleware :
Un middleware permet d’effectuer un traitement avant l’exécution de la méthode du Controller associé à une route.

Elle se définit dans un Controller pour toutes les méthodes ou seulement pour certaines.

----------

> **Auth/Logged.php** - Permet de vérifier si un utilisateur est connecté ou non.

> Si un utilisateur est connecté, le middleware ira créer un objet "User" et l'ajoutera en variable "globale" de l'application.
> 
> Sinon il retournera un code d'erreur **401** indiquant qu'il faut être connecté pour effectuer le traitement associé (la méthode du Controller associé ne seras pas exécutée).

### 2.2. Routes :

> **Note :** Les routes seront présentées sous cette forme :

    METHOD - URI - NAMESPACE\CONTROLLER@Method()

----------

> **api/auth.php** - Gestion de l'authentification et de l'enregistrement des utilisateurs.

    POST - /api/register - API\Auth@register()
> Enregistrement d'un utilisateur.
> Les paramètres nécessaire sont :
> 
>  - username
>  - password
>  - confirm (confirmation du mot de passe)

    POST - /api/login - API\Auth@login()
> Connexion d'un utilisateur
> Les paramètres nécessaire sont :
> 
> - username
> - password

-----------

> **api/tweets.php** - Envoi et récupération de tweets.

    POST - /api/tweet - API\Tweets@tweet()
> Envoi d'un tweet par un utilisateur
> Les paramètres nécessaire sont :
> 
> - tweet (contenu du message à poster)

    GET - /api/tweets/user/$username/$page - API\Tweets@getByUsername()
> Récupération des tweets d'un utilisateur par son nom d'utilisateur et par page

    GET - /api/tweets/hashtag/$hashtag/$page - API\Tweets@getByHashtag()
> Récupération des tweets par un hashtag et par page

    GET - /api/tweets/timeline/$page - API\Tweets@getTimeline()
> Récupération des tweets des following de l'utilisateur actuellement connecté (ainsi que ses propres tweets)

-----------

> **api/user.php** - Traitement lié à l'utilisateur connecté.

    POST - /api/user/$username/toggleFollow/$bool - API\User@toggleFollow()
> Ajoute ou supprime un utilisateur par son nom d'utilisateur de la liste des following de l'utilisateur connecté en fonction de la valeur de bool.

    GET - /api/user/$username - API\User@getByUsername()
> Récupération des informations de l'utilisateur définis par la variable username.

    GET - /api/user - API\User@getCurrentUser()
> Récupération des informations de l'utilisateur connecté.

### 2.3. Modèles de données :

Les Modèles de données sont là pour modéliser et simplifier l'usage d'Objet utilisé régulièrement.

----------

> **User** - Modélise un Utilisateur

static::**getByToken()** - Renvoi l'utilisateur lié au token

static::**getByUUID()** - Renvoi l'utilisateur lié à l'UUID

static::**getByUsername()** - Renvoi l'utilisateur lié au nom d'utilisateur

static::**register()** - Enregistre un utilisateur

static::**getByLogin()** - Renvoi un utilisateur si les credentials sont correct

static::**crypt()** - Crypte un mot de passe *(sha1 + salt)*

**getToken()** - Récupère le token de l'utilisateur

**getFollowers()** - Renvoi tout les followers de l'utilisateur

**getFollowing()** - Renvoi tout les following de l'utilisateur

**toggleFollow()** - Ajoute ou supprime un utilisateur de la liste des following

**getTimeline()** - Renvoi la timeline de l'utilisateur (tweets des following de l'utilisateur)

**getTweets()** - Renvoi les tweets de l'utilisateur

**tweet()** - Envoi un tweet

**static::getUUIDByUsername()** - Renvoi l'UUID d'un username

**static::getDataByUUID()** - Renvoi les données de l'utilisateur pour la construction par son UUID

**static::checkUsername()** - Vérifier si un nom d'utilisateur est enregistré

----------

> Tweet - Modélise un Tweet

static::**tweet()** - Sauvegarde un tweet d'un utilisateur

static::**getByUUID** - Renvoi un tweet depuis son UUID

---------

> Tweets - Modélise une collection de Tweets

static::**getByHashtag** - Récupère les tweets contenant un hashtag

### 2.4. Controllers :
Toutes les méthodes des Controllers renvoi du JSON puisque que nous utiliserons un front-end développé en AngularJS.

> **Auth.php** - Authentification

    login

>   Vérifie les credentials envoyés, renvoi un token en cas de succès.

    register

> Enregistre un utilisateur avec les données envoyés.

----------

> **User.php** - Gestion des utilisateurs

    getByUsername

> Renvoi les informations d'un utilisateur. Utile quand nous voulons afficher un profil.

    getCurrentUser

> Renvoi les informations de l'utilisateur connecté. Permet d'afficher le profil de l'utilisateur.

    toggleFollow

> Ajoute ou supprime un utilisateur des following.

----------

> **Tweets.php** - Gestion des Tweets

    getTimeline

> Renvoi la timeline de l'utilisateur connecté.

    getByUsername

> Renvoi les tweets de l'utilisateur. Utilisé quand on veux afficher le profil d'un utilisateur avec ses tweets.

    getByHashtag

> Renvoi les tweets contenant un hashtag.

    tweet

> Permet à un utilisateur d'envoyer un tweet.

## 3. Structure Front-End

La partie front-end étant secondaire par rapport au sujet du travail et de la matière, nous nous cantonnerons à une description de l'architecture AngularJS.


    app/
        assets/
            js/
                components/
                    -- Les composants unique
                    followers/
                        followersController.js
                    following/
                        followingController.js
                    hashtags/
                        hashtagsController.js
                    home/
                        homeController.js
                    login/
	                loginController.js
	            register/
	                registerController.js
	            tweets/
	                tweetsController.js
                shared/
                    -- Les composants partagés
                    alert/
                        alertController.js
                        alertService.js
                    auth/
                        authController.js
                        authService.js
                    filters/
                        escapeFilter.js
                    tweet/
                        messageDirective.js
                        tweetDirective.js
                        tweetFactory.js
                        tweetService.js
                    user/
                        userController.js
                        userFactory.js
                        userService.js
                    middlewareService.js
                app.config.js
                app.modules.js
                app.routes.js