<?php

use TobiasKrais\D2UReferences\FrontendHelper;

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('d2u_references[]', rex_i18n::msg('d2u_references_rights'));
    rex_perm::register('d2u_references[edit_data]', rex_i18n::msg('d2u_references_rights_edit_data'), rex_perm::OPTIONS);
    rex_perm::register('d2u_references[edit_lang]', rex_i18n::msg('d2u_references_rights_edit_lang'), rex_perm::OPTIONS);
    rex_perm::register('d2u_references[settings]', rex_i18n::msg('d2u_references_rights_settings'), rex_perm::OPTIONS);
}

if (rex::isBackend()) {
    rex_extension::register('ART_PRE_DELETED', rex_d2u_references_article_is_in_use(...));
    rex_extension::register('CLANG_DELETED', rex_d2u_references_clang_deleted(...));
    rex_extension::register('D2U_HELPER_TRANSLATION_LIST', rex_d2u_references_translation_list(...));
    rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_references_media_is_in_use(...));
}
else {
    rex_extension::register('D2U_HELPER_ALTERNATE_URLS', rex_d2u_references_alternate_urls(...));
    rex_extension::register('D2U_HELPER_BREADCRUMBS', rex_d2u_references_breadcrumbs(...));
}

/**
 * Get alternate URLs for jobs.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<int,string> Addon url list
 */
function rex_d2u_references_alternate_urls(rex_extension_point $ep): array
{
    $params = $ep->getParams();
    $url_namespace = (string) $params['url_namespace'];
    $url_id = (int) $params['url_id'];

    $url_list = FrontendHelper::getAlternateURLs($url_namespace, $url_id);
    if (count($url_list) === 0 && $ep->getSubject() !== null) {
        $url_list = $ep->getSubject();
    }

    return $url_list;
}

/**
 * Checks if article is used by this addon.
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @throws rex_api_exception If article is used
 * @return string Warning message
 */
function rex_d2u_references_article_is_in_use(rex_extension_point $ep): string
{
    $warning = [];
    $params = $ep->getParams();
    $article_id = $params['id'];

    // Prepare warnings
    // Settings
    $addon = rex_addon::get('d2u_references');
    if ($addon->hasConfig('article_id') && (int) $addon->getConfig('article_id') === $article_id) {
        $message = '<a href="index.php?page=d2u_references/settings">'.
             rex_i18n::msg('d2u_references_rights') .' - '. rex_i18n::msg('d2u_references_settings') . '</a>';
            $warning[] = $message;
    }

    if (count($warning) > 0) {
        throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') .'<ul><li>'. implode('</li><li>', $warning) .'</li></ul>');
    }

    return '';
}

/**
 * Get breadcrumb part for jobs.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<int,string> HTML formatted breadcrumb elements
 */
function rex_d2u_references_breadcrumbs(rex_extension_point $ep) {
    $params = $ep->getParams();
    $url_namespace = (string) $params['url_namespace'];
    $url_id = (int) $params['url_id'];

    $breadcrumbs = FrontendHelper::getBreadcrumbs($url_namespace, $url_id);
    if (count($breadcrumbs) === 0) {
        $breadcrumbs = $ep->getSubject();
    }

    return $breadcrumbs;
}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_references_clang_deleted(rex_extension_point $ep): array
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = (int) $params['id'];

    // Delete
    $references = \TobiasKrais\D2UReferences\Reference::getAll($clang_id, false);
    foreach ($references as $reference) {
        $reference->delete(false);
    }
    $tags = \TobiasKrais\D2UReferences\Tag::getAll($clang_id, false);
    foreach ($tags as $tag) {
        $tag->delete(false);
    }

    // Delete language settings
    if (rex_config::has('d2u_references', 'lang_replacement_'. $clang_id)) {
        rex_config::remove('d2u_references', 'lang_replacement_'. $clang_id);
    }
    // Delete language replacements
    \TobiasKrais\D2UReferences\LangHelper::factory()->uninstall($clang_id);

    return $warning;
}

/**
 * Checks if media is used by this addon.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_references_media_is_in_use(rex_extension_point $ep): array
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = addslashes((string) $params['filename']);

    // References
    $sql_references = rex_sql::factory();
    $sql_references->setQuery('SELECT lang.reference_id, name FROM `' . rex::getTablePrefix() . 'd2u_references_references_lang` AS lang '
        .'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_references_references` AS refs ON lang.reference_id = refs.reference_id '
        .'WHERE FIND_IN_SET("'. $filename .'", pictures)');

    // Tags
    $sql_tags = rex_sql::factory();
    $sql_tags->setQuery('SELECT lang.tag_id, name FROM `' . rex::getTablePrefix() . 'd2u_references_tags_lang` AS lang '
        .'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_references_tags` AS tags ON lang.tag_id = tags.tag_id '
        .'WHERE picture = "'. $filename .'"');

    // Prepare warnings
    // References
    for ($i = 0; $i < $sql_references->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_references/reference&func=edit&entry_id='.
            $sql_references->getValue('reference_id') .'\')">'. rex_i18n::msg('d2u_references_rights') .' - '. rex_i18n::msg('d2u_references_references') .': '. $sql_references->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_references->next();
    }

    // Tags
    for ($i = 0; $i < $sql_tags->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_references/tag&func=edit&entry_id='. $sql_tags->getValue('tag_id') .'\')">'.
             rex_i18n::msg('d2u_references_rights') .' - '. rex_i18n::msg('d2u_references_tags') .': '. $sql_tags->getValue('name') . '</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_tags->next();
    }

    return $warning;
}

/**
 * Addon translation list.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<array<string,array<int,array<string,string>>|string>|string> Addon translation list
 */
function rex_d2u_references_translation_list(rex_extension_point $ep): array
{
    $params = $ep->getParams();
    $source_clang_id = (int) $params['source_clang_id'];
    $target_clang_id = (int) $params['target_clang_id'];
    $filter_type = (string) $params['filter_type'];

    $list = $ep->getSubject();
    $list_entry = [
        'addon_name' => rex_i18n::msg('d2u_references'),
        'pages' => []
    ];

    $references = \TobiasKrais\D2UReferences\Reference::getTranslationHelperObjects($target_clang_id, $filter_type);
    if (count($references) > 0) {
        $html_references = '<ul>';
        foreach ($references as $reference) {
            if ('' === $reference->name) {
                $reference = new \TobiasKrais\D2UReferences\Reference($reference->reference_id, $source_clang_id);
            }
            $html_references .= '<li><a href="'. rex_url::backendPage('d2u_references/reference', ['entry_id' => $reference->reference_id, 'func' => 'edit']) .'">'. $reference->name .'</a></li>';
        }
        $html_references .= '</ul>';
        
        $list_entry['pages'][] = [
            'title' => rex_i18n::msg('d2u_references'),
            'icon' => 'fa-thumbs-o-up',
            'html' => $html_references
        ];
    }

    $tags = \TobiasKrais\D2UReferences\Tag::getTranslationHelperObjects($target_clang_id, $filter_type);
    if (count($tags) > 0) {
        $html_tags = '<ul>';
        foreach ($tags as $tag) {
            if ('' === $tag->name) {
                $tag = new \TobiasKrais\D2UReferences\Tag($tag->tag_id, $source_clang_id);
            }
            $html_tags .= '<li><a href="'. rex_url::backendPage('d2u_references/tag', ['entry_id' => $tag->tag_id, 'func' => 'edit']) .'">'. $tag->name .'</a></li>';
        }
        $html_tags .= '</ul>';
        
        $list_entry['pages'][] = [
            'title' => rex_i18n::msg('d2u_references_tags'),
            'icon' => 'fa-tags',
            'html' => $html_tags
        ];
    }

    $list[] = $list_entry;

    return $list;
}