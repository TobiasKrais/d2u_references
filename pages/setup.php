<?php
/*
 * Modules
 */
$d2u_module_manager = new D2UModuleManager(D2UReferencesModules::getModules(), "modules/", "d2u_references");

// D2UModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if($d2u_module_id != "") {
	$d2u_module_manager->doActions($d2u_module_id, $function, $paired_module);
}

// D2UModuleManager show list
$d2u_module_manager->showManagerList();

/*
 * Templates
 */
?>
<h2>Beispielseiten</h2>
<ul>
	<li>Referenzen Addon: <a href="http://www.design-to-use.de" target="_blank">
		www.design-to-use.de</a>.</li>
</ul>
<h2>Support</h2>
<p>Fehlermeldungen bitte im <a href="https://github.com/TobiasKrais/d2u_references" target="_blank">GitHub Repository</a> melden.</p>
<h2>Changelog</h2>
<p>1.0.1 (NEXT):</p>
<ul>
	<li>Bugfix: Speichern wenn zweite Sprache Standardsprache ist schlug fehl.</li>
	<li>Bugfix: Löschen einer Sprache hat Objekte nicht gelöscht.</li>
	<li>Weitere kleine Bugfixes.</li>
	<li>Neues Modul: Horizontale Mini Reihe.</li>
	<li>Einbindung von Videos nun möglich.</li>
</ul>
<p>1.0.0:</p>
<ul>
	<li>Initiale Version.</li>
</ul>