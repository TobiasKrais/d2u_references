<?php
/**
 * Offers helper functions for frontend.
 */
class d2u_references_frontend_helper
{
    /**
     * Returns alternate URLs. Key is Redaxo language id, value is URL.
     * @return string[] alternate URLs
     */
    public static function getAlternateURLs()
    {
        $alternate_URLs = [];

        // Prepare objects first for sorting in correct order
        $url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
        $url_id = d2u_addon_frontend_helper::getUrlId();

        if (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
            $reference_id = filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $reference_id = $url_id;
            }
            foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                $lang_references = new Reference($reference_id, $this_lang_key);
                if ('delete' != $lang_references->translation_needs_update) {
                    $alternate_URLs[$this_lang_key] = $lang_references->getUrl();
                }
            }
        } elseif (filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'tag_id' === $url_namespace) {
            $tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $tag_id = $url_id;
            }
            foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                $lang_tag = new Tag($tag_id, $this_lang_key);
                if ('delete' != $lang_tag->translation_needs_update) {
                    $alternate_URLs[$this_lang_key] = $lang_tag->getUrl();
                }
            }
        }

        return $alternate_URLs;
    }

    /**
     * Returns breadcrumbs. Not from article path, but only part from this addon.
     * @return string[] Breadcrumb elements
     */
    public static function getBreadcrumbs()
    {
        $breadcrumbs = [];

        // Prepare objects first for sorting in correct order
        $tag = false;
        $reference = false;

        $url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
        $url_id = d2u_addon_frontend_helper::getUrlId();

        if (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
            $reference_id = filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $reference_id = $url_id;
            }
            $reference = new Reference($reference_id, rex_clang::getCurrentId());
        }
        if (filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'tag_id' === $url_namespace) {
            $tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $tag_id = $url_id;
            }
            $tag = new Tag($tag_id, rex_clang::getCurrentId());
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

    /**
     * Returns breadcrumbs. Not from article path, but only part from this addon.
     * @return string[] Breadcrumb elements
     */
    public static function getMetaTags()
    {
        $meta_tags = '';

        // Prepare objects first for sorting in correct order
        $url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
        $url_id = d2u_addon_frontend_helper::getUrlId();

        // References
        if (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
            $reference_id = filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $reference_id = $url_id;
            }
            $reference = new Reference($reference_id, rex_clang::getCurrentId());
            $meta_tags .= $reference->getMetaAlternateHreflangTags();
            $meta_tags .= $reference->getCanonicalTag() . PHP_EOL;
            $meta_tags .= $reference->getMetaDescriptionTag() . PHP_EOL;
            $meta_tags .= $reference->getTitleTag() . PHP_EOL;
        }

        return $meta_tags;
    }
}
