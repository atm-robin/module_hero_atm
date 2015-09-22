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
            $hero->removeChild('THaPouvoir', GETPOST('idPower'));
            $hero->save($PDOdb);
            _fiche($PDOdb, $hero,'edit');
            break;   
          
        case 'delete' :
            
            $hero->load($PDOdb, GETPOST('id'));
            $hero->delete($PDOdb);
            
            _liste($PDOdb);
            break;
		case 'view'	:
            $hero->load($PDOdb, GETPOST('id'));
            
			_fiche($PDOdb, $hero);
			break;
			
		case 'save'	:
			$hero->load($PDOdb, GETPOST('id'));
			$hero->set_values($_REQUEST);
            
            $k = $hero->addChild($PDOdb, 'THaPouvoir');
            $hero->THaPouvoir[$k]->name = GETPOST('power');
            
            
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
    
    $sql=" SELECT rowid as ID, name, description FROM ".MAIN_DB_PREFIX."hero";
    
    echo $l->render($PDOdb, $sql,array(
        'link'=>array(
            'name'=>'<a href="?action=view&id=@ID@">@val@</a>'
        )
        ,'title'=>array(
            'name'=>$langs->trans('Name')
        )
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
	
	$form=new TFormCore($_SERVER['PHP_SELF'], 'form', 'POST');
    print $form->hidden('action','save');
    print $form->hidden('id', $hero->getId());
    
    
    $form->Set_typeaff($action);
    
	$TBS=new TTemplateTBS();
	
    $buttons ='';
    
    if($action == 'view') {
        if($hero->getId()>0) $buttons .="<input type=\"button\" id=\"action-delete\" value=\"Supprimer\" name=\"cancel\" class=\"butActionDelete\" onclick=\"if(confirm('Supprimer ce hero ?'))document.location.href='?action=delete&id=".$hero->rowid."'\" />"; 
     
        $buttons.='<a class="butAction" href="?action=edit&id='.$hero->getId().'">Modifier</a>';
        
    }
    else {
     
        $buttons .= $form->btsubmit('Valider', 'save');
    }
    /*$btSave = $form->btsubmit('Valider', 'save');
    $btCancel = $form->btsubmit('Annuler', 'cancel');
	
	$btDelete = ;
	*/
	
	
    
    $THero = $hero->get_tab();
    $THero['name']=$form->texte('', 'name', $hero->name, 30,255);
    $THero['description']=$form->zonetexte('', 'description', $hero->description, 80,5);
    
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

	