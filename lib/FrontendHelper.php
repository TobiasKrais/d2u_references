<?php

namespace TobiasKrais\D2UReferences;

use rex_clang;

/**
 * Offers helper functions for frontend.
 * @api
 */
class FrontendHelper
{
    /**
     * Returns required tag filter assets only once per request.
     */
    public static function getTagFilterAssets(): string
    {
        static $assetsLoaded = false;

        if ($assetsLoaded) {
            return '';
        }

        $assetsLoaded = true;

        $version = urlencode((string) \rex_addon::get('d2u_references')->getVersion());

        return '<script src="'. \rex_url::addonAssets('d2u_references', 'tag-filter.js') .'?v='. $version .'"></script>';
    }

    /**
     * Returns required lightbox assets only once per request.
     */
    public static function getLightboxAssets(): string
    {
        static $assetsLoaded = false;

        if ($assetsLoaded) {
            return '';
        }

        $assetsLoaded = true;

        $version = urlencode((string) \rex_addon::get('d2u_references')->getVersion());

        return '<link rel="stylesheet" href="'. \rex_url::addonAssets('d2u_references', 'lightbox.css') .'?v='. $version .'">'
            .'<script src="'. \rex_url::addonAssets('d2u_references', 'lightbox.js') .'?v='. $version .'"></script>';
    }

    /**
     * Returns tag filter markup for reference list modules.
     * @param Tag[] $tags Array with tag objects
     */
    public static function getTagFilterMarkup(array $tags): string
    {
        if (0 === count($tags)) {
            return '';
        }

        $html = '<div class="col-12 d2u-references-tag-filter-row">';
        $html .= '<ul class="tag-list" data-d2u-reference-filter-nav>';
        $html .= '<li class="active"><span class="icon tags"></span><a href="#" data-d2u-reference-filter-tag="all">'. \Sprog\Wildcard::get('d2u_references_all_tags') .'</a></li>';
        foreach ($tags as $tag) {
            if (0 >= $tag->tag_id || '' === trim($tag->name)) {
                continue;
            }
            $html .= '<li><span class="icon tag"></span><a href="#" data-d2u-reference-filter-tag="'. $tag->tag_id .'">'. \rex_escape($tag->name) .'</a></li>';
        }
        $html .= '</ul>';
        $html .= '<div class="clearfix"></div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Returns HTML data attributes for a reference list item.
     */
    public static function getReferenceFilterAttributes(Reference $reference): string
    {
        $tag_ids = array_map('intval', $reference->tag_ids);

        return ' data-d2u-reference-filter-item data-d2u-reference-filter-tags="'. implode(',', $tag_ids) .'"';
    }

    /**
     * Returns alternate URLs. Key is Redaxo language id, value is URL.
     * @param ?string $url_namespace URL namespace
     * @param ?int $url_id URL id
     * @return array<int,string> alternate URLs
     */
    public static function getAlternateURLs($url_namespace = null, $url_id = null)
    {
        if (null === $url_namespace) {
            $url_namespace = \TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
        }
        if (null === $url_id) {
            $url_id = \TobiasKrais\D2UHelper\FrontendHelper::getUrlId();
        }
        $alternate_URLs = [];

        // Prepare objects first for sorting in correct order
        if (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
            $reference_id = (int) filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $reference_id = $url_id;
            }
            foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                $lang_references = new \TobiasKrais\D2UReferences\Reference($reference_id, $this_lang_key);
                if ('delete' !== $lang_references->translation_needs_update) {
                    $alternate_URLs[$this_lang_key] = $lang_references->getUrl();
                }
            }
        }

        return $alternate_URLs;
    }

    /**
     * Returns breadcrumbs. Not from article path, but only part from this addon.
     * @param ?string $url_namespace URL namespace
     * @param ?int $url_id URL id
     * @return array<int,string> Breadcrumb elements
     */
    public static function getBreadcrumbs($url_namespace = null, $url_id = null)
    {
        if (null === $url_namespace) {
            $url_namespace = \TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
        }
        if (null === $url_id) {
            $url_id = \TobiasKrais\D2UHelper\FrontendHelper::getUrlId();
        }
        $breadcrumbs = [];

        // Prepare objects first for sorting in correct order
        $reference = false;
        if (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
            $reference_id = (int) filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $reference_id = $url_id;
            }
            $reference = new \TobiasKrais\D2UReferences\Reference($reference_id, rex_clang::getCurrentId());
        }

        // Breadcrumbs
        if (false !== $reference) {
            $breadcrumbs[] = '<a href="' . $reference->getUrl() . '">' . $reference->name . '</a>';
        }

        return $breadcrumbs;
    }
}
