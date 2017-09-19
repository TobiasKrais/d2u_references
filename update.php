<?php
// Update language replacements
d2u_references_lang_helper::factory()->install();

// Update modules
if(class_exists(D2UModuleManager)) {
	$modules = [];
	$modules[] = new D2UModule("50-1",
		"D2U Referenzen - Vertikale Referenzboxen ohne Detailansicht",
		1);
	$modules[] = new D2UModule("50-2",
		"D2U Referenzen - Horizontale Referenzboxen mit Detailansicht",
		1);
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_references");
	$d2u_module_manager->autoupdate();
}