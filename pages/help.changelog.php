<?php
?>
<h2>Changelog</h2>
<p>1.2.1-DEV:</p>
<ul>
	<li>Backend: CSRF-Schutz fuer Speichern-, Loesch- und Statusaktionen der Referenzverwaltung ergaenzt.</li>
	<li>...</li>
</ul>
<p>1.2.0:</p>
<ul>
	<li>Neue Module 50-5 bis 50-8 als Bootstrap-5-Varianten der bestehenden Beispielmodule hinzugefügt.</li>
	<li>Leerer Modulordner 50-5 mit einer echten BS5-Variante belegt, damit Setup und Dokumentation wieder konsistent sind.</li>
	<li>Module 50-1 bis 50-4 als "(BS4, deprecated)" markiert. Die BS4-Varianten werden im nächsten Major Release entfernt.</li>
	<li>Module 50-4 und 50-8: Textspalte der Farbboxen mit seitlichem Bild auf Flex-Zentrierung umgestellt, damit der Text nicht oberhalb des Containers positioniert wird.</li>
	<li>Referenzen haben nun ein eigenes Light-/Dark-Mode Farbfeld im Backend. Die BS5-Farbbox-Module verwenden im Dark Mode den individuellen dunklen Referenz-Farbwert mit Fallback auf den hellen Referenz-Farbwert.</li>
	<li>Benötigt d2u_helper &gt;= 2.1.0.</li>
	<li>Tags in Übersichtspalte hinzugefügt und Spalten sortierbar gemacht.</li>
	<li>Referenz um Feld für Artikel ID erweitert.</li>
</ul>
<p>1.1.0:</p>
<ul>
	<li>Vorbereitung auf R6: Folgende Klassen wurden umbenannt. Die alten Klassennamen funktionieren weiterhin, sind aber als veraltet markiert.
		<ul>
			<li><code>d2u_references_frontend_helper</code> wird zu <code>TobiasKrais\D2UReferences\FrontendHelper</code>.</li>
			<li><code>Reference</code> wird zu <code>TobiasKrais\D2UReferences\Reference</code>.</li>
			<li><code>Tag</code> wird zu <code>TobiasKrais\D2UReferences\Tag</code>.</li>
		</ul>
		Folgende interne Klassen wurden wurden ebenfalls umbenannt. Es gibt keinen Übergang, da diese Klassen nur intern verwendet wird:
		<ul>
			<li><code>d2u_references_lang_helper</code> wird zu <code>TobiasKrais\D2UReferences\LangHelper</code>.</li>
			<li><code>D2UReferencesModules</code> wird zu <code>TobiasKrais\D2UReferences\Module</code>.</li>
		</ul>
	</li>
	<li>rexstand Level 9 Abgleich.</li>
	<li>update.php und install.php vereinheitlicht.</li>
	<li>Modul "50-2 D2U Referenzen - Horizontale Referenzboxen mit Detailansicht": An D2U Videomanager Addon >= 1.2 angepasst.</li>
	<li>Modul "50-3 D2U Referenzen - Horizontale Mini Referenzboxen mit Detailansicht": An D2U Videomanager Addon >= 1.2 angepasst.</li>
	<li>Modul "50-4 D2U Referenzen - Farbboxen mit seitlichem Bild": An D2U Videomanager Addon >= 1.2 angepasst.</li>
</ul>
<p>1.0.11:</p>
<ul>
	<li>Nutzt das neue Bilderliste Feld mit Vorschaufunktion der Bilder.</li>
	<li>PHP-CS-Fixer Code Verbesserungen.</li>
</ul>
<p>1.0.10:</p>
<ul>
	<li>Fehler Installer Action behoben.</li>
</ul>
<p>1.0.9:</p>
<ul>
	<li>Anpassungen an Publish Github Release to Redaxo.</li>
	<li>Unterstützt nur noch URL Addon >= 2.0.</li>
	<li>Methode d2u_references_frontend_helper::getMetaTags() entfernt, da das URL Addon eine bessere Funktion anbietet.
		Ebenso die Methoden getMetaAlternateHreflangTags(), getMetaDescriptionTag(), getCanonicalTag und getTitleTag() aller Klassen, die diese Methoden abgeboten hatten.</li>
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