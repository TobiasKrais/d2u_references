<?php
/*
 * Modules
 */
$d2u_module_manager = new \TobiasKrais\D2UHelper\ModuleManager(\TobiasKrais\D2UReferences\Module::getModules(), 'modules/', 'd2u_references');

// \TobiasKrais\D2UHelper\ModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if ('' !== $d2u_module_id) {
    $d2u_module_manager->doActions($d2u_module_id, $function, $paired_module);
}

// \TobiasKrais\D2UHelper\ModuleManager show list
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