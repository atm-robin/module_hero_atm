<?php
	require('config.php');
    dol_include_once('/hero_atm/class/hero.class.php');
	dol_include_once('/hero_atm/class/pouvoir.class.php');
	
	$PDOdb=new TPDOdb;
	
	$socid=GETPOST('socid');
	
	$action = GETPOST('action');
	if(!$action) $action = 'list';
	//_form_thirdparty($PDOdb, $db);
	// Pour que le bouton "Annuler" de la fiche d'un dossier annule et ne sauvegarde pas...
	/*if(isset($_REQUEST['cancel']) && $_REQUEST['cancel'] == "Annuler") {
		$action = "";
	
*/

    //instanciation d'un nouvel objet hero
    $hero = new Thero;

	llxHeader("");
	
	switch($action) {
		case 'see':
			var_dump('case see');
			_liste($PDOdb, $pouvoirs);
			break;
		case 'add':
			//var_dump('case add');
			
			
			_fiche($PDOdb, $hero, 'edit');
			break;
        case 'edit' :
            
            $hero->load($PDOdb, GETPOST('id'));
            
            _fiche($PDOdb, $hero,'edit');
            break;
        case 'delete-power':
            $hero->load($PDOdb, GETPOST('id'));
            //supprime l'element enfant (ici à travers la clé étrangère vers hapouvoirs)
            $hero->removeChild('THaPouvoir', GETPOST('idPower'));
            //Remove child ajoute un attribut to_delete. Il faut alors sauvegarder à nouveau pour le supprimer
            $hero->save($PDOdb);
            _fiche($PDOdb, $hero,'edit');
            break;   
          
        case 'delete' :
            
            $hero->load($PDOdb, GETPOST('id'));
            $hero->delete($PDOdb);
            
            _liste($PDOdb);
            break;
		case 'view'	:
		
            //charge l'objet hero correspondant à l'id passé en paramètre
            $hero->load($PDOdb, GETPOST('id'));
            
			_fiche($PDOdb, $hero);
			break;
			
		case 'save'	:
            //charge l'objet hero correspondant à l'id passé en paramètre
			$hero->load($PDOdb, GETPOST('id'));
            //ajoute les valeurs au héro en base de donnée (sauvegarde)
			$hero->set_values($_REQUEST);
            
            //attribution de la clé étrangère THaPouvoir à Hero
            $k = $hero->addChild($PDOdb, 'THaPouvoir');
            //récupération des pouvoirs du héro
            $hero->THaPouvoir[$k]->name = GETPOST('power');
            
            //sauvegarde du héro
			$hero->save($PDOdb);
			
			_fiche($PDOdb, $hero);
			break;
			
		default:
			_liste($PDOdb);
			break;
	}


llxFooter();

function _liste(&$PDOdb)
{
	
    global $langs;
    
    $l=new TListviewTBS('listHero');
    
    //requete récupérant les attributs de chaque héro
    $sql=" SELECT rowid as ID, name, description FROM ".MAIN_DB_PREFIX."hero";
    
    //affichage du template
    //utilisation de tableaux imbriqués :
    // le premier tableau définit les lignes
    // les sous tableaux correspondent chacun a une colonne et aux valeurs correspondantes
    echo $l->render($PDOdb, $sql,array(
        'link'=>array(
        //les @..@ autour de ID et val font références aux attributs correspondant à la valeur de la meme ligne.
            'name'=>'<a href="?action=view&id=@ID@">@val@</a>'
        )
        ,'title'=>array(
            'name'=>$langs->trans('Name')
        )
        //on cache les attributs entity et active qui n'ont ici aucun interet 
        ,'hide'=>array(
            'entity','active'
        )
        ,'search'=>array(
            'name'=>true
        )
    ));
    
}


function _fiche(&$PDOdb, &$hero, $action='view')
{
	global $db;
	
    // création du template
	$form=new TFormCore($_SERVER['PHP_SELF'], 'form', 'POST');
    //affiche un formulaire qui définit les actions 
    print $form->hidden('action','save');
    print $form->hidden('id', $hero->getId());
    
    //définit le type d'action du formulaire en fonction de la variable $action récupérée par le GETPOST
    $form->Set_typeaff($action);
    
    //instanciationb de l'objet template
	$TBS=new TTemplateTBS();
	
    $buttons ='';
    
    //si l'action passée en paramètre est à view : 
    //on affiche les boutons modifier et supprimer 
    if($action == 'view') {
        //si l'id du héro est égal à 1 :
        //on ne peut qu'ajouter un héro (on ne peut pas supprimer un héro inexistant)
        if($hero->getId()>0) $buttons .="<input type=\"button\" id=\"action-delete\" value=\"Supprimer\" name=\"cancel\" class=\"butActionDelete\" onclick=\"if(confirm('Supprimer ce hero ?'))document.location.href='?action=delete&id=".$hero->rowid."'\" />"; 
        
        $buttons.='<a class="butAction" href="?action=edit&id='.$hero->getId().'">Modifier</a>';
        
    }
    //sinon si on est en édition affiche le bouton valider qui sauvegarde
    else {
        
        $buttons .= $form->btsubmit('Valider', 'save');
    }
    /*$btSave = $form->btsubmit('Valider', 'save');
    $btCancel = $form->btsubmit('Annuler', 'cancel');
	
	$btDelete = ;
	*/
	
	
    
    $THero = $hero->get_tab();
    //création de la zone de texte (tirée du template) qui permet d'ajouter le ,nom du héro
    $THero['name']=$form->texte('', 'name', $hero->name, 30,255);
    //création de la zone de texte (tirée du template) qui permet d'ajouter la description
    $THero['description']=$form->zonetexte('', 'description', $hero->description, 80,5);
    
    //récupération des pouvoirs dans TPower (tableau de pouvoirs)
    $TPower = $hero->getPower();
    
    $THero['powers']='';
    
    if(empty($TPower) && $action == 'view') {
        $THero['powers'].='pas de chocolat';
    }
    else {
        
        foreach($TPower as $idp=>$pName) {
            if(!empty($THero['powers']))$THero['powers'].=', ';
            
            $THero['powers'].=$pName;
            
            if($action == 'edit' ) $THero['powers'].=' <a href="?action=delete-power&id='.$hero->getId().'&idPower='.$idp.'">X</a>';
        }
        
    }
    
    if($action == 'edit') {
        $THero['powers'].=$form->texte(' - Nouveau pouvoir', 'power', '', 30);
    }
    
	print $TBS->render('./tpl/hero.tpl.php'
		,array(
		     	
		)
		,array(
			'hero'=>$THero
			,'view'=>array(
				'mode'=>$mode
			)
			,'buttons'=>array(
				'buttons'=>$buttons
			)
			
		)
	);
	
	$form->end_form();
}

	