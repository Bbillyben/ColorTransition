# Color Transition plugin pour Jeedom

<p align="center">
  <img width="100" src="/plugin_info/ColorTransition_icon.png">
</p>

Plugin Utilitaire qui permet de calculer des transition sur un lots de couleurs, sur les canaux RGB et/ou Alpha et/ou Blanc.
Plusieurs type de transition sont proposées, avec 1 paramètre (!) pour pousser la personnalisation.

La couleur de transition est calculée à partir d'un curseur, sur la totalité des couleurs ou entre chaque couleur.


# |Configuration|
  
  1. activer le plugin
  
  2. Configurations :  pas de configuration
  
  3. créer un premier équipement
 
  
 # |Equipement|
 <p align="center">
  <img width="100%" src="/ReadmeImg/equipement.PNG">
</p>

 

 ### Paramètres généraux      
 
 * __Nom de l'équipement__ 
 * __Objet parent__ 
 * __Catégorie__ 
 Comme tout équipement classique
 
 ### Aperçu
 Colonne de droite de la configuration de l'équipement.
 affiche un graphique représentant la fonction de transition choisie, avec l'application de différents paramètres (cf ci dessous).
 Cette prévisualisation ne prends pas en compte les canaux ``Alpha`` et ``Blanc`` si sélectionnés.
 Le graphique se met à jour selon la selection du paramètre ``Type de transition``
 Affiche également une prévisualisation de la transition sur toute la range, sans le canal alpha.
 Cette prévisualisation se met à jour quand vous enregistrez l'équipement.
 
 ### Curseur  
  * __Bornes du curseur __ : permet de spécifier les limites supérieure et inférieure entre lesquelles le curseur va varier pour calculer la couleur de rendue. Une fois défini, les valeur min et max des informations et commandes seront configurées.
     * *de 0 à 1* : le curseur sera borné entre 0 et 1, attention, décimale requise!
     *  *nombre de couleurs* : variera entre 0 et le nombre de couleurs définis dans l'onglet couleurs.(par exmple si 3 couleurs -> entre 0 et 2)
     *  *personnalisées* : fait apparaitre deux champs de saisis **min** et **max** qui vous permettrons de spécifier les bornes.


### Transition
* __Application de la transition__ : vous permet de choisir si la fonction de transition (cf ci-dessous) est appliqué sur l'ensemble des couleurs ordonnées ou entre chaque couleur.
   * *sur toute la gamme * : la fonction de transition est appliquée sur l'ensemble des couleurs
   ![transoition-all](/ReadmeImg/transition_all.PNG)     
   * *entre chaque couleur* : la fonction de transition est appliquée entre chacune des couleurs
   ![transoition-each](/ReadmeImg/transition_each.PNG)    
*exemple avec la fonction puissance, paramètre=5*

* __Type de transition__ : spécifie la fonction de transition
  *   *linéaire* : une droite de pente 1 : ``y=x`` - pas de paramètre
  *   *puissance* : ``x^[param]``, par défaut param = 2
  *   *sigmoid* : ``1/(1+exp(-[param]*(x-0.5)))``, par défaut param = 10
  *   *logit* : ``[param]*log(x/(1-x))+0.5``, par défaut param = 0.2
  *   *log* : ``[param]*ln(x)+1``, par défaut param = 0.2
  *   *exp* : ``[param]*exp(ln(1/[param])*x)``, par défaut param = 0.01
  *   *sinus* :  ``y=sin(PI/2*x)`` - pas de paramètre

*note:* la prévisualisation est mise à jour quand vous enregistez l'équipement

### Sortie Couleur
Cette configuration permet de spécifier le format de sortie de la couleur de transition calculée
* __Utiliser le canal Alpha__ : Si coché, le canal alhpa sera ajouté
* __Utiliser le canal Blanc__ : Si coché, le canal blanc sera ajouté
* __Format de la sortie__ : spécifie le format de la sortie 
  * *Hexadécimal* : format ``#AAWWRRGGBB`` ou ``#AARRGGBB`` ou ``#WWRRGGBB`` ou ``#RRGGBB``  
  * *json* : format type json : ``{"r":rr,"g":gg,"b":bb,"a":aa, "w":ww}``, avec ou sans les canaux ``a`` et ``w``, au minimal ``{"r":rr,"g":gg,"b":bb}``

 # |Commandes|
  
 Trois commandes sont crées avec l'éuiqpement : 
 * __Couleur courante__ : Info type string qui contient la couleur de transition calculée au format défini (cf ci dessus)
 * __Curseur__ : Info type numeric qui contient la valeur du curseur. Les bornes min max sont renseignée à partir du paramètre *Bornes du curseur*
* __Set Curseur__ : Action type slider qui permet de définir la valeur de *Curseur* entre les bornes spécifiées

Vous pouvez utiliser une commande type ``event`` dans un scénario pour définir la valeur de ``curseur``.

# |Couleurs|
  
   ![couleur-onglet](/ReadmeImg/couleurs.PNG) 
   
   ici sont définis les couleurs de la transition.
   Vous pouvez ordonner les couleurs par glissé-déposé
   Vous pouvez ajouter autant de couleurs que vous souhaitez !
   Quatre paramètres sont disponible pour les couleurs :
   * __Nom__ : un nom unique que vous choississez
   * __couleur__ : la valeur de la couleur. la fenêtre de selection est dépendante du navigateur et du système. En général avec une roue ou un panel, les valeurs TSL et RGB
   * __Alpha__ : une valeur pour le canal alpha, défini par un curseur entre 0 et 255
   * __Blanc__ : une valeur pour le canal Blanc, défini par un curseur entre 0 et 255

*note* : les colonnes Alpha et Blanc sont affichées ou masquées selon la configuration de l'équipement. 
  
  
# |Widget|
  ![couleur-onglet](/ReadmeImg/widget.PNG) 
  
  Le widget par défaut du pllugin affiche la valeur de la couleur de transition dans le format configuré, avec une bulle représentant cette couleur (sans les canaux alpha et blanc).
 Le slider affiché pour la commande ``Set Curseur`` est dérivé du slider du core, avec un pas de 1/1000 de la range configurée par le curseur.
