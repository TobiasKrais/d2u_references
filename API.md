# REST API

Das Addon d2u_references stellt eine REST API bereit, mit der externe Werkzeuge Referenzen und Tags auslesen und einspielen können – inklusive Texten, Bildern, Farben und Verknüpfungen (Tags, Video, Artikel).

Die API baut auf dem Addon [`api`](https://github.com/FriendsOfREDAXO/api) auf. Alle Endpunkte erscheinen automatisch in dessen OpenAPI-/Swagger-Ansicht und unter `/api/me`.

## Voraussetzungen

- Addon `api` (FriendsOfREDAXO) installiert und aktiviert.
- Ein API-Token mit den benötigten Scopes (siehe unten).
- Optional `d2u_videos`, falls Referenzen ein Video verknüpfen sollen (Feld `video_id`).

### Authorization-Header

Manche Apache-Konfigurationen entfernen den `Authorization`-Header. Falls Aufrufe trotz gültigem Token mit `401` beantwortet werden, muss der Header durchgereicht werden. Dazu in der `.htaccess` im Projektstamm direkt nach `RewriteEngine On` ergänzen:

```apache
RewriteCond %{HTTP:Authorization} .
RewriteRule ^ - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```

## Authentifizierung

Jeder Aufruf benötigt ein Bearer-Token aus dem `api`-Addon. Das Token wird im Backend unter **API › Token** angelegt; dort werden ihm die benötigten Scopes zugewiesen.

```
Authorization: Bearer DEIN_TOKEN
```

## Basis-URL

```
https://deine-domain.tld/api/d2u_references/...
```

## Scopes

Die Scopes folgen dem Schema `d2u_references/<ressource>/<operation>`. Der Schema-Endpunkt benötigt nur ein gültiges Token (keinen eigenen Scope).

| Operation | Scope |
| --- | --- |
| Schema/Discovery | `d2u_references/schema` (kein Scope nötig) |
| Liste | `d2u_references/<ressource>/list` |
| Einzeln lesen | `d2u_references/<ressource>/get` |
| Anlegen | `d2u_references/<ressource>/create` |
| Ändern | `d2u_references/<ressource>/update` |
| Löschen | `d2u_references/<ressource>/delete` |

## Discovery / Schema-Abfrage

Der Schema-Endpunkt liefert eine maschinenlesbare Beschreibung: Addon-Version, Sprachen sowie pro Ressource die verfügbaren Felder mit Typ, Pflichtangabe, Sprachabhängigkeit, Relation und SEO-Rolle.

```bash
curl -H "Authorization: Bearer DEIN_TOKEN" \
  https://deine-domain.tld/api/d2u_references/schema
```

Das Attribut `seo` markiert, wofür ein Feld im Frontend genutzt wird: `title` (Meta-Titel), `description` (Meta-Beschreibung) oder `image` (Quelle für `og:image`; bei `media[]` das erste Bild).

## Ressourcen

| Ressource | ID-Feld | Beschreibung |
| --- | --- | --- |
| `references` | `reference_id` | Referenzen/Projekte inkl. sprachabhängiger Texte |
| `tags` | `tag_id` | Schlagworte zur Gruppierung von Referenzen |

## Endpunkte

Pro Ressource stehen die folgenden Endpunkte bereit:

| Methode | Pfad | Beschreibung |
| --- | --- | --- |
| `GET` | `/api/d2u_references/<ressource>` | Liste (Query: `clang_id`, `page`, `per_page`) |
| `GET` | `/api/d2u_references/<ressource>/{id}` | Einzelnen Datensatz inkl. Übersetzungen lesen |
| `POST` | `/api/d2u_references/<ressource>` | Datensatz anlegen |
| `PUT`/`PATCH` | `/api/d2u_references/<ressource>/{id}` | Datensatz ändern |
| `DELETE` | `/api/d2u_references/<ressource>/{id}` | Datensatz löschen |

> Hinweis: `PUT`, `PATCH` und `DELETE` müssen serverseitig erlaubt sein. Manche Apache-Konfigurationen blockieren diese Methoden (Antwort: HTTP 403 als HTML).

## Payload-Aufbau

Nicht sprachabhängige Felder liegen unter `fields`, sprachabhängige je Sprach-ID (clang) unter `translations`. Unbekannte Felder werden mit `HTTP 400` abgelehnt.

Eine Referenz anlegen:

```json
{
  "fields": {
    "online_status": "online",
    "pictures": ["projekt_a_1.jpg", "projekt_a_2.jpg"],
    "background_color": "#ffffff",
    "tag_ids": [2, 5],
    "video_id": 12,
    "article_id": 0,
    "external_url": "https://kunde.example",
    "date": "2024-05-01"
  },
  "translations": {
    "1": { "name": "Projekt A", "teaser": "Kurztext", "description": "<p>Beschreibung</p>" },
    "2": { "name": "Project A" }
  }
}
```

Einen Tag anlegen:

```json
{
  "fields": { "picture": "tag_icon.svg" },
  "translations": { "1": { "name": "Industrie" } }
}
```

### Referenz-Felder

| Feld | Typ | Sprachabhängig | SEO | Beschreibung |
| --- | --- | --- | --- | --- |
| `online_status` | `enum:online,offline,archived` | nein | – | Status |
| `pictures` | `media[]` | nein | `image` | Bilder; das erste dient als `og:image` |
| `background_color` | `string` | nein | – | Hintergrundfarbe (Hex) |
| `background_color_dark` | `string` | nein | – | Hintergrundfarbe Dark-Mode (Hex) |
| `video_id` | `int` | nein | – | Verknüpftes d2u_videos-Video (nur mit `d2u_videos`) |
| `article_id` | `int` | nein | – | Verknüpfter REDAXO-Artikel |
| `external_url` | `string` | nein | – | Externe URL |
| `tag_ids` | `int[]` | nein | – | Verknüpfte Tags |
| `date` | `string` | nein | – | Datum (`YYYY-MM-DD`) |
| `name` | `string` | **ja** | `title` | Titel (Meta-Titel) |
| `teaser` | `html` | **ja** | `description` | Kurztext (Meta-Beschreibung) |
| `description` | `html` | **ja** | – | Beschreibung |
| `external_url_lang` | `string` | **ja** | – | Externe URL (sprachspezifisch) |

### Feldtypen

| Typ | Bedeutung |
| --- | --- |
| `string` | Zeichenkette |
| `html` | HTML-Text |
| `int` | Ganzzahl |
| `int[]` | Liste von IDs (z. B. Relationen) |
| `media` | Dateiname aus dem Medienpool |
| `media[]` | Liste von Medienpool-Dateinamen |
| `enum:a,b` | Fester Wertebereich |

## Bilder hochladen

Bilder werden zuerst über den Medien-Endpunkt des `api`-Addons hochgeladen und anschließend per Dateiname referenziert:

1. `POST /api/media` (multipart) im `api`-Addon → liefert den Dateinamen.
2. Den Dateinamen in `pictures` (Liste) bzw. `picture` (Tag, Einzelbild) eintragen.

## Beispiele

Referenzen auflisten:

```bash
curl -H "Authorization: Bearer DEIN_TOKEN" \
  "https://deine-domain.tld/api/d2u_references/references?per_page=20"
```

Referenz anlegen:

```bash
curl -X POST \
  -H "Authorization: Bearer DEIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"fields":{"online_status":"online","tag_ids":[2]},"translations":{"1":{"name":"Projekt A"}}}' \
  https://deine-domain.tld/api/d2u_references/references
```

## Fehlercodes

| Code | Bedeutung |
| --- | --- |
| `400` | Ungültiger Payload, unbekanntes Feld oder fehlendes Pflichtfeld |
| `401` | Kein oder ungültiges Token bzw. fehlender Scope |
| `404` | Ressource nicht verfügbar oder Datensatz nicht gefunden |
| `500` | Interner Fehler (Details im REDAXO-Systemlog) |
