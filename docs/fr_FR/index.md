# Plugin Enphase Secure V7 pour Jeedom

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
* Le stockage
    * La puissance délivrée
    * Le pourcentage de charge restant

L'actualisation des données est réglable avec un minimum de 10s.

L'accé se fait soit avec les identifiants enphase soit avec un token généré manuellement.

# Configuration

**Equipements**

2 possibilités:
* Equipement Unique Enphase: 
   * Toutes les données sont regroupées sur un seul équipement et donc un seul widget
* 4 équipements/widgets différents Enphase:
   * Production
   * Consommation Net (si Passerelle Metered)
   * Concommation Totale (si Passerelle Metered)
   * Stockage (si présence batteries Enphase)
   
> Pensez à relancer le démon si vous changer les paramètres.
>
> Si vous changer de mode, les équipements seront effacés.

**Mode de récupération du Token**

Le plugin permet de gérer le Token d'identification de 2 manières différentes:
* Automatique: Il s'occupe automatiquement de la demande initiale et du renouvellemen
   > Ce mode demande une connexion internet pour le renouvellement du token (entre 12h et 1 an suivant la configuration de votre compte Enphase)

* Manuel: c'est à vous de founir le token au plugin en le générant depuis le site enphase
   * Se loguer sur cette page: https://enlighten.enphaseenergy.com/
   * Se rendre sur cette page en mettant le numéro de serie de votre passerelle à la fin de l'adresse de la page:  https://enlighten.enphaseenergy.com/entrez-auth-token?serial_num=<LE NUMERO DE SERIE DE VOTRE PASSERELLE> 
   * Copier le token inclus après: "token":"
   > Ce mode permet de s'affranchir de connexion internet et génère un token d'une durée de 1 an.

**Paramètres à configurer**

Une fois les dépendances installées;
Il faudra  rentrer les informations suivante dans la page de configuration du plugin:
* Mode Token Auto:
   * Utilisateur Enphase
   * Mot de passe Enphase
   * Adresse IP locale de la passerelle
   * ID du site (dispponible sur l'application)
   * Numéro de série de la passerelle (disponnible sur l'application)
* Mode Token Manuel:
   * Adresse IP locale de la passerelle
   * Token généré précédement depuis le site Enphase

Le plugin ajoutera les équipements dont il a besoin.

>Attention, pensez à relancer le démon si vous changer les paramètres.
