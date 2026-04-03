Documentation — REVIEWEO


1. Présentation du projet

REVIEWEO est une application web permettant aux utilisateurs de publier, consulter et interagir avec des critiques sur différents types de contenus :

Films
Jeux vidéo
Livres
Séries
Musique

L’application repose sur une architecture classique :

Frontend (interface utilisateur)
Backend (logique métier)
Base de données (stockage)


2. Architecture générale

🔹 Frontend
HTML : structure des pages
CSS / Bootstrap : mise en forme et responsive
JavaScript : interactions dynamiques (AJAX)

🔹 Backend
PHP (POO)
Gestion des sessions
Authentification des utilisateurs
Traitement des requêtes CRUD

🔹 Base de données
MySQL
Gestion des relations entre entités


3. Gestion des utilisateurs

Authentification
Inscription avec email + mot de passe
Connexion avec vérification
Sessions PHP pour maintenir l’état connecté

Rôles
Rôle	Permissions
Utilisateur	Lire, liker
Critique	création/modification/suppresion de critiques
Administrateur	Gestion globale


4. Structure de la base de données

Table user
Champ	Type	Description
id	int	Identifiant unique
pseudo	varchar	Nom utilisateur
email	varchar	Email
password	varchar	Mot de passe hashé
role	enum	utilisateur / critique / administrateur

Table critique
Champ	Type	Description
id_critique	int	ID critique
titre	varchar	Titre
contenu	text	Contenu
date_creation	date	Date
id_user	int	Auteur
note	int	Note (/10)
epingle	bool	Critique mise en avant
id_categorie	int	Catégorie

Table categorie
Champ	Type	Description
id	int	ID catégorie
nom	varchar	Nom

Table like
Champ	Type	Description
id_user	int	Utilisateur
id_critique	int	Critique


5. Relations entre les tables

Un user → peut écrire plusieurs critiques
Une critique → appartient à une catégorie
Un user → peut liker plusieurs critiques
Une critique → peut recevoir plusieurs likes


6. Fonctionnalités détaillées

Authentification
Inscription sécurisée
Hashage des mots de passe (password_hash)
Vérification (password_verify)


Gestion des critiques
Création d’une critique
Modification (auteur uniquement)
Suppression (auteur ou admin)
Affichage liste + détail


Système de likes
Ajouter un like
Supprimer un like
Empêcher les doublons


Catégories
Association d’une critique à une catégorie
Filtrage possible


Épinglage
Fonction admin uniquement
Met en avant certaines critiques


7. Sécurité

Protection contre les injections SQL (requêtes préparées)
Hashage des mots de passe
Vérification des rôles
Protection des accès (sessions)


8. Logique CRUD

Exemple : Critique
Create → ajout critique
Read → affichage critiques
Update → modification
Delete → suppression


9. Déploiement

En local
Installer XAMPP / WAMP
Importer la base de données
Configurer la connexion MySQL
Lancer via localhost


10. Évolutions possibles

Recherche avancée
Commentaires
Statistiques
API REST
Responsive amélioré


11. Tests

Vérifier les rôles utilisateurs
Tester CRUD complet
Tester sécurité (login, accès pages)
Tester interactions (likes, épinglage)


12. Conclusion

REVIEWEO est un projet complet permettant de mettre en pratique :

la gestion d’une base de données relationnelle
le développement backend en PHP
la création d’interfaces dynamiques
la gestion des rôles et permissions