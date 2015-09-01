<?php
/*
 * Script créant et vérifiant que les champs requis s'ajoutent bien
 */


	define('INC_FROM_CRON_SCRIPT', true);
	
	
	//require('../class/pouvoirs.class.php');

	
	$powersdb=new TPDOdb;
	$powersdb->db->debug=true;
	
	
	//$power=new THaPouvoirs;
	//$power->init_db_by_vars($powersdb);
	
	


/* uncomment


dol_include_once('/mymodule/class/xxx.class.php');

$PDOdb=new TPDOdb;

$o=new TXXX($db);
$o->init_db_by_vars($PDOdb);
*/
