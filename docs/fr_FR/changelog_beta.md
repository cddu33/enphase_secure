# Changelog plugin Enphase Secure V7 BETA

>**IMPORTANT**
>
>S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte

# 18/01/2022
correction réinitialisation des unités au démarrage du démon


# 14/01/2023
Correction blocage démon lors d'un problème de connexion

Correction création des commandes onduleurs

Suppression des logs suivant le mode utilisé (user, password...)

# 07/01/2023
Création d'un équipement par onduleur au lancement du démon

Récupération de la puissance par onduleur

Récupération de la puissance max par onduleur

# 10/12/2022
Possibilité de passer le delais à 1s mais sans garantie de résultat, le minimum recommandé est de 10s

Si aucun délais d'actualisation n'est rentré, 60s par défaut

# 05/12/2022
Amelioration dépendances

# 04/12/2022
Rajout de la possibilité de désactiver le renouvellement du token et de le gérer depuis le site enphase (token de 1 ans)

Correction erreur sur renouvellement token

Amelioration de la documentation

# 14/11/2022
Correction création multiple stockage widget

# 14/11/2022
Correction info batteries log

Amélioration des logs en erreur

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
