# D2U Referenzen - Redaxo Addon

A Redaxo 5 CMS addon for managing and displaying references/testimonials. References can be tagged, filtered, and displayed through various frontend modules.

## Tech Stack

- **Language:** PHP >= 8.0
- **CMS:** Redaxo >= 5.10.0
- **Frontend Framework:** Bootstrap 4 (modules 50-1 to 50-4), Bootstrap 5 (module 50-5)
- **Namespace:** `TobiasKrais\D2UReferences`

## Project Structure

```text
d2u_references/
├── boot.php               # Addon bootstrap (extension points, backend/frontend hooks)
├── install.php             # Installation (database tables, views, media manager types)
├── update.php              # Update (calls install.php)
├── uninstall.php           # Cleanup (database tables, views, media manager, sprog wildcards)
├── package.yml             # Addon configuration, version, dependencies
├── README.md
├── AGENTS.md
├── lang/                   # Backend translations (de_de, en_gb)
├── lib/                    # PHP classes
│   ├── Reference.php       # Reference model
│   ├── Tag.php             # Tag model
│   ├── FrontendHelper.php  # Frontend utilities (alternate URLs, breadcrumbs)
│   ├── LangHelper.php      # Sprog wildcard provider (DE, EN, FR, ES, RU, SK)
│   ├── Module.php          # Module definitions and revisions
│   └── deprecated_classes.php  # Backward compatibility class aliases
├── modules/                # 5 module variants in group 50
│   └── 50/
│       ├── 1/              # Vertikale Referenzboxen ohne Detailansicht
│       ├── 2/              # Horizontale Referenzboxen mit Detailansicht
│       ├── 3/              # Horizontale Mini Referenzboxen mit Detailansicht
│       ├── 4/              # Farbboxen mit seitlichem Bild
│       └── 5/              # Kundenstimmen Carousel (BS5)
└── pages/                  # Backend pages
    ├── index.php           # Page router
    ├── reference.php       # Reference management (create/edit/list)
    ├── tag.php             # Tag management
    ├── settings.php        # Addon settings (article link, languages)
    └── setup.php           # Module manager + changelog
```

## Coding Conventions

- **Namespace:** `TobiasKrais\D2UReferences` for all classes
- **Naming:** camelCase for variables, PascalCase for classes
- **Indentation:** 4 spaces in PHP classes, tabs in module/template files
- **Comments:** English comments only
- **Frontend labels:** Use `Sprog\Wildcard::get()` backed by `LangHelper`, not `rex_i18n::msg()`
- **Backend labels:** Use `rex_i18n::msg()` with keys from `lang/` files

## AGENTS.md Maintenance

- When new project insights are gained during work and they are relevant to agent guidance, workflows, conventions, architecture, or known pitfalls, update this AGENTS.md accordingly.

## Key Classes

| Class | Description |
| ----- | ----------- |
| `Reference` | Reference model: name, teaser, description, pictures, background color, video, tags, URL, online status |
| `Tag` | Tag model: name, picture, associated reference IDs |
| `FrontendHelper` | Frontend utilities: alternate URLs for multilingual pages, breadcrumbs |
| `LangHelper` | Sprog wildcard provider for 6 languages (DE, EN, FR, ES, RU, SK) |
| `Module` | Module definitions and revision numbers |

## Database Tables

| Table | Description |
| ----- | ----------- |
| `rex_d2u_references_references` | Language-independent reference data (pictures, background_color, video_id, article_id, url, online_status, date) |
| `rex_d2u_references_references_lang` | Language-specific reference data (name, teaser, description, url_lang, translation_needs_update) |
| `rex_d2u_references_tags` | Language-independent tag data (picture) |
| `rex_d2u_references_tags_lang` | Language-specific tag data (name, translation_needs_update) |
| `rex_d2u_references_tag2refs` | Many-to-many relation between tags and references |

### Database Views (for URL addon)

- `rex_d2u_references_url_references` — Reference URLs for the URL addon
- `rex_d2u_references_url_tags` — Tag URLs for the URL addon

## Architecture

### Extension Points

| Extension Point | Location | Purpose |
| --------------- | -------- | ------- |
| `D2U_HELPER_ALTERNATE_URLS` | boot.php (frontend) | Provides alternate URLs for references and tags |
| `D2U_HELPER_BREADCRUMBS` | boot.php (frontend) | Provides breadcrumb segments for references and tags |
| `D2U_HELPER_TRANSLATION_LIST` | boot.php (backend) | Registers addon in D2U Helper translation manager |
| `ART_PRE_DELETED` | boot.php (backend) | Prevents deletion of articles used by the addon |
| `CLANG_DELETED` | boot.php (backend) | Cleans up language-specific data when a language is deleted |
| `MEDIA_IS_IN_USE` | boot.php (backend) | Prevents deletion of media files used by references/tags |

### Modules

5 module variants, all in group 50. Each module has:

- `input.php` — Backend input form
- `output.php` — Frontend output
- `style.css` — Optional module CSS (aggregated via `FrontendHelper::getModulesCSS()`)

| Module | Name | Bootstrap | Description |
| ------ | ---- | --------- | ----------- |
| 50-1 | Vertikale Referenzboxen ohne Detailansicht | 4 | Vertical reference boxes, tag filter, no detail view |
| 50-2 | Horizontale Referenzboxen mit Detailansicht | 4 | Horizontal boxes with detail view, year grouping, galleries, video support |
| 50-3 | Horizontale Mini Referenzboxen mit Detailansicht | 4 | Smaller horizontal boxes with detail view, configurable max count |
| 50-4 | Farbboxen mit seitlichem Bild | 4 | Colored boxes with side image, alternating layout |
| 50-5 | Kundenstimmen Carousel (BS5) | 5 | Testimonial carousel using `teaser` as quote and `name` as author, optional tag filter |

#### Module Versioning

Each module has a revision number defined in `lib/Module.php` inside the `getModules()` method. When a module is changed:

1. Add a changelog entry in `pages/setup.php` describing the change.
2. Increment the module's revision number in `Module::getModules()` by one.

**Important:** The revision only needs to be incremented **once per release**, not per commit. To determine whether a release is still in development, check the changelog in `pages/setup.php`: if the version number is followed by `-DEV` (e.g. `1.1.1-DEV`), the release is still in development and no additional revision bump is needed for further changes to the same module. Once a version is released (no `-DEV` suffix), the next change requires a new revision increment.

### Reference Data Model

Key properties of `Reference`:

- `reference_id` (int) — Database ID
- `clang_id` (int) — Language ID
- `name` (string) — Reference name (used as author in testimonial module)
- `teaser` (string) — Short description (used as quote text in testimonial module)
- `description` (string) — Full HTML description
- `pictures` (string[]) — Array of picture filenames
- `background_color` (string) — Hex color code
- `video` (Video|false) — Optional video from d2u_videos addon
- `tag_ids` (int[]) — Array of associated tag IDs
- `online_status` (string) — "online", "offline", or "archived"
- `date` (string) — Date in YYYY-MM-DD format
- `external_url` (string) — External URL
- `external_url_lang` (string) — Language-specific external URL
- `article_id` (int) — Redaxo article ID for internal linking

### Settings

Managed via `pages/settings.php` and stored in `rex_config`:

- **Article ID:** Article where references are displayed (used for URL generation)
- **Languages:** Sprog wildcard installation and language mapping (DE, EN, FR, ES, RU, SK)
- **Wildcard overwrite:** Option to preserve custom Sprog translations on update

## Dependencies

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `d2u_helper` | >= 1.14.0 | Backend/frontend helpers, module manager, translation interface |
| `sprog` | >= 1.0.0 | Frontend translation wildcards |
| `url` | >= 2.0 | SEO-friendly URLs for references and tags |
| `yrewrite` | >= 2.0.1 | URL rewriting and multidomain support |

### Optional Dependencies

| Package | Purpose |
| ------- | ------- |
| `d2u_videos` | Video integration in reference detail views (conflicts with < 1.2) |

## Media Manager Types

| Type | Purpose |
| ---- | ------- |
| `d2u_references_list_flat` | Reference list thumbnails |

## Versioning

This addon follows [Semantic Versioning](https://semver.org/):

- **Major** (1st digit): Breaking changes (e.g. removed classes, renamed methods, incompatible DB changes)
- **Minor** (2nd digit): New features, new modules, new database fields (backward compatible)
- **Patch** (3rd digit): Bug fixes, small improvements (backward compatible)

The version number is maintained in `package.yml`. During development, the changelog in `pages/setup.php` uses a `-DEV` suffix (e.g. `1.2.0-DEV`). The `-DEV` suffix is removed when the version is released. The `package.yml` always contains the final version number without `-DEV`.

## Changelog

The changelog is located in `pages/setup.php`, not in a separate file.
