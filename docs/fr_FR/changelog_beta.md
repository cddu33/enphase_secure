# Changelog plugin Enphase Secure V7 BETA

>**IMPORTANT**
>
>S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

# 12/11/2022 
Rajout gestion batterie

# 11/11/2022 
Stable = Beta

# 01/11/2022 Grosses modifications du plugin
Création d'un démon à la place du cron; cela permet:
* une actualisation des données modifiable en dessous de la minute (réglée de base sur 10s) 
* d'intéroger la passerelle en mode cloudless tant que le jeton n'est pas expiré

Possibilité de divier les informations en 3 widget (depuis la configuration du plugin)

Les paramètres de la passerelle sont maintenant dans la page de configuration du plugin

# 22/10/2022
Compatibilité Jeedom 4.3 (passage en watts et affichage instantané dans le configurateur)

Compatibilité Jeedom 4.0

Correction Wh et W

Rajout des: infos conso net // conso total // export et import réseau

Rajout d'un début de widget accessible sur validation dans l'équipement

# 04/10/2022
Intégration Passerelles Standard

# 03/10/2022
Changement du fichier source pour /production.json?details=1

Rajout tension réseau

# 02/10/2022
Fonctionnement OK pour les informations de production et de consommation sur passerelle Metered

Correction cron

# 01/10/2022
Initial
