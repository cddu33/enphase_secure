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
 
Il se base sur l'accès par jeton de sécurité de Enphase.

Il necessite une connexion internet pour se loguer mais récupère les donnnées directement depuis la passerelle ensuite jusqu'à expiration du jeton

Il faudra donc rentrer utilisateur et mot de passe pour se connecter au site Enphase, le numéro de site ainsi que le numéro de série de la passerelle

L'actualisation des données se fait au minimum à 10s
