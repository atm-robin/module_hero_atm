<?php
/*
 * Script créant et vérifiant que les champs requis s'ajoutent bien
 */


	/*if(!defined('INC_FROM_DOLIBARR')) {*/
    define('INC_FROM_CRON_SCRIPT', true);
	
    dol_include_once('hero_atm/config.php');
	dol_include_once('/hero_atm/class/hero.class.php');
	dol_include_once('/hero_atm/class/pouvoir.class.php');
	
    $PDOdb=new TPDOdb;
   // $PDOdb->debug=true;
   
   
   $power=new THaPouvoir;
	$power->init_db_by_vars($PDOdb);
	
    
    $hero=new Thero;
    $hero->init_db_by_vars($PDOdb);
	
