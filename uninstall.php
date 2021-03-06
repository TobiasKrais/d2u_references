<?php
$sql = rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_url_references');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_url_tags2ref'); // Unused since 1.0.5
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_url_tags');

// Delete url schemes
if(\rex_addon::get('url')->isAvailable()) {
	if(rex_version::compare(\rex_addon::get('url')->getVersion(), '1.5', '>=')) {
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'reference_id';");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'tag_id';");		
	}
	else {
		$sql->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_references_url_%'");
	}
}

// Delete Media Manager media types
$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."media_manager_type WHERE name LIKE 'd2u_immo%'");
$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."media_manager_type_effect WHERE createuser = 'd2u_immo'");

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_tag2refs');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_tags');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_tags_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_references');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_references_lang');

// Delete language replacements
if(!class_exists('d2u_references_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_references_lang_helper.php';
}
d2u_references_lang_helper::factory()->uninstall();