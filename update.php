<?php
// Update language replacements
d2u_references_lang_helper::factory()->install();

// Update modules
if(class_exists(D2UModuleManager)) {
	$modules = [];
	$modules[] = new D2UModule("50-1",
		"D2U Referenzen - Vertikale Referenzboxen ohne Detailansicht",
		2);
	$modules[] = new D2UModule("50-2",
		"D2U Referenzen - Horizontale Referenzboxen mit Detailansicht",
		2);
	$modules[] = new D2UModule("50-3",
		"D2U Referenzen - Horizontale Mini Referenzboxen mit Detailansicht",
		1);
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_references");
	$d2u_module_manager->autoupdate();
}

// 1.0.1 Update database: add video support
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_references_references LIKE 'video_id';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_references_references "
		. "ADD video_id INT(10) NULL DEFAULT NULL AFTER pictures;");
}

// remove default lang setting
if (!$this->hasConfig()) {
	$this->removeConfig('default_lang');
}