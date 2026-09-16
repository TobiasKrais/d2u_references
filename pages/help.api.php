<?php

/**
 * Backend documentation for the d2u_references REST API.
 *
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use TobiasKrais\D2UReferences\Api\Schema;

if (!rex_addon::get('api')->isAvailable()) {
    echo rex_view::warning('Das Addon <code>api</code> ist nicht installiert oder aktiviert. Die REST API von d2u_references baut darauf auf und ist erst nach Installation des <code>api</code>-Addons verfügbar.');
    return;
}

$describe = Schema::describe();
$tokenUrl = rex_url::backendPage('api/token');

$content = '';

$content .= '<p>Diese REST API erlaubt es externen Werkzeugen, Referenzen und Tags dieses Addons auszulesen und einzuspielen – inklusive Texten, Bildern, Farben und Verknüpfungen (Tags, Video, Artikel). Sie baut auf dem Addon <code>api</code> auf; alle Endpunkte erscheinen automatisch in dessen OpenAPI/Swagger-Ansicht und unter <code>/api/me</code>.</p>';

$content .= '<h3>Authentifizierung</h3>';
$content .= '<p>Jeder Aufruf benötigt ein Bearer-Token aus dem <code>api</code>-Addon. Lege ein Token unter <a href="'. rex_escape($tokenUrl) .'">API &rsaquo; Token</a> an und weise ihm die benötigten Scopes zu. Beispiel-Header:</p>';
$content .= '<pre><code>Authorization: Bearer DEIN_TOKEN</code></pre>';

$content .= '<h3>Scopes</h3>';
$content .= '<p>Die Scopes folgen dem Schema <code>d2u_references/&lt;ressource&gt;/&lt;operation&gt;</code>. Der Schema-Endpunkt benötigt nur ein gültiges Token (kein eigener Scope).</p>';
$content .= '<table class="table table-striped"><thead><tr><th>Zweck</th><th>Scope</th></tr></thead><tbody>';
$content .= '<tr><td>Fähigkeiten &amp; Feldschema</td><td><code>d2u_references/schema</code></td></tr>';
foreach ($describe['resources'] as $resource => $info) {
    foreach (['list', 'get', 'create', 'update', 'delete'] as $operation) {
        $content .= '<tr><td>'. rex_escape($resource) .' &ndash; '. rex_escape($operation) .'</td><td><code>d2u_references/'. rex_escape($resource) .'/'. rex_escape($operation) .'</code></td></tr>';
    }
}
$content .= '</tbody></table>';

$content .= '<h3>Discovery / Feldschema</h3>';
$content .= '<p>Der Schema-Endpunkt liefert eine maschinenlesbare Beschreibung (Sprachen, Ressourcen und Felder inkl. Typ, Pflichtfeld, Sprachabhängigkeit, Relation und SEO-Rolle):</p>';
$content .= '<pre><code>curl -H "Authorization: Bearer DEIN_TOKEN" \\'. "\n" .'  '. rex_escape(rtrim((string) rex::getServer(), '/')) .'/api/d2u_references/schema</code></pre>';
$content .= '<p>Das Attribut <code>seo</code> markiert, wofür ein Feld im Frontend genutzt wird: <code>title</code> (Meta-Titel), <code>description</code> (Meta-Beschreibung) oder <code>image</code> (Quelle für <code>og:image</code>; bei <code>media[]</code> das erste Bild).</p>';

$content .= '<h3>Payload-Aufbau (Anlegen/Aktualisieren)</h3>';
$content .= '<p>Nicht-sprachabhängige Felder liegen unter <code>fields</code>, sprachabhängige je Sprach-ID unter <code>translations</code>. Unbekannte Felder werden mit <code>HTTP 400</code> abgelehnt. Das Feld <code>video_id</code> verknüpft ein d2u_videos-Video und ist nur beschreibbar, wenn das Addon <code>d2u_videos</code> installiert ist.</p>';
$referenceExample = [
    'fields' => [
        'online_status' => 'online',
        'pictures' => ['projekt_a_1.jpg', 'projekt_a_2.jpg'],
        'tag_ids' => [2, 5],
        'date' => '2024-05-01',
    ],
    'translations' => [
        '1' => ['name' => 'Projekt A', 'teaser' => 'Kurztext', 'description' => '<p>Beschreibung</p>'],
    ],
];
$content .= '<pre><code>curl -X POST -H "Authorization: Bearer DEIN_TOKEN" -H "Content-Type: application/json" \\'. "\n" .'  -d \''. rex_escape((string) json_encode($referenceExample, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) .'\' \\'. "\n" .'  '. rex_escape(rtrim((string) rex::getServer(), '/')) .'/api/d2u_references/references</code></pre>';

$content .= '<h3>Bilder hochladen</h3>';
$content .= '<p>Bilder werden zuerst über den Medien-Endpunkt des <code>api</code>-Addons hochgeladen und anschließend per Dateiname referenziert:</p>';
$content .= '<ol>';
$content .= '<li><code>POST /api/media</code> (multipart) im <code>api</code>-Addon &rarr; liefert den Dateinamen.</li>';
$content .= '<li>Den Dateinamen in <code>pictures</code> (Liste) bzw. <code>picture</code> (Tag, Einzelbild) eintragen.</li>';
$content .= '</ol>';

$content .= '<h3>Fehlercodes</h3>';
$content .= '<table class="table table-striped"><thead><tr><th>Code</th><th>Bedeutung</th></tr></thead><tbody>';
$content .= '<tr><td><code>400</code></td><td>Ungültiger Payload, unbekanntes Feld oder fehlendes Pflichtfeld</td></tr>';
$content .= '<tr><td><code>401</code></td><td>Kein oder ungültiges Token bzw. fehlender Scope</td></tr>';
$content .= '<tr><td><code>404</code></td><td>Ressource nicht verfügbar oder Datensatz nicht gefunden</td></tr>';
$content .= '<tr><td><code>500</code></td><td>Interner Fehler (Details im REDAXO-Systemlog)</td></tr>';
$content .= '</tbody></table>';
$content .= '<p><small>Hinweis: <code>PUT</code>, <code>PATCH</code> und <code>DELETE</code> müssen serverseitig erlaubt sein; manche Apache-Konfigurationen blockieren diese Methoden (Antwort: HTTP 403 als HTML).</small></p>';

$content .= '<h3>Aktuell verfügbare Ressourcen und Felder</h3>';

foreach ($describe['resources'] as $resource => $info) {
    $content .= '<h4>'. rex_escape($resource) .' <small>(ID-Feld: <code>'. rex_escape($info['id_field']) .'</code>)</small></h4>';
    $content .= '<table class="table table-striped"><thead><tr><th>Feld</th><th>Typ</th><th>Pflicht</th><th>Sprachabhängig</th><th>Relation</th><th>SEO</th></tr></thead><tbody>';
    foreach ($info['fields'] as $field) {
        $content .= '<tr>'
            .'<td><code>'. rex_escape($field['name']) .'</code></td>'
            .'<td>'. rex_escape($field['type']) .'</td>'
            .'<td>'. ($field['required'] ? 'ja' : '&ndash;') .'</td>'
            .'<td>'. ($field['language'] ? 'ja' : '&ndash;') .'</td>'
            .'<td>'. (null !== $field['relation'] ? '<code>'. rex_escape((string) $field['relation']) .'</code>' : '&ndash;') .'</td>'
            .'<td>'. (null !== $field['seo'] ? '<code>'. rex_escape((string) $field['seo']) .'</code>' : '&ndash;') .'</td>'
            .'</tr>';
    }
    $content .= '</tbody></table>';
}

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('d2u_references_help_chapter_api'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
