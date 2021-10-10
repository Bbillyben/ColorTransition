# Color Transition plugin pour Jeedom

<p align="center">
  <img width="100" src="/plugin_info/ColorTransition_icon.png">
</p>

Plugin Utilidad que permite calcular transiciones sobre un lote de colores, sobre los canales RGB y/o Alfa y/o Blanco.
Se proponen varios tipos de transición, con 1 parámetro (!) para impulsar la personalización.

El color de transición se calcula a partir de un deslizador, ya sea sobre todos los colores o entre cada color.


# |Configuración|
  
  1. activar el plugin
  
  2. Configuraciones: sin configuración
  
  3. Crear un primer equipo
 
  
 # |Equipo|
 <p align="center">
  <img width="100%" src="/ReadmeImg/equipement.PNG">
</p>

 

 ### Parámetros generales      
 
 * __Nombre del equipo__ 
 * __Objeto padre__ 
 * __Categoría__
 
 Como con todos los equipos convencionales
 
 ### Visión general
 Columna derecha de la configuración del equipo.
 muestra una representación gráfica de la función de transición elegida, con la aplicación de diferentes parámetros (véase más adelante).
 Esta previsualización no tiene en cuenta los canales Alfa y Blanco si están seleccionados.
 El gráfico se actualiza según la selección del parámetro "Tipo de transición".
 También muestra una vista previa de la transición en todo el rango, sin el canal alfa.
 Esta vista previa se actualiza cuando se guarda el equipo.
 
 ### Cursor  
  * __Límites del cursor__: Permite especificar los límites superior e inferior entre los que variará el cursor para calcular el color de renderizado. Una vez configurados, los valores mínimos y máximos de la información y los controles serán configurados.
     * *de 0 a 1* : el cursor se limitará entre 0 y 1, atención, ¡decimal requerido!
     *número de colores*: variará entre 0 y el número de colores definidos en la pestaña de colores (por ejemplo, si son 3 colores -> entre 0 y 2)
     * *personalizado* : aparecerán dos campos de entrada **mín** y **máx** que le permitirán especificar los límites.


### Transición
* Aplicación de la transición__ : permite elegir si la función de transición (ver más abajo) se aplica sobre todos los colores ordenados o entre cada color.
   *en toda la gama* : la función de transición se aplica a todos los colores
   ![transoition-all](/ReadmeImg/transition_all.PNG)     
   *entre cada color*: la función de transición se aplica entre cada color
   ![transoition-each](/ReadmeImg/transition_each.PNG)    
*Ejemplo con función de potencia, parámetro=5*


* Tipo de transición__ : especifica la función de transición
  * *Lineal*: una línea con pendiente 1: ``y=x`` - sin parámetro
  * *poder*: ``x^[param]``, por defecto param = 2
  * *sigmoide*: ``1/(1+exp(-[param]*(x-0.5)))``, por defecto param = 10
  * *logit*: ``[param]*log(x/(1-x))+0.5``, por defecto param = 0.2
  * *log* : ``[param]*ln(x)+1``, por defecto param = 0.2
  * *exp* : ``[param]*exp(ln(1/[param])*x)``, por defecto param = 0.01
  * *sinus*: ``y=sin(PI/2*x)`` - sin parámetro

*nota:* la vista previa se actualiza al registrar el equipo

### Salida de color
Este ajuste permite especificar el formato de salida del color de transición calculado
* __Usar canal alfa__ : Si se marca, se añadirá el canal alhpa
* __Usar el canal blanco__ : Si se marca, se añadirá el canal blanco
* __Formato de salida__ : especifica el formato de salida 
  * *Hexadecimal* : formato ``#AAWWRRGGBB`` o ``#AARRGGBB`` o ``#WWRRGGBB`` o ``#RRGGBB`` *json* : formato tipo ``AAWRRGGBB`` o ``#AARRGGBB`` o ``#WRRGGBB`` o ``#RGGBB``.  
  * *json*: formato tipo json: ``{"r":rr, "g":gg, "b":bb, "a":aa, "w":ww}``, con o sin canales ``a`` y ``w``, al menos ``{"r":rr, "g":gg, "b":bb}``.

 # |Commands|
  
 Se crean tres comandos con el elemento: 
 * __Color actual__ : Cadena de tipo informativo que contiene el color de transición calculado en el formato definido (véase más arriba)
 * __Cursor__ : una información de tipo numérico que contiene el valor del cursor. Los límites mínimo y máximo se establecen a partir del parámetro *Límites del cursor*.
* __Set Cursor__ : Acción de tipo deslizante que permite definir el valor de *Cursor* entre los límites especificados

Puedes utilizar un comando de tipo ``evento'' en un escenario para establecer el valor del ``Cursor''.

# |Colours|
  
   ![couleur-onglet](/ReadmeImg/couleurs.PNG) 
   
Aquí se definen los colores de la transición.
Puedes ordenar los colores arrastrando y soltando
Puedes añadir tantos colores como quieras.
Hay cuatro parámetros disponibles para los colores:
   * __Name__ : un nombre único que usted elige
   * La ventana de selección depende del navegador y del sistema. En general, con una rueda o un panel, los valores TSL y RGB
   * __Alpha__ : un valor para el canal alfa, definido por un deslizador entre 0 y 255
   * __Blanco__ : un valor para el canal blanco, definido por un deslizador entre 0 y 255

*Nota*: Las columnas Alfa y Blanco se muestran u ocultan dependiendo de la configuración del equipo. 
  
  
# |Widget|
  ![couleur-onglet](/ReadmeImg/widget.PNG) 
  
  El widget por defecto del pllugin muestra el valor del color de transición en el formato configurado, con una burbuja que representa ese color (sin los canales alfa y blanco).
 El deslizador que se muestra para el comando Establecer Cursor se deriva del deslizador del núcleo, con un tamaño de paso de 1/1000 del rango configurado por el cursor.
