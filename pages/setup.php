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
<p>1.0.9-DEV:</p>
<ul>
	<li>Anpassungen an Publish Github Release to Redaxo.</li>
	<li>Unterstützt nur noch URL Addon >= 2.0.</li>
	<li>Methode d2u_references_frontend_helper::getMetaTags() entfernt, da das URL Addon eine bessere Funktion anbietet.
		Ebenso die Methoden getMetaAlternateHreflangTags(), getMetaDescriptionTag(), getCanonicalTag und getTitleTag() der aller Klassen, die diese Methoden abgeboten hatten.</li>
	<li>Bugfix: Beim Löschen von Medien die vom Addon verlinkt werden wurde der Name der verlinkenden Quelle in der Warnmeldung nicht immer korrekt angegeben.</li>
</ul>
<p>1.0.8:</p>
<ul>
	<li>Benötigt Redaxo >= 5.10, da die neue Klasse rex_version verwendet wird.</li>
	<li>Spanische Frontend Übersetzungen hinzugefügt.</li>
	<li>Backend: Beim online stellen einer Referenz in der Referenzenliste gab es beim Aufruf im Frontend einen Fatal Error, da der URL cache nicht neu generiert wurde.</li>
	<li>Modul 50-1 wurde Schatten entfernt und unterstützt nur die in der Referenz gewählte Hintergrundfarbe.</li>
	<li>Modul 50-3 und 50-4: Anpassungen der Eingabefelder an den Redaxo 13 Dark Mode.</li>
	<li>Bugfix: beim Speichern eines Tags mit gewählter Referenzen gab es einen fatal error.</li>
</ul>
<p>1.0.7:</p>
<ul>
	<li>Backend: Einstellungen und Setup Tabs rechts eingeordnet um sie vom Inhalt besser zu unterscheiden.</li>
	<li>Anpassungen an neueste Version des URL Addons Version 2.</li>
	<li>Bugfix: das Löschen eines Bildes im Medienpool wurde unter Umständen mit der Begründung verhindert, dass das Bild in Benutzung sei, obwohl das nicht der Fall war.</li>
	<li>Russische Frontend Übersetzung hinzugefügt.</li>
</ul>
<p>1.0.6:</p>
<ul>
	<li>Bugfix: Fatal error beim Speichern verursacht durch die URL Addon Version 2 Anpassungen behoben.</li>
</ul>
<p>1.0.5:</p>
<ul>
	<li>Bild in sitemap.xml aufgenommen.</li>
	<li>Anpassungen an URL Addon 2.x.</li>
	<li>Listen im Backend werden jetzt nicht mehr in Seiten unterteilt.</li>
	<li>YRewrite Multidomain support.</li>
	<li>Konvertierung der Datenbanktabellen zu utf8mb4.</li>
	<li>Sprachdetails werden ausgeblendet, wenn Speicherung der Sprache nicht vorgesehen ist.</li>
	<li>Bugfix: VIEW blieb in Datenbank bei Deinstallation übrig.</li>
	<li>Bugfix: Deaktiviertes Addon zu deinstallieren führte zu fatal error.</li>
	<li>In den Einstellungen gibt es jetzt eine Option, eigene Übersetzungen in SProg dauerhaft zu erhalten.</li>
</ul>
<p>1.0.4:</p>
<ul>
	<li>Methode zum Erstellen von Meta Tags d2u_references_frontend_helper::getAlternateURLs() hinzugefügt.</li>
	<li>Methode zum Erstellen von Meta Tags d2u_references_frontend_helper::getMetaTags() hinzugefügt.</li>
	<li>Neues Modul mit seitlichem Bild.</li>
	<li>Bugfix: Fehler beim Ändern des Status der Referenz behoben.</li>
	<li>Bugfix: Fehler beim Speichern von Namen mit einfachem Anführungszeichen behoben.</li>
	<li>YRewrite Multidomain Anpassungen.</li>
	<li>Lieblingseditor aus D2U Helper Addon frei wählbar.</li>
</ul>
<p>1.0.3:</p>
<ul>
	<li>Englische Übersetzung fürs Backend hinzugefügt.</li>
	<li>Bugfix: Abruf von Referenzen für Tag endete in Fehler.</li>
</ul>
<p>1.0.2:</p>
<ul>
	<li>Bugfix in Tag Klasse.</li>
</ul>
<p>1.0.1:</p>
<ul>
	<li>Übersetzungshilfe im D2U Helper Addon intergriert.</li>
	<li>Bugfix: Speichern wenn zweite Sprache Standardsprache ist schlug fehl.</li>
	<li>Bugfix: Löschen einer Sprache hat Objekte nicht gelöscht.</li>
	<li>Weitere kleine Bugfixes.</li>
	<li>Neues Modul: Horizontale Mini Reihe.</li>
	<li>Einbindung von Videos nun möglich.</li>
	<li>Editierrechte für Übersetzer eingeschränkt.</li>
	<li>Anpassungen an URL Addon 1.0.1.</li>
</ul>
<p>1.0.0:</p>
<ul>
	<li>Initiale Version.</li>
</ul>