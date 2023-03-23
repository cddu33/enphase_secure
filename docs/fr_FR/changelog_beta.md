# Changelog plugin Enphase Secure V7 version Beta

>**IMPORTANT**
>
>S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

# 23/03/2023

Utilisation de la méthode officielle pour la génération du token

Suppression de la donnée estimée Injection MES dans le réseau non fiable

# 20/03/2023

Ajout de la donnée estimée Injection MES dans le réseau

# 14/02/2023

correction triphase phase 3


# 12/02/2023
Gestion du triphasé avec les passerelle metered uniquement:
- Consommation
- Production

# 10/02/2023
Correction compatibilité 4.4

# 24/01/2023
Crorrection Cron

# 22/01/2023
Cron 1day => remise à 0 de la production par onduleur

Cron 15min => cumule de la production journalière

Si la puissance est nulle, on ne prend pas en compte la valeur

# 21/01/2023
rajout d'une donnée calculée pour les onduleur les WH

=> Attention, cette valeur est a tester car le calcul est actuellement: "puissance * 0.25" (car un relevé toutes les 15min)
A voir ce que ça donne dans le long terme

# 19/01/2023
Correction réinitialisation unité au démarrage du démon

Correction rafraîchissement données passerelle standard

# 14/01/2023
Correction blocage démon lors d'un problème de connexion

Correction création des commandes onduleurs

Suppression des logs suivant le mode utilisé (user, password...)

# 11/01/2023
Création d'un équipement par onduleur au lancement du démon

Récupération de la puissance par onduleur

Récupération de la puissance max par onduleur


# 21/12/2022
Première version Stable 

Possibilité de passer le delais à 1s mais sans garantie de résultat, le minimum recommandé est de 10s

Si aucun délais d'actualisation n'est rentré, 60s par défaut

Rajout de la possibilité de désactiver le renouvellement du token et de le gérer depuis le site enphase (token de 1 ans)

Correction erreur sur renouvellement token

Gestion Batterie
