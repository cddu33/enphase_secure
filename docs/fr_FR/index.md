# Plugin FordCar pour Jeedom

Ce plugin permet de récuperer les informations de tous les véhicule possédant fordpass
Il s'appuie sur l'API fordcar.

Paramétrage: 
* Activer le Plugin
* Lancer l'installation des dépandances (normalemnt elles se lancent automatiquement)
* Créer un équipement
* Renseigner l'adresse, le nom d'udilisateur et le mot de passe (identique à l'application mobile)
* Renseigner le VIN du véhicule
* Sauvegarder

>Limite de l'API:
>
>Suivant l'état de la voiture, une commande peut mettre plusieurs dizaines de secondes à être exécutée.

Les boutons (de gauche à droite):
- Verrouiller le véhicule
- Déverrouiller le véhicule
- Forcer la synchronisation des données (équivaut à un refresh depuis l'application Ford)
- Démarrer le moteur (fonctionnel suivant les modèles)
- Couper le moteur (fonctionnel suivant les modèles)

>
>Toutes les informations remontants de l'API sont disponible dans le fichier Data/vin.json du plugin.

>
>Toutes les informations interprétées par le plugin sont disponible dans les log en mode debug.
