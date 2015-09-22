<?php

class Thero extends TObjetStd{
	function __construct(){
        parent::set_table(MAIN_DB_PREFIX.'hero');
        parent::add_champs('entity,active', array('type'=>'integer', 'index'=>true )  );
        parent::add_champs('name,description');
        parent::_init_vars();
        $this->start();
        
        $this->setChild('THaPouvoir', 'fk_hero');
        
    }
	
    function getPower() {
        $Tab = array();
        
        //pour chaque élément de THapouvoir attribue l'id au nom du pouvoir dans un tableau
        foreach ($this->THaPouvoir as &$power) {
            if($power->to_delete) continue;
            
            $Tab[$power->getId()] = $power->name;
        }
        
        return $Tab;
        
        
    }
    
    
}



