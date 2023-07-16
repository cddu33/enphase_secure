# Changelog plugin Enphase Secure V7 version Stable

>**IMPORTANT**
>
>S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

# 16/07/2023
Firmware D.7.6.175 : correction cumul import et export (mono et tri)

Passage sur Venv pour les dépendances (réinstallation automatique normalement)


# 08/07/2023
Choix depuis la configuration du plugin du calcul pour l'import/export cumulé


# 07/07/2023
Renommage Commande

Possibilité de choisir les commandes créées

Simplification du code pour la création des commandes

@Piug Correction sur la création des équipements des groupes 

Rajouts des informations suivantes pour la production et la consommation:
- Puissance Réactive
- Puissance Apparente
- Facteur de puissance
- Courant
- Energie Réactive inductive jour
- Energie Réactive inductive Mes
- Energie Réactive capacitive jour
- Energie Réactive capacitive Mes
- Energie Apparente jour
- Energie Apparente Mes


# 24/06/2023
Possibilité de créé un équipement par groupement avec prod jour, alarme, puissance max et puissance



# 19/06/2023
Pas de surveillance de la production de chaque panneau si aucun groupement n'est mis sur l'équipement

# 16/06/2023
Formule import export stabilisée
Suppression obligation arrondis et historique

# 09/06/2023
Correction formule import export

Rajout de la possibilité de faire des groupes de panneaux

Envoie une alerte par jour à 22h pour controler la production des panneaux si anomalie


# 06/06/2023
Rajout de 2 données calculées:
- Import Journalier réseau (estimation)
- Export Journalier réseau (estimation)

Merci @gus69, @Neoseb38 et @bison pour leurs recherches

# 24/04/2023

Correction problème connexion passerelle suivant la version

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
