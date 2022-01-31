<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class ColorTransition extends eqLogic {
  
     // définition des commande par type d'équipement
   const logID_common = array(
      'currentColor',
      'curseurIndex',
      'setCurseurIndex'
   );
  const default_param = array(
      'linear'=>-1,
      'sinus'=>-1,
      'log'=>0.2,
      'exp'=>0.01,
      'puiss'=>2,
      'sigmoid'=>10,
      'logit'=>0.2
    
    );
  // #################### methodes statics
  // pour retour des couleurs
  public static function getShowRoom($eqId){
    
    //$eqL=eqLogic::byLogicalId($eqId,"ColorTransition");
    $eqL=eqLogic::byId($eqId);

    if(!is_object($eqL)){
      log::add('ColorTransition', 'error', '####### Show room error '.$eqId.' not found######');
      return false;
    }
    $colorArray=$eqL->getColorsArray();

    $showRoomColors=Array();
    $numChild=180;
    for($i=0;$i<=$numChild;$i++){
      $showRoomColors[]=$eqL->calculateColorFromIndex($i/$numChild, $colorArray,false,false,'hexa');
    }

    //log::add('ColorTransition', 'debug','╠════ Colors :'.print_r($output));
      return ($showRoomColors);
  }

 // #################### Méthode de calcul des couleurs ###################
  
  public function calculateCurrentColor(){
    // récupe des config 
    $useAlpha=$this->getConfiguration('use_alpha');
    $useWhite=$this->getConfiguration('use_white');
    $outputType=$this->getConfiguration('color-format');
    
    
    log::add('ColorTransition', 'debug', '╔═══════════════════════ start color calculation ════════════════════ ');
    $colorArray = $this->getColorsArray();
    //log::add('ColorTransition', 'debug', '╠════ Colors : '.json_encode($colorArray));
    
    // valeur du curseur
    $ctCMD = $this->getCmd(null, 'curseurIndex');
     if (!is_object($ctCMD)) {
     	log::add('ColorTransition', 'error', '####### Commande Curseur non définie ######');
       return false;
     }
    $cursValue = $ctCMD->execCmd();
    //log::add('ColorTransition', 'debug', '╠════ Curseur Value : '.$cursValue);
    
    // bornes du curseur
    $typeBorne = $this->getConfiguration('cursor-range');
    
    
    switch ($typeBorne) {
    case 'unite':
        $cursValue = max(min($cursValue,1),0);
        $cursMin=0;
        $cursMax=1;
        
        break;
    case 'colorLength':
        $cursMin=0;
        $cursMax=count($colorArray)-1;
        $cursValue = max(min($cursValue,$cursMax),0);
        
        break;
    case 'custom':
        $cursMin=$this->getConfiguration('cursor-custom-min');
        $cursMax=$this->getConfiguration('cursor-custom-max');
        $cursValue = max(min($cursValue,$cursMax),$cursMin);
        break;
	}
    $cursPos=($cursValue-$cursMin)/($cursMax-$cursMin);
    //log::add('ColorTransition', 'debug', '╠════ Curseur Value / % pos : '.$cursValue.' / '.$cursPos);
    return $this->calculateColorFromIndex($cursPos, $colorArray,$useAlpha,$useWhite,$outputType);
  }

  // ------------------------ calcul à partir de l'index et des types retournés
  public function calculateColorFromIndex($cursPos, $colorArray,$useAlpha,$useWhite,$outputType){
    
    // si color array non défnin
    if(is_null($colorArray)) $colorArray = $this->getColorsArray();
    // transition
   $transType = $this->getConfiguration('transition-type'); // recup type de transition
   
    
    // champs application de la transition
    $transhFielEach=$this->getConfiguration('transition-field-each');
    //log::add('ColorTransition', 'debug', '╠════ Transition champs chacun : '.$transhFielEach);
    

    $cc=count($colorArray)-1;
    $cursIndex=$cursPos*$cc;
    $minI = floor($cursIndex);
    $maxI = ceil($cursIndex);
    
   	$transValue =$this->getTransitionValue($transType, $transhFielEach,$cursIndex, $minI, $maxI, $cc);
    
    $colorRef1=$colorArray[$minI];
    $colorRef2=$colorArray[$maxI];
    
    $colorEnd = $this->calculateFinalColor($colorRef1, $colorRef2, $transValue);
    
    //log::add('ColorTransition', 'debug', '╟─── couleur finale:'.sprintf("#%02x%02x%02x%02x",$colorEnd['a'], $colorEnd['r'],$colorEnd['g'],$colorEnd['b']));
    //log::add('ColorTransition', 'debug', '╟─── couleur finale:'.json_encode($colorEnd));
    
    $output = $this->formatOutput($colorEnd,$useAlpha,$useWhite,$outputType);
    
    
    //log::add('ColorTransition', 'debug', '╠══════════════════════ Couleur Format Final : '.$output);
    
    return $output;
    
  }

  // calcul sur les couleurs
  public function calculateFinalColor($colorRef1, $colorRef2, $transValue){
    
   // log::add('ColorTransition', 'debug', '╠══════════════════════ calcul de la couleur finale au ratio : '.$transValue);

    $hex = "#ff9900";
	  $col1Arr = sscanf($colorRef1['color'], "#%02x%02x%02x");
    $col1Arr[]=intval($colorRef1['alpha']);
    $col1Arr[]=intval($colorRef1['white']);
    $col2Arr = sscanf($colorRef2['color'], "#%02x%02x%02x");
    $col2Arr[]=intval($colorRef2['alpha']);
    $col2Arr[]=intval($colorRef2['white']);
    //log::add('ColorTransition', 'debug', '╟─── couleur ref 1 :'.json_encode($colorRef1));
    //log::add('ColorTransition', 'debug', '╟─── rgb :'.json_encode($col1Arr));
    //log::add('ColorTransition', 'debug', '╟─── couleur ref 1 :'.json_encode($colorRef2));
    //log::add('ColorTransition', 'debug', '╟─── rgb :'.json_encode($col2Arr));
    
    $currCol['r']=intval($col1Arr[0]+($col2Arr[0]-$col1Arr[0])*$transValue);
    $currCol['g']=intval($col1Arr[1]+($col2Arr[1]-$col1Arr[1])*$transValue);
    $currCol['b']=intval($col1Arr[2]+($col2Arr[2]-$col1Arr[2])*$transValue);
    $currCol['a']=intval($col1Arr[3]+($col2Arr[3]-$col1Arr[3])*$transValue);
    $currCol['w']=intval($col1Arr[4]+($col2Arr[4]-$col1Arr[4])*$transValue);
   
    return $currCol;
  }
  
  // format de la sortie
  public function formatOutput($color, $useAlpha, $useWhite, $outputType){
    
    log::add('ColorTransition', 'debug', '╠══════════════════════ formattage de la sortie : ');
    log::add('ColorTransition', 'debug', '╟─── format type :'.$outputType);
    log::add('ColorTransition', 'debug', '╟─── canal alpha :'.$useAlpha);
    log::add('ColorTransition', 'debug', '╟─── canal white :'.$useWhite);
    switch($outputType){
      case 'hexa':
        $output="#".($useAlpha?sprintf("%02x",$color['a']):"").($useWhite?sprintf("%02x",$color['w']):"").sprintf("%02x%02x%02x", $color['r'],$color['g'],$color['b']);
        break;
      case 'json':
        if(!$useAlpha){
          unset($color['a']);
        }
        if(!$useWhite){
          unset($color['w']);
        }
        $output=json_encode($color);
        break;
    }
    
    return $output;
  }
  
  
  
  // fonction mathématique de calculs selon les transitions et le champs d'application de la transition
  // retourne le pourcentage de la seconde couleur
  
 public function getTransitionValue($transType, $transhFielEach, $cursor, &$minI, &$maxI, $totalCount){
   
   $transParam = $this->getConfiguration('transition-param'); // récup le param de la transition
    if(is_null($transParam) || empty($transParam) || !is_numeric($transParam)){ // si param pas défini ou si vide ou si pas numéric
    	$transParam=ColorTransition::default_param[$transType]; // on va cherhcher le params par défaut dans le tableau de classe
    }
   
   //log::add('ColorTransition', 'debug', '╠══════════════════════ calcul de la transition : '.$transType);
   //log::add('ColorTransition', 'debug', '╟─── trans param:'.$transParam);
   //log::add('ColorTransition', 'debug', '╟─── curs param:'.$cursor);
	if($transhFielEach==1){// si transistion entre chaque couleur
      	$val=$this->getTransitionEach($transType, $cursor, $minI, $maxI, $transParam);
    }else{//transition sur toutes les couleurs
    	$val=$this->getTransitionAll($transType, $cursor, $minI, $maxI, $transParam,$totalCount);
    }
   //$val = max(min($val,1),0);
   return $val;
   
 }
  public function getTransitionEach($transType, $cursor, $minI, $maxI, $transParam){

	if( $maxI != $minI){
      $cursIndex = ($cursor-$minI)/($maxI-$minI);// index de variation entre les 2 courleurs en cours
    }else{
      $cursIndex = $maxI;
    }
    $val =$this->getGlobalTransitionValue($transType, $cursIndex, $transParam);
    log::add('ColorTransition', 'debug', '╟─── transition min | max | index | value: '.$minI.' | '.$maxI.' | '.$cursor.' | '.$cursIndex.' | '.$val);
    
  	return $val;
  }
  public function getTransitionAll($transType, $cursor, &$minI, &$maxI, $transParam,$totalCount){
    //log::add('ColorTransition', 'debug', '╟─── start All calculation : '.$minI.' | '.$maxI.' | '.$cursor);
    // ici on va calculer le ratio entre les 2 valeurs qui encadre la valeur calculée de l'index en cours
    $curVal=$this->getGlobalTransitionValue($transType, $cursor/$totalCount, $transParam);
    
    $cursIndex=$curVal*$totalCount;
    $minI = floor($cursIndex);
    $maxI = ceil($cursIndex);
    
    
    $minVal=$minI/$totalCount;
    $maxVal=$maxI/$totalCount;
     if($minVal==$maxVal){ 
       	$val = 1;
     }else{
    	$val =($curVal-$minVal)/($maxVal-$minVal);
     }
    
    //log::add('ColorTransition', 'debug', '╟─── transition min | max | index | value: '.$minVal.' | '.$maxVal.' | '.$curVal.' | '.$val);
   
    
  	return $val;
  }
  
  public function getGlobalTransitionValue($transType, $cursor, $transParam){
  	switch($transType){
     case 'linear':
       		$val=$cursor;
       break;
     case 'sinus':
       		$val=sin((3.1415952*$cursor)/2);
       break;
     case 'log':
       		$val=$transParam*log($cursor)+1;
       break;
     case 'exp':
       		$val=$transParam*exp(log(1/$transParam)*$cursor);
       break;
     case 'puiss':
       		$val=pow($cursor,$transParam);
       break; 
       case 'sigmoid':
        $val=1/(1+exp(-$transParam*($cursor-0.5)));
    break;  
        case 'logit':
          $val=$transParam*log($cursor/(1-$cursor),10)+0.5;
        break;
   }
    $val = max(min($val,1),0);
  return $val;
  
  }
  
  
// construction de l'array des couleurs
  public function getColorsArray(){
    $colorsA = Array();
    
    $allCmds = $this->getCmd('info');
    
    // On récupère les configurations des couleurs parmi les cmd info - ss logicalId
       foreach($allCmds as $cmdCol){
         if(in_array($cmdCol->getLogicalId(),ColorTransition::logID_common)==false){
          $colorsA[]=$cmdCol->getConfiguration();
         }
       }
    
    // sort par le rank ocazou
    usort($colorsA, function($a, $b) { return $a['rank'] - $b['rank'];});
    
   	 return $colorsA;
  }

  // récup des bornes min/max
  public function getBornes(){
    // définition des min max et step
       $typeBorne = $this->getConfiguration('cursor-range');    
      switch ($typeBorne) {
      case 'unite':
          $cursMin=0;
          $cursMax=1;
          break;
      case 'colorLength':
          $colorArray = $this->getColorsArray();
          $cursMax=count($colorArray)-1;
          $cursMin=0;

          break;
      case 'custom':
          $cursMin=$this->getConfiguration('cursor-custom-min');
          $cursMax=$this->getConfiguration('cursor-custom-max');
          break;
      }
    
    return array('min'=>$cursMin, 'max'=>$cursMax);
  }

    /*     * *********************Méthodes d'instance************************* */
    
 // Fonction exécutée automatiquement avant la création de l'équipement 
    public function preInsert() {        
      $this->setConfiguration('transition-field-all', 1);
    }

 // Fonction exécutée automatiquement après la création de l'équipement 
    public function postInsert() {
        
    }

 // Fonction exécutée automatiquement avant la mise à jour de l'équipement 
    public function preUpdate() {
        
    }

 // Fonction exécutée automatiquement après la mise à jour de l'équipement 
    public function postUpdate() {
        
    }

 // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement 
    public function preSave() {
      //log::add('ColorTransition', 'debug', '╔═══════════════════════ start presave ════════════════════ ');
      //rendre les couleurs non visibles
      $allCmds = $this->getCmd('info');
       foreach($allCmds as $cmdCol){
         $cmdLID=$cmdCol->getLogicalId();
         if(in_array($cmdLID,ColorTransition::logID_common)==false && $cmdCol->getIsVisible() == 1){
           //log::add('ColorTransition', 'debug', '╠════ set non visible cmd : '.$cmdCol->getName());
           $cmdCol->setIsVisible(0);
           $cmdCol->save(true);
           
         }
       }
    }

 // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
    public function postSave() {
      log::add('ColorTransition', 'debug', '╔═══════════════════════ start postsave and cmd creation ════════════════════ '); 
      // commande info de la couleur courante
    $ctCMD = $this->getCmd(null, 'currentColor');
      if (!is_object($ctCMD)) {
          $ctCMD = new ColorTransitionCmd();
          $ctCMD->setLogicalId('currentColor');
          $ctCMD->setIsVisible(1);
          $ctCMD->setName(__('Couleur courante', __FILE__));
          $ctCMD->setType('info');
          $ctCMD->setSubType('string');
          $ctCMD->setTemplate('dashboard', 'ColorTransition::colorText');
          $ctCMD->setTemplate('mobile', 'ColorTransition::colorText');
      }
      
      $ctCMD->setType('info');
      $ctCMD->setSubType('string');
      $ctCMD->setEqLogic_id($this->getId());
      $ctCMD->save();


      
      // définition des min max et step
       $typeBorne = $this->getConfiguration('cursor-range');    
      switch ($typeBorne) {
      case 'unite':
          $cursMin=0;
          $cursMax=1;
          break;
      case 'colorLength':
          $colorArray = $this->getColorsArray();
          $cursMax=count($colorArray)-1;
          $cursMin=0;

          break;
      case 'custom':
          $cursMin=$this->getConfiguration('cursor-custom-min');
          $cursMax=$this->getConfiguration('cursor-custom-max');
          break;
      }
      log::add('ColorTransition','debug', '╠════ Set min/max for slider :'.$cursMin.' | '.$cursMax);
      
      
      // commande info de la valeur de curseur
    $ctCMD = $this->getCmd(null, 'curseurIndex');
    if (!is_object($ctCMD)) {
       $ctCMD = new ColorTransitionCmd();
       $ctCMD->setLogicalId('curseurIndex');
       $ctCMD->setIsVisible(0);
       $ctCMD->setName(__('Curseur', __FILE__));
    }
    $ctCMD->setType('info');
    $ctCMD->setSubType('numeric');
    $ctCMD->setEqLogic_id($this->getId());
      
      $ctCMD->setConfiguration('minValue',$cursMin);
      $ctCMD->setConfiguration('maxValue',$cursMax);
      
    $ctCMD->save();

      // cmd de set du curseur
      //log::add('ColorTransition', 'debug', '╠════ cmd I value to link : '.$ctCMD->getId()); 
      $ctCMDAct = $this->getCmd(null, 'setCurseurIndex');
      if (!is_object($ctCMDAct)) {
         $ctCMDAct = new ColorTransitionCmd();
         $ctCMDAct->setLogicalId('setCurseurIndex');
         $ctCMDAct->setIsVisible(1);
         $ctCMDAct->setName(__('Set Curseur', __FILE__));
      }
      
      $ctCMDAct->setValue($ctCMD->getId());
      $ctCMDAct->setType('action');
      $ctCMDAct->setSubType('slider');
      $ctCMDAct->setEqLogic_id($this->getId());
      
      
      $ctCMDAct->setConfiguration('minValue',$cursMin);
      $ctCMDAct->setConfiguration('maxValue',$cursMax);
      $ctCMDAct->setConfiguration('step',($cursMax-$cursMin)/1000);
      $ctCMDAct->setTemplate('dashboard', 'ColorTransition::rangedSlider');
      $ctCMDAct->setTemplate('mobile', 'ColorTransition::rangedSlider');
      
      //save
      $ctCMDAct->save(true);
  
      
      
    }
 /* public static function templateWidget(){
	$return = array('info' => array('string' => array()));
	$return['info']['string']['color'] = array(
	);
	return $return;
}*/


 // Fonction exécutée automatiquement avant la suppression de l'équipement 
    public function preRemove() {
        
    }

 // Fonction exécutée automatiquement après la suppression de l'équipement 
    public function postRemove() {
        
    }

    /*
     * Non obligatoire : permet de modifier l'affichage du widget (également utilisable par les commandes)
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire : permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire : permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class ColorTransitionCmd extends cmd {
    /*     * *************************Attributs****************************** */
    
    /*
      public static $_widgetPossibility = array();
    */
    
    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */
 	public function setConfiguration($_key, $_value) {
		 parent::setConfiguration($_key, $_value);
    	 log::add('ColorTransition','debug', '╠------------------- set config call :'.$this->getName().'  / '.$_key.':'.$_value);
	}
  // Exécution d'une commande  
     public function execute($_options = array()) {
       log::add('ColorTransition','debug', "╔═══════════════════════ execute CMD : ".$this->getId()." | ".$this->getHumanName().", logical id : ".$this->getLogicalId() ."  options : ".print_r($_options));
      log::add('ColorTransition','debug', '╠════ Eq logic '.$this->getEqLogic()->getHumanName());
      
      switch($this->getLogicalId()){
         case 'setCurseurIndex':
          	$cmdInfo = cmd::byId($this->getValue());
          	if(is_object($cmdInfo))$cmdInfo->event($_options['slider']);
         	break;
         Default:
         log::add('ColorTransition','debug', '╠════ Default call');

      } 
      log::add('ColorTransition','debug', "╚═════════════════════════════════════════ END execute CMD ");
     }
  
  public function event($_value, $_datetime = null, $_loop = 1) {
    parent::event($_value, $_datetime, $_loop);
    if($this->getLogicalId()=='curseurIndex'){
      $output=$this->getEqLogic()->calculateCurrentColor();
      // valorisation de l'info currentColor
      $ctCMD = $this->getEqLogic()->getCmd(null, 'currentColor');
        if (!is_object($ctCMD)) {
           log::add('ColorTransition', 'error', '####### Commande Couleur Courante non définie ######');
          return false;
        }
      $ctCMD->event($output);
    
    }
  }
 

    /*     * **********************Getteur Setteur*************************** */
}