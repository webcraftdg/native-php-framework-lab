# Native PHP Framework Lab [Version anglaise](README.md)

Un projet expérimental et pédagogique dont l'objectif est d'explorer le **développement en PHP natif** et de mieux comprendre les mécanismes internes d'un framework web.

> ⚠️ Ce projet est destiné à l'apprentissage et à l'expérimentation. Il n'est **pas conçu pour être utilisé en production**.

## 🎯 Objectif du projet

Les frameworks PHP modernes comme Symfony, Laravel ou Yii fournissent de nombreuses abstractions qui facilitent considérablement le développement d'applications web.

Mais ces abstractions peuvent parfois masquer les mécanismes qui se trouvent derrière.

L'objectif de ce projet est de reconstruire certains de ces mécanismes à partir de zéro, principalement en **PHP natif**, afin de mieux comprendre leur fonctionnement.

Le but n'est pas de créer un nouveau framework PHP destiné à concurrencer les frameworks existants.

Le but est avant tout **d'expérimenter, d'apprendre, de comprendre et de progresser**.

## 🔬 Les mécanismes explorés

Le projet expérimente actuellement différents concepts que l'on retrouve habituellement dans les frameworks PHP :

* cycle de vie d'une application ;
* requêtes et réponses HTTP ;
* routage ;
* analyse et génération d'URL ;
* contrôleurs et actions ;
* vues et layouts ;
* helpers HTML ;
* gestion des assets ;
* intégration de Tailwind CSS ;
* connexion aux bases de données avec PDO ;
* Query Builder SQL ;
* requêtes préparées et paramètres PDO ;
* modèles et validation ;
* implémentation simplifiée du pattern Active Record ;
* requêtes associées aux modèles ;
* Query Provider ;
* pagination.

D'autres expérimentations pourront être ajoutées progressivement.

Par exemple :

* endpoints API et réponses JSON ;
* intégration d'Angular ;
* authentification et autorisations ;
* sessions et cookies ;
* middlewares ;
* injection de dépendances ;
* événements ;
* cache ;
* logs ;
* gestion centralisée des erreurs ;
* tests automatisés.

## 🧱 Architecture générale

Le projet explore progressivement un cycle de traitement proche de celui que l'on retrouve dans les frameworks web :

```text
Requête HTTP
     ↓
Application
     ↓
Request
     ↓
Router
     ↓
Controller
     ↓
Model / Query / PDO
     ↓
Controller
     ↓
View
     ↓
Response
     ↓
Réponse HTTP
```

L'objectif est de comprendre la responsabilité de chaque couche plutôt que de masquer leur fonctionnement derrière une abstraction complexe.

## 🗄️ Expérimentations autour des données

La couche d'accès aux données repose sur **PDO** et explore plusieurs concepts couramment proposés par les frameworks :

```text
Connection
    ↓
Query
    ↓
PdoModelQuery
    ↓
PdoModel
```

Les requêtes préparées permettent de conserver une séparation entre la structure SQL et les valeurs transmises à PDO.

Une implémentation simplifiée du principe d'**Active Record** est également expérimentée.

L'objectif n'est pas de reproduire complètement un ORM existant, mais de comprendre les mécanismes nécessaires à son fonctionnement.

## 🎨 Vues et gestion des assets

Le projet explore également la partie présentation d'un framework :

```text
View / Layout
      ↓
AssetBundle
      ↓
AssetManager
      ↓
CSS / JavaScript
      ↓
Navigateur
```

Tailwind CSS est actuellement utilisé afin d'expérimenter la compilation, la publication et l'intégration des assets dans les vues PHP.

Cette partie permet notamment d'étudier la manière dont un framework peut enregistrer puis positionner automatiquement les fichiers CSS, JavaScript, balises meta et autres ressources nécessaires à la construction d'une page HTML.

## 🧪 Un laboratoire plutôt qu'un framework terminé

Certaines implémentations sont volontairement simples.

D'autres pourront être réécrites plusieurs fois à mesure que de nouveaux mécanismes seront étudiés ou que de meilleures solutions seront découvertes.

Cela fait partie de l'objectif du projet.

Ce dépôt sert avant tout à documenter et expérimenter **pourquoi les frameworks fonctionnent de cette manière et quels problèmes leurs différentes abstractions cherchent à résoudre**.

Les concepts présents dans des frameworks existants peuvent naturellement servir d'inspiration, mais l'objectif reste de comprendre et d'implémenter les mécanismes sous-jacents.

## 🤝 Tout le monde est le bienvenu

**Toute personne intéressée par le projet est la bienvenue.**

Les suggestions, discussions, revues de code, propositions d'architecture, corrections et Pull Requests sont les bienvenues.

Si vous identifiez :

* une architecture qui pourrait être améliorée ;
* un mécanisme PHP intéressant à expérimenter ;
* un problème de sécurité ;
* une autre manière d'implémenter une fonctionnalité ;
* une erreur ou un bug ;
* une amélioration possible ;
* ou simplement une idée intéressante à explorer ;

n'hésitez pas à ouvrir une **Issue** ou à proposer une **Pull Request**.

L'objectif n'est pas nécessairement de trouver une unique « bonne » implémentation, mais également de pouvoir apprendre en confrontant différentes approches.

## 💡 Philosophie du projet

L'idée principale de ce dépôt peut se résumer simplement :

> **Comprendre ce que fait un framework en essayant d'en reconstruire nous-mêmes les mécanismes.**

Parfois, l'une des meilleures manières de comprendre une abstraction est de la retirer et d'observer ce qui se passe en dessous.

## ⚠️ Avertissement

Ce projet est un projet pédagogique et expérimental.

Il peut contenir des implémentations incomplètes, des changements importants entre deux versions ou des mécanismes qui nécessiteraient davantage de sécurité, de tests et d'optimisations avant toute utilisation réelle.

**Il ne doit pas être considéré comme un framework destiné à la production.**

## 📜 Licence

La licence du projet reste à définir.

En attendant, toute personne souhaitant participer, proposer une amélioration ou discuter d'une implémentation est invitée à ouvrir une Issue.