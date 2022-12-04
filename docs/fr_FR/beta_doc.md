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

Ces données sont soit sur un seul widget soit sur 4 différents
 
Le plugin se base sur l'accès par jeton de sécurité de Enphase, 2 possibilité de configuration:
* Génération du token Automatique:
Le plugin gère tous seul la demande de token sur le site Enphase et le renouvelle quand il en a besoin

Par contre dans certaints cas (quand propriétaire et instalalteur sont la même personnne) le token généré ne dure que 12h donc ilk sera renouvellé souvent.

Ce mode nécessite une connexion internet pour se loguer et récupérer le nouveau token

Une fois les dépendances installées;
Il faudra  rentrer les informations suivante dans la page de configuration du plugin:
    * Utilisateur Enphase
    * Mot de passe Enphase
    * Adresse IP locale de la passerelle
    * ID du site (dispponible sur l'application)
    * Numéro de série de la passerelle (disponnible sur l'application)

* Génération du token manuellement par l'utilisateur
   * Se loguer sur cette page: https://enlighten.enphaseenergy.com/

   * Se rendre sur cette page en mettant le numéro de serie de votre passerelle à la fin de l'adresse de la page:  https://enlighten.enphaseenergy.com/entrez-auth-token?serial_num=LE NUMERO DE SERIE DE VOTRE PASSERELLE 

Une fois réalisé, il faudra  rentrer les informations suivante dans la page de configuration du plugin:
    * Adresse IP locale de la passerelle
    * Token généré sur la page web

Le plugin ajoutera les équipements dont il a besoin.

L'actualisation des données est réglable avec un minimum de 10s


Attention, pensez à relancer le démon si vous changez les paramètres.

Si vous changer de mode entre 1 ou 4 widget, les équipements seront effacés
