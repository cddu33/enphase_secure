# Plugin Enphase Secure V7 Beta pour Jeedom

Le plugin permet de récupérer les données: 
* de production:
    * Instantanné
    * Jour
    * Semaine
    * Mise en service
* de consommation totale (avec la production):
    * Instantanné
    * Jour
    * Semaine
    * Mise en service
* de consommation net (importée du réseau):
    * Instantanné
    * Jour
    * Semaine
    * Mise en service
* La balance import et export instantanée réseau
 
Il se base sur l'accès par jeton de sécurité de Enphase, donc il nécessite une connexion internet pour se loguer mais récupère les donnnées toutes les 10s (modifiables) directement depuis la passerelle jusqu'à expiration du jeton.

Une fois les dépandances installées;
Il faudra commencer par créer un équipement puis lui rentrer les informations suivante:
* Utilisateur Enphase
* Mot de passe Enphase
* Adresse IP locale de la passerelle
* ID du site (dispponible sur l'application)
* Numéro de série de la passerelle (disponnible sur l'application)


Attention, pensez à relancer le démon si vous changer les paramètres dans l'équipement

Inspiré de https://github.com/dlmcpaul/EnphaseCollector
