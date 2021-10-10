# Color Transition plugin pour Jeedom

<p align="center">
  <img width="100" src="/plugin_info/ColorTransition_icon.png">
</p>

Plugin Utility that allows to calculate transitions on a batch of colours, on the RGB and/or Alpha and/or White channels.
Several types of transition are proposed, with 1 parameter (!) to push the customization.

The transition colour is calculated from a cursor, on the totality of the colours or between each colour.


# |Configuration|
  
  1. activate the plugin
  
  2. Configurations: no configuration
  
  3. Create a first equipment
 
  
 # |Equipement|
 <p align="center">
  <img width="100%" src="/ReadmeImg/equipement.PNG">
</p>

 

 ### General parameters      
 
 * __Name of the equipment__ 
 * __Parent object__
 * __Category__ 
 As with all conventional equipment
 
 ### Aperçu
 Right-hand column of the equipment configuration.
 displays a graphical representation of the chosen transition function, with the application of different parameters (see below).
 This preview does not take into account the Alpha and White channels if selected.
 The graph updates according to the selection of the "Transition Type" parameter.
 Also displays a preview of the transition over the entire range, without the alpha channel.
 This preview updates when you save the equipment.
 
 ### Curseur  
  * __Cursor terminals __ : allows you to specify the upper and lower limits between which the cursor will vary to calculate the rendering colour. Once set, the min and max values of the information and commands will be configured.
     * *from 0 to 1*: the cursor will be limited between 0 and 1, attention, decimal required!
     * *number of colours*: will vary between 0 and the number of colours defined in the colours tab (e.g. if 3 colours -> between 0 and 2)
     * *customised* : two input fields **min** and **max** will appear, allowing you to specify the limits.

### Transition
* __Transition application__ : allows you to choose if the transition function (see below) is applied on all the ordered colours or between each colour.
   * *On the whole range * : the transition function is applied on all the colours
   ![transoition-all](/ReadmeImg/transition_all.PNG)     
   * *between each colour*: the transition function is applied between each colour
   ![transoition-each](/ReadmeImg/transition_each.PNG)    
*exemple avec la fonction puissance, paramètre=5*

* Type of transition__ : specifies the transition function
  * *linear*: a line with slope 1: ``y=x`` - no parameter
  * *power*: ``x^[param]``, default param = 2
  * *sigmoid*: ``1/(1+exp(-[param]*(x-0.5)))``, default param = 10
  * *logit*: ``[param]*log(x/(1-x))+0.5``, default param = 0.2
  * *log* : ``[param]*ln(x)+1``, default param = 0.2
  * *exp* : ``[param]*exp(ln(1/[param])*x)``, default param = 0.01
  * *sinus*: ``y=sin(PI/2*x)`` - no parameter

*note:* the preview is updated when you register the equipment


### Sortie Couleur
This setting allows you to specify the output format of the calculated transition colour
* __Use Alpha Channel__ : If checked, the alhpa channel will be added
* __Use White Channel__ : If checked, the white channel will be added
* __Output format__ : specify the output format 
  * *Hexadecimal* : format ``#AAWWRRGGBB`` or ``#AARRGGBB`` or ``#WWRRGGBB``` or ``#RRGGBB``` *json* : format type ``AAWRRGGBB`` or ``#AARRGGBB`` or ``#WRRGGBB`` or ``#RRGGBB``.  
  * *json*: json type format: ``{"r":rr, "g":gg, "b":bb, "a":aa, "w":ww}``, with or without ``a`` and ``w`` channels, at least ``{"r":rr, "g":gg, "b":bb}``

 # |Commands|
  
 Three commands are created with the tool: 
 * __Current colour__ : Info type string which contains the transition colour calculated in the defined format (see above)
 * __Cursor__ : Numeric type info which contains the value of the cursor. The min and max bounds are set from the parameter *Cursor bounds*.
* __Set Cursor__: Action type slider which allows to define the value of *Cursor* between the specified bounds

You can use an ``event'' type command in a scenario to set the ``cursor'' value.

# |Colors|
  
   ![couleur-onglet](/ReadmeImg/couleurs.PNG) 
   
   Here the colours of the transition are defined.
   You can order the colours by drag and drop
   You can add as many colours as you want!
   Four parameters are available for the colours:
   * __Name__ : a unique name that you choose
   * __Colors__ : Color Value, The selection window is browser and system dependent. In general with a wheel or a panel, the TSL and RGB values
   * __Alpha__ : a value for the alpha channel, defined by a slider between 0 and 255
   * __White__ : a value for the White channel, defined by a slider between 0 and 255

*Note*: The Alpha and White columns are displayed or hidden depending on the equipment configuration. 
  
  
# |Widget|
  ![couleur-onglet](/ReadmeImg/widget.PNG) 
  
 The default plugin widget displays the value of the transition colour in the configured format, with a bubble representing that colour (without the alpha and white channels).
 The slider displayed for the Set Cursor command is derived from the core slider, with a step size of 1/1000 of the range configured by the cursor.
