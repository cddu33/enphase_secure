# Changelog plugin Enphase Secure V7 version Beta

>**IMPORTANT**
>
>S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

# 18/02/2024
Suppression Lxml non utilisé

# 13/11/2023
ajout batterie 3t

# 31/10/2023
Test nouvelle page pour les batteries 3t iq

# 10/10/2023
Amélioration message probleme prod dans un groupe

# 09/09/2023
correction cumul groupe 2

# 06/08/2023
correction code info daemon

# 27/07/2023
Correction dépendance

# 26/07/2023
Correction Cumuls Triphasé

# 20/07/2023
Nouveaux Index pour les Cumuls

Nouvelles données pour les Cumuls

>Le zéro est réalisé sur les cumuls à minuit donc les données de cumul ne seront bonne que le lendemain. La durée entre 2 rafraichissements n'a pas d'influence sur les valeurs
> Merci @Ziz

# 17/07/2023
Correction dépendance python 3.7

# 13/07/2023
Correction import pour firmware avant  bug

# 12/07/2023
Correction dépendances

# 11/07/2023
Correction import

Passage sur Venv pour les dépendances (réinstallation automatique normalement)

# 10/07/2023
Rajout de la partie triphasé sur la nouvelle méthode de calcul cumul

Correction dépendances

Correction import pour nouveau firmware

# 08/07/2023
Choix depuis la configuration du plugin du calcul pour l'import/export cumulé

# 07/07/2023
Renommage Commande


# 27/06/2023
Corretion Tri

Possibilité de choisir les commandes créées

# 26/06/2023
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

Certaines commandes n'ont pas de nom car je ne sais pas à quoi ça correspond

# 24/06/2023
Possibilité de créé un équipement par groupement avec prod jour, alarme, puissance max et puissance

# 19/06/2023
Pas de surveillance de la production de chaque panneau si aucun groupement n'est mis sur l'équipement

# 16/06/2023
Suppression obligation arrondis et historique

# 09/06/2023
Correction formule import export

# 06/06/2023
Rajout de la possibilité de faire des groupes de panneaux

Envoie une alerte par jour à 22h pour controler la production des panneaux. Pour les essais en beta une alerte est envoyée même si il n'y a pas d'anomalie de production.

# 04/06/2023
Changement de formule import/export cumul
Correction tri


# 31/05/2023
reset cumul import export tri


# 27/05/2023
Corrections import/export avec les arrondis


# 25/05/2023
Rajout de 2 données calculées:
- Import Journalier réseau
- Export Journalier réseau (estimation)

Merci @gus69 et @Neoseb38 pour leurs recherches


# 20/05/2023

Rajout du mode autoconsommation dans la page de config

# 01/05/2023

Evite le passage à 0 du compteur Mise En Service

# 24/04/2023

Correction problème connexion passerelle suivant la version

# 23/04/2023

Gestion des problèmes de déconnexion de la passerelle à 23h: rajout d'un delais de 120s pour la reconnexion afin que ça soit transparent pour jeedom

Correction installation dépendances 

# 06/04/2023

Correction récupération de l'inventaire


# 23/03/2023
Utilisation de la méthode officielle pour la génération du token:
- Suppression dans la configuration du numéro de site (NU)
- Simplification de la méthode de récupération du token

Suppression de la donnée estimée Injection MES dans le réseau non fiable suivant ou est situé le TOR de mesure

Documentation du code


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
