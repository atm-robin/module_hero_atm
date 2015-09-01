<?php

class THaPouvoirs extends TObjetStd{
	function __construct(){
		parent::set_table(MAIN_DB_PREFIX, 'ha_pouvoirs');
		parent::add_champs('entity, active', 'type=entier;');
		parent::add_champs('name, description', 'type=chaine');
		parent::_init_vars();
		$this->start();
	}
		
	
}
