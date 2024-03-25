<?php

// Update database to 1.0.5
$sql = rex_sql::factory();
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_url_tags2ref');

if (rex_version::compare(rex_addon::get('d2u_references')->getVersion(), '1.0.5', '<')) {
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_references_references_lang DROP updateuser;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_references_tags_lang DROP updateuser;');

    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_references_references_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_references_references_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_references_references_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_references_references_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');

    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_references_tags_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_references_tags_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_references_tags_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_references_tags_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');
}

// remove default lang setting
if (rex_config::has('d2u_references', 'default_lang')) {
    rex_config::remove('d2u_references', 'default_lang');
}

rex_addon::get('d2u_references')->includeFile(__DIR__.'/install.php');