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
        } elseif (filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'tag_id' === $url_namespace) {
            $tag_id = (int) filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $tag_id = $url_id;
            }
            foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                $lang_tag = new \TobiasKrais\D2UReferences\Tag($tag_id, $this_lang_key);
                if ('delete' !== $lang_tag->translation_needs_update) {
                    $alternate_URLs[$this_lang_key] = $lang_tag->getUrl();
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
        $tag = false;
        $reference = false;
        if (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
            $reference_id = (int) filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $reference_id = $url_id;
            }
            $reference = new \TobiasKrais\D2UReferences\Reference($reference_id, rex_clang::getCurrentId());
        }
        if (filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'tag_id' === $url_namespace) {
            $tag_id = (int) filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $tag_id = $url_id;
            }
            $tag = new \TobiasKrais\D2UReferences\Tag($tag_id, rex_clang::getCurrentId());
        }

        // Breadcrumbs
        if (false !== $tag) {
            $breadcrumbs[] = '<a href="' . $tag->getUrl() . '">' . $tag->name . '</a>';
        }
        if (false !== $reference) {
            $breadcrumbs[] = '<a href="' . $reference->getUrl() . '">' . $reference->name . '</a>';
        }

        return $breadcrumbs;
    }
}
