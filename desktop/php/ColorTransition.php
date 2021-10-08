<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('ColorTransition');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<!-- Page d'accueil du plugin -->
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<!-- Boutons de gestion du plugin -->
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i>
				<br>
				<span>{{Configuration}}</span>
			</div>
		</div>
		<legend><i class="fas fa-table"></i> {{Mes Colortransitions}}</legend>
		<?php
		if (count($eqLogics) == 0) {
			echo '<br/><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun équipement Template n\'est paramétré, cliquer sur "Ajouter" pour commencer}}</div>';
		} else {
			// Champ de recherche
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic"/>';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
			// Liste des équipements du plugin
			echo '<div class="eqLogicThumbnailContainer">';
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
			echo '</div>';
		}
		?>
	</div> <!-- /.eqLogicThumbnailDisplay -->

	<!-- Page de présentation de l'équipement -->
	<div class="col-xs-12 eqLogic" style="display: none;">
		<!-- barre de gestion de l'équipement -->
		<div class="input-group pull-right" style="display:inline-flex;">
			<span class="input-group-btn">
				<!-- Les balises <a></a> sont volontairement fermées à la ligne suivante pour éviter les espaces entre les boutons. Ne pas modifier -->
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
				</a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs">  {{Dupliquer}}</span>
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<!-- Onglets -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>
			<li role="presentation"><a href="#colortab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-pencil-alt"></i> {{Couleurs}}</a></li>
		</ul>
		<div class="tab-content">
			<!-- Onglet de configuration de l'équipement -->
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<!-- Partie gauche de l'onglet "Equipements" -->
				<!-- Paramètres généraux de l'équipement -->
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;"/>
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" >{{Objet parent}}</label>
								<div class="col-sm-7">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
										$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Catégorie}}</label>
								<div class="col-sm-7">
									<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
										echo '</label>';
									}
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Options}}</label>
								<div class="col-sm-7">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
								</div>
							</div>

							<legend><i class="fas jeedomapp-triselect"></i> {{Curseur}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Bornes du curseur}}
									<sup><i class="fas fa-question-circle tooltips" title="{{borne max de valeur du curseur pour le calcul de la transition}}"></i></sup>
								</label>
								<div class="col-sm-7">
                                      <select id="cursor-range" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cursor-range">
										<option value="unite">{{de 0 à 1}}</option>
                                      	<option value="colorLength">{{nombre de couleurs}}</option>
                                      	<option value="custom">{{personnalisées}}</option>
									</select>
								</div>
							</div>
                            <div class="form-group cursor-custom-range" style="display:none">
								 <label class="col-sm-3 control-label">{{min}}</label>
								 <div class="col-sm-2">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cursor-custom-min" placeholder="min"/>
								 </div>
                                      <label class="col-sm-1 control-label">{{max}}</label>
								 <div class="col-sm-2">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cursor-custom-max" placeholder="max"/>
								 </div>
							  </div>
                                      
                            <legend><i class="fas techno-courbes2"></i> {{Transition}}</legend>
                            <div class="form-group">
								<label class="col-sm-3 control-label">{{Application de la transition}}
									<sup><i class="fas fa-question-circle tooltips" title="{{défini si la transition s'applique à l'ensemble des couleurs ou entre chaque couleur}}"></i></sup>
								</label>
								<div class="col-sm-7">
                                      <input type="radio" class="eqLogicAttr " data-l1key="configuration" data-l2key="transition-field-all" id="all" name="transition-field" value="all" checked="">
                                        <label for="all"> {{sur toute la gamme de couleurs}}</label></br>
                                      <input type="radio" class="eqLogicAttr " data-l1key="configuration" data-l2key="transition-field-each" id="each" name="transition-field" value="each">
                                      <label for="each">  {{entre chaque couleur}}</label>
                                      
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Type de transition}}
									<!-- <sup><i class="fas fa-question-circle tooltips" title="{{type de transition appliquée entre 2 couleurs consécutives}}"></i></sup> -->
								</label>
								<div class="col-sm-7">
                                      <select id="transition-type" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="transition-type">
                                      <option value="linear">{{linéaire}}</option>
                                      <option value="puiss">{{puissance}}</option>
									  <option value="sigmoid">{{sigmoid}}</option>
									  <option value="logit">{{logit}}</option>
                                      <option value="log">{{log}}</option>
                                      <option value="exp">{{exp}}</option>
                                      <option value="sinus">{{sinuzoidale}}</option>
                                      
									</select>
								</div>
							</div>
                           	<div class="form-group">
								<label class="col-sm-3 control-label"> {{Paramètre de la transition}}
									<sup><i class="fas fa-question-circle tooltips" title="{{permet de renseigner un paramètre pour la transition, laissez vide par défaut}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="transition-param"/>
                                </div>
							</div>
                                      
                             <legend><i class="fas fa-eye"></i> {{Sortie Couleur}}</legend>
                               <div class="form-group">
								 <label class="col-sm-3 control-label">{{Utiliser le canal Alpha}}</label>
								 <div class="col-sm-7">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="use_alpha" checked/>
								 </div>
							  </div>
							  <div class="form-group">
								 <label class="col-sm-3 control-label">{{Utiliser le canal Blanc}}</label>
								 <div class="col-sm-7">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="use_white"/>
								 </div>
							  </div>
                                      
                              <div class="form-group">
								<label class="col-sm-3 control-label">{{Format de la sortie}}</label>
								<div class="col-sm-7">
                                      <select id="cursor-range" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="color-format">
										<option value="hexa">{{Hexadecimale}} - #(AA/WW)RRGGBB</option>
                                      	<option value="json">{{json}}  - {"r":rr,"g":gg,"b":bb,"a":aa, "w":ww})</option>
									</select>
								</div>
							</div>
                                      
                                      
							
						</div>

						<!-- Partie droite de l'onglet "Équipement" -->
						<!-- Affiche l'icône du plugin par défaut mais vous pouvez y afficher les informations de votre choix -->
						<div class="col-lg-6">
                         <legend><i class="fas fa-info"></i> {{Aperçu}}</legend> 
                         <div class="form-group">
                         	<label class="col-sm-6">{{Courbes de la fonction de transition, en fonction du paramètre}}</label>
                          </div>            
                         <div class="form-group">
                                      
                                      <div class="show-curve-wrapper text-center">
                                      </div>
									

                                    <div class="text-center" id="trans-param-def">
                                          <span class="trans-param-linear trans-param-sinus"><i>{{pas de paramètre}}</i></span>
                                          <span class="trans-param-log"><b>[param]</b>*ln(x)+1  <i>({{défaut}} 0.2)</i></span>
                                          <span class="trans-param-exp"><b>[param]</b>*exp(ln(1/<b>[param]</b>)*x) <i>({{défaut}} 0.01)</i></span>
                                          <span class="trans-param-puiss">x^<b>[param]</b> <i>({{défaut}} 2)</i></span>
										  <span class="trans-param-sigmoid">1/(1+exp(-<b>[param]</b>*(x-0.5))) <i>({{défaut}} 10)</i></span>
										  <span class="trans-param-logit"><b>[param]</b>*log(x/(1-x))+0.5 <i>({{défaut}} 0.2)</i></span>
                                    </div>

                         </div>
                          <br/>
                         <div class="form-group">
                         	<label class="col-sm-6">{{Visualisation de la transformation}}
								<i style="font-size: small;">  ({{sauvegarder pour mettre à jour}})</i>
							</label>
                                      
                         </div>
                         <div class="form-group">
								<div class="show-transition-wrapper">
									<!-- Jquery ajax will insert span-->
								</div>
							</div>
						</div>
					</fieldset>
				</form>
				<hr>
			</div><!-- /.tabpanel #eqlogictab-->

			<!-- Onglet des commandes de l'équipement -->
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<!-- <a class="btn btn-default btn-sm pull-right cmdAction" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande}}</a> -->
				<br/><br/>
				<div class="table-responsive">
					<table id="table_cmd" class="table table-bordered table-condensed ui-sortable">
						<thead>
							<tr>
								<th>{{Id}}</th>
								<th>{{Nom}}</th>
								<th>{{Type}}</th>
								<th>{{Paramètres}}</th>
								<th>{{Options}}</th>
								<th>{{Action}}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div><!-- /.tabpanel #commandtab-->
			<!-- Onglet des couleurs  de l'équipement -->
			<div role="tabpanel" class="tab-pane" id="colortab">
				<a class="btn btn-default btn-sm pull-right cmdAction" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une couleur}}</a>
				<br/><br/>
                <div class="col-lg-6">
				<div class="table-responsive">
					<table id="table_color_cmd" class="table table-bordered table-condensed ui-sortable">
					<thead>
							<tr>
								<th>{{Id}}</th>
                                <th>{{Nom}}</th>
								<th>{{Couleur}}</th>
								<th class="white_slider">{{Blanc}}</th>
								<th class="alpha_slider	">{{Alpha}}</th>
								<!-- <th>{{rank}}</th> -->
                                <th>{{Action}}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
                  </div>
				</div>
			
			</div>
		</div><!-- /.tab-content -->
	</div><!-- /.eqLogic -->
</div><!-- /.row row-overflow -->

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, id_du_plugin) -->
<?php include_file('desktop', 'ColorTransition', 'js', 'ColorTransition');?>
<!-- Inclusion du css) -->
<?php include_file('desktop', 'ColorTransition', 'css', 'ColorTransition');?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js');?>