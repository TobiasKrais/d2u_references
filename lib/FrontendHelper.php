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
}
