# Plugin Enphase Secure V7 pour Jeedom

> Vérifiez bien que votre passerelle Enphase est en Firmware V7
> 
> Pour cela:
> * Ouvrir l'application "Toolkit"
> * Onglet Système
> * Sélection de votre Site
> * Clic sur 2 Appareils & Calepinage
> * Clic sur Iq Gateway
> * Version: V7. au minimum

Le plugin permet de récupérer les données: 
* de production mono ou tri:
    * Instantanné
    * Jour
    * Semaine
    * Mise en service
    * Cumul Journalier Export réseau
    * Puissance Réactive
    * Puissance Apaprente
    * Facteur de puissance
    * Courant
    * Energie Réactive inductive jour
    * Energie Réactive inductive Mes
    * Energie Réactive capacitive jour
    * Energie Réactive capacitive Mes
    * Energie Apparente jour
    * Energie Apparente Mes
* de consommation totale mono ou tri (avec la production):
    * Instantanné
    * Jour
    * Semaine
    * Mise en service
    * Puissance Réactive
    * Puissance Apparente
    * Facteur de puissance
    * Courant
    * Energie Réactive inductive jour
    * Energie Réactive inductive Mes
    * Energie Réactive capacitive jour
    * Energie Réactive capacitive Mes
    * Energie Apparente jour
    * Energie Apparente Mes
* de consommation net mono ou tri (importée du réseau):
    * Instantanné
    * Jour
    * Semaine
    * Mise en service
    * Cumul journalier Import réseau (non fonctionnel avec le dernier firmware, bug enphase)
    * Puissance Réactive
    * Puissance Apaprente
    * Facteur de puissance
    * Courant
    * Energie Réactive inductive jour
    * Energie Réactive inductive Mes
    * Energie Réactive capacitive jour
    * Energie Réactive capacitive Mes
    * Energie Apparente jour
    * Energie Apparente Mes
* la balance import et export instantanée réseau mono ou tri
* le stockage
    * La puissance délivrée
    * Le pourcentage de charge restant
* les onduleurs
    * La puissance délivrée par onduleur
    * Le puissance délivrée max par onduleur
    * La production cumulée sur la journée (estimation)
    * Le groupe d'appartenance (orientation ou puissance du panneau) pour la surveillance de production
* Groupe d'onduleurs (si configuré)
    * La puissance délivrée dans le groupe
    * Le puissance délivrée max dans le groupe
    * La production cumulée sur la journée (estimation) dans le groupe
    * La surveillance de production


Pour maximiser l'autoconsommation, posssibilité de régler un seuil haut et un seuil bas pour déclencher une commande

L'actualisation des données est réglable avec un minimum de 1s sauf pour les onduleurs qui eux s'actualise toutes les 15min environ (non modifiable)

La production des panneaux est surveillé une fois par jour. La formule de calcul est: l'addition des panneaux d'un groupe diviser par le nombre de panneau . Ce calcul est minoré de 10/100. Si la production journalière d'un panneau est inférieur à ce calcul, il est listé dans un rapport journalier dans le centre de message.

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
   * Numéro de série de la passerelle (disponible sur l'application)
* Mode Token Manuel:
   * Adresse IP locale de la passerelle
   * Token généré précédement depuis le site Enphase

* Racordement de l'installation:
   * Mode Monophasé
   * Mode triphasé

* Equipement par Groupement:
Si coché, cré un équipement par groupement pour centraliser les informations

* Type de Firmware de la passerelle:
Adapte le plugin au firmware de la passerelle. vous le retrouver sur l'application Enlighten, Onglet Menu, Système, Périphérique, Passerelle

Les groupements sont à renseigner dans chaque équipement onduleur
>Attention, pensez à relancer le démon si vous changez ce paramètre.

* Commandes par équipements:
Cocher les commandes ( sur la page configuration du plugin) que vous voulez que le plugin rajoute

* Autoconsommation:
   * Mode de fonctionnement: active ou non l'autoconsommation
   * Surplus déclenchement seuil 1: valeur en Watt au dessus de laquelle on déclenche l'autoconsommation
   * Commande déclenchement seuil 1: commande déclenchée au dessus du seuil 1
   * Surplus arrêt seuil 1 : valeur en Watt en dessous de laquelle on déclenche la commande d'arrêt de l'autoconsommation
   * Commande arrêt seuil 1: commande déclenchée lors de l'arrêt de l'autoconsommation

Le plugin ajoutera les équipements dont il a besoin.

>Attention, pensez à relancer le démon si vous changez les paramètres.
