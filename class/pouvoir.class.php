<?php

class THaPouvoir extends TObjetStd{
	function __construct(){
		parent::set_table(MAIN_DB_PREFIX.'ha_pouvoirs');
		 parent::add_champs('fk_hero,active', array('type'=>'integer', 'index'=>true )  );
        parent::add_champs('name,description');
		parent::_init_vars();
		$this->start();
	}
		
	
}
