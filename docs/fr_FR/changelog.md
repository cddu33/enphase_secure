# Changelog plugin Enphase Secure V7 version Stable

>**IMPORTANT**
>
>S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

# 23/04/2023

Gestion des problèmes de déconnexion de la passerelle à 23h: rajout d'un delais de 120s pour la reconnexion afin que ça soit transparent pour jeedom

Correction installation dépendances 

# 21/04/2023
Utilisation de la méthode officielle pour la génération du token:
- Suppression dans la configuration du numéro de site (NU)
- Simplification de la méthode de récupération du token

Suppression de la donnée estimée Injection MES dans le réseau non fiable suivant ou est situé le TOR de mesure

Documentation du code

Diverses corrections de la beta

# 20/03/2023
Gestion du triphasé avec les passerelles metered uniquement:
- Consommation
- Production

Diverses corrections de la beta

# 22/01/2023
Cron 1day => remise à 0 de la production par onduleur

Cron 15min => cumule de la production journalière

Si la puissance est nulle, on ne prend pas en compte la valeur

rajout d'une donnée calculée pour les onduleur les WH

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
