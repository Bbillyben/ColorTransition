
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


/* Permet la réorganisation des commandes dans l'équipement */
$("#table_cmd").sortable({
  axis: "y",
  cursor: "move",
  items: ".cmd",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
});


/* Permet la réorganisation des commandes dans les couleurs */
$("#table_color_cmd").sortable({
  axis: "y",
  cursor: "move",
  items: ".cmd",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
});


/* ajout d'une couleur dans la table des couleurs */


/* Fonction permettant l'affichage des commandes dans l'équipement */
function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
     var _cmd = {configuration: {}};
   }
   if (!isset(_cmd.configuration)) {
     _cmd.configuration = {};
   }
   
   if(_cmd.logicalId == 'currentColor' || _cmd.logicalId == 'curseurIndex' || _cmd.logicalId == 'setCurseurIndex'  ){
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td style="width:60px;">';
    tr += '<span class="cmdAttr" data-l1key="id"></span>';
    tr += '</td>';
    tr += '<td style="min-width:300px;width:350px;">';
    tr += '<div class="row">';
    tr += '<div class="col-xs-7">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom de la commande}}">';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display : none;margin-top : 5px;" title="{{Commande information liée}}">';
    tr += '<option value="">{{Aucune}}</option>';
    tr += '</select>';
    tr += '</div>';
    //tr += '<div class="col-xs-5">';
    //tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> {{Icône}}</a>';
    //tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
    //tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td style="min-width:150px;width:350px;">';
    //tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min.}}" title="{{Min.}}" style="width:30%;display:inline-block;"/> ';
    //tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max.}}" title="{{Max.}}" style="width:30%;display:inline-block;"/> ';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="{{Unité}}" title="{{Unité}}" style="width:30%;display:inline-block;"/>';
    tr += '</td>';
    tr += '<td style="min-width:80px;width:350px;">';
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label>';
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label>';
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label>';
    tr += '</td>';
    tr += '<td style="min-width:80px;width:200px;">';
    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> Tester</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    var tr = $('#table_cmd tbody tr').last();
  }else{

    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td style="width:30px;">';
    tr += '<span class="cmdAttr" data-l1key="id"></span>';
    tr += '<input class="cmdAttr form-control input-sm type" data-l1key="type" value="info" style="display:none;"/>';
    tr += '<span class="subType" subType="string" value="string"  style="display:none;"></span>';
    //tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" value="string" style="display:none;"/>';
    tr += '</td>';
    
     tr += '<td style="min-width:30px;width:40px;">';
    tr += '<div class="">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom de la commande}}">';
    tr += '</div>';
    tr += '</td>';
    tr += '<td style="min-width:10px;width:15px;">';
    tr += '<input type="color" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="color" placeholder="{{Couleur}}">';
    tr += '</td>';

    tr += '<td class="white_slider" style="min-width:20px;width:25px;">';
    //tr += '<input class="slider-show" data-l1key="configuration" data-l2key="white" style="width: 100%;"/>';
    tr += '<input class="cmdAttr slider ui-slider ui-corner-all ui-slider-horizontal slider-show" type="range" min="0" max="255" data-l1key="configuration" data-l2key="white" placeholder="{{white}}" style="width: 100%;">';
    tr += '<span class="sliderValue" style="display: none;"></span>';
    tr += '</td>';
    
    tr += '<td class="alpha_slider" style="min-width:20px;width:25px;">';
    tr += '<input class="cmdAttr slider ui-slider ui-corner-all ui-slider-horizontal slider-show" type="range" min="0" max="255" data-l1key="configuration" data-l2key="alpha" placeholder="{{Alpha}}" style="width: 100%;">';
    tr += '<span class="sliderValue" style="display: none;"></span>';
    tr += '</input>';
    tr += '</td>';
    
   /* tr += '<td style="min-width:20px;width:25px;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="rank" placeholder="{{rank}}" disabled>';
    tr += '</td>';
    */
    tr += '<td style="min-width:10px;width:20px;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="rank"  style="display:none;" placeholder="{{rank}}" disabled >';
    
    //tr += '<input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>';
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_color_cmd tbody').append(tr);
    
    var tr = $('#table_color_cmd tbody tr').last();
    colortransition_fillColorRank();
    checkUseAlphaWhite();
  }
   jeedom.eqLogic.builSelectCmd({
     id:  $('.eqLogicAttr[data-l1key=id]').value(),
     filter: {type: 'info'},
     error: function (error) {
       $('#div_alert').showAlert({message: error.message, level: 'danger'});
     },
     success: function (result) {
       tr.find('.cmdAttr[data-l1key=value]').append(result);
       tr.setValues(_cmd, '.cmdAttr');
       jeedom.cmd.changeType(tr, init(_cmd.subType));
     }
   });
 }


// gestion du sort et renseignement du rank
$( "#table_color_cmd" ).sortable({
  stop: function( event, ui ) {
   	colortransition_fillColorRank();
    
  }
});

function colortransition_fillColorRank() {
   var i=0;
    jQuery("#table_color_cmd").find('[data-l2key="rank"]').each(function () {
      
      this.value=i;
      i+=1;
  	});
}


// gestion des menu de configuration 
$(".eqLogicAttr[data-l2key='cursor-range']").on('change', function () {
  if($(this).val() != 'custom'){
    $(".cursor-custom-range").hide();
  }else{
    $(".cursor-custom-range").show();
  }

});
$(".eqLogicAttr[data-l2key='use_white']").on('change', checkUseAlphaWhite);
$(".eqLogicAttr[data-l2key='use_alpha']").on('change', checkUseAlphaWhite);
//$(".eqLogicAttr[data-l2key='use_white']").on('click', checkUseAlphaWhite);
//$(".eqLogicAttr[data-l2key='use_alpha']").on('click', checkUseAlphaWhite);

function checkUseAlphaWhite(){
  if($(".eqLogicAttr[data-l2key='use_white']").prop('checked')){
    $(".white_slider").show();
  }else{
    $(".white_slider").hide();
  }
  if($(".eqLogicAttr[data-l2key='use_alpha']").prop('checked')){
    $(".alpha_slider").show();
  }else{
    $(".alpha_slider").hide();
  }
}

// gestion des slider slider-show

$(".slider-show").on("change input mousemove", function(event,ui) {
  console.log(event.value.newValue);
});
/*
$(".slider-show").slider({
  from:0,
  to:255,
  step:1,
  dimension:'',
  slide: function(event, ui) {
      console.log(ui.value);
  },
  change: function(event, ui) {
      console.log(ui.value);
  }
});
$(".slider-show").on('input change', function(){
  console.log("slider input");
  //console.log($(this).val());
});
$( ".slider-show" ).slider({
  slide: function(event, ui) {
    console.log("slider change");
  }
});*/

// gestion image représentative de la transition

$(".eqLogicAttr[data-l2key='transition-type']").on('change', function () {
  var curr = $(this).val();
  
  //gestion des image
  $(".show-curve-wrapper").empty();
  $(".show-curve-wrapper").append('<img class="show-curve"  src="/plugins/ColorTransition/plugin_info/img/'+curr+'.png"  alt="">');
  
  // gestion des aide sur fonction
  $("#trans-param-def").children('*').each(function(){

    if($(this).hasClass('trans-param-'+curr)){
      $(this).show();
    }else{
      $(this).hide();
    }
  });

});





// gestion de l'affichage de l'équipement
function printEqLogic(_mem) {



  $.ajax({
    type: "POST", 
    url: "plugins/ColorTransition/core/ajax/ColorTransition.ajax.php", 
    data: {
        action: "get-showroom",
        eqlogicId:init(_mem.id)
    },
    dataType: 'json',
    error: function (request, status, error) {
        handleAjaxError(request, status, error);
    },
    success: function (data) { // si l'appel a bien fonctionné
        if (init(data.state) != 'ok') {
            $('#div_alert').showAlert({message: data.result, level: 'danger'});
            return;
        }
        //console.log(data.result);
        
        // onvide le wrapper
        $(".show-transition-wrapper").empty();
        
        for (var i = 0, len = data.result.length; i < len; i++) {
          $(".show-transition-wrapper").append('<span class="show-transition">.</span>');
          $(".show-transition-wrapper span:last-child").css("background-color", data.result[i]);
          $(".show-transition-wrapper span:last-child").css("color", data.result[i]);
        }

        
    }
  });
};