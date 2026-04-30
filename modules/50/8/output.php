<?php
if (!rex::isBackend()) {
    echo \TobiasKrais\D2UReferences\FrontendHelper::getTagFilterAssets();
    echo \TobiasKrais\D2UReferences\FrontendHelper::getLightboxAssets();
}

if (!function_exists('printImages')) {
    /**
     * Prints images in Ekko Lightbox module format.
     * @param string[] $pics Array with images
     */
    function printImages($pics): void
    {
        $type_thumb = 'd2u_helper_gallery_thumb';
        $type_detail = 'd2u_helper_gallery_detail';
        $lightbox_id = random_int(0, getrandmax());

        echo '<div class="col-12 print-border">';
        echo '<div class="row">';
        foreach ($pics as $pic) {
            $media = rex_media::get($pic);
            echo '<a href="'. rex_escape(rex_media_manager::getUrl($type_detail, $pic)) .'" data-d2u-gallery="gallery-'. (int) $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
            if ($media instanceof rex_media) {
                echo ' data-title="'. rex_escape((string) $media->getValue('title')) .'"';
            }
            echo ' onclick="event.preventDefault(); d2uLightboxOpen(\'gallery-'. (int) $lightbox_id .'\', this);">';
            echo '<img src="'. rex_escape(rex_media_manager::getUrl($type_thumb, $pic)) .'" class="img-fluid gallery-pic-box-rf-mod-4"';
            if ($media instanceof rex_media) {
                echo ' alt="'. rex_escape((string) $media->getValue('title')) .'" title="'. rex_escape((string) $media->getValue('title')) .'"';
            }
            echo '>';
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('printReferenceList_mod_50_4')) {
    /**
     * Prints reference list.
     * @param \TobiasKrais\D2UReferences\Reference[] $references Array with reference objects
     * @param \TobiasKrais\D2UReferences\Tag[] $tags Array with tag objects
     */
    function printReferenceList_mod_50_4($references, $tags): void
    {
        echo '<div data-d2u-reference-filter-root>';
        echo '<div class="col-12 abstand">';

        // Text
        $heading = 'REX_VALUE[1]';
        if ('' !== $heading) {
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo '<h1 class="heading-rf-mod-4">'. rex_escape($heading) .'</h1>';
            echo '</div>';
            echo '</div>';
        }

        echo '<div class="row">';
        echo \TobiasKrais\D2UReferences\FrontendHelper::getTagFilterMarkup($tags);
        echo '</div>';

        echo '<div class="row">';
        $pic_orientation = 'left';
        foreach ($references as $reference) {
            echo '<div class="col-12"'. \TobiasKrais\D2UReferences\FrontendHelper::getReferenceFilterAttributes($reference) .'>';
            echo '<a href="'. rex_escape($reference->getUrl()) .'">';

            $style_vars = [];
            if ('' !== $reference->background_color) {
                $style_vars[] = '--reference-bg-color: '. $reference->background_color;
                $style_vars[] = '--reference-border-color: '. $reference->background_color;
            }
            if ('' !== $reference->background_color_dark) {
                $style_vars[] = '--reference-bg-color-dark: '. $reference->background_color_dark;
                $style_vars[] = '--reference-border-color-dark: '. $reference->background_color_dark;
            }
            $box_style = count($style_vars) > 0 ? ' style="'. implode('; ', $style_vars) .';"' : '';
            echo '<div class="references-mod-4"'. $box_style .'>';
            echo '<div class="row">';

            // Picture
            $picture = '<div class="col-12 col-md-6 picbox-'. $pic_orientation .'-outer">';
            if (count($reference->pictures) > 0) {
                $picture .= '<div class="picbox-'. $pic_orientation .'-inner">';
                $picture .= '<div><img src="'. rex_escape(rex_media_manager::getUrl('d2u_helper_sm',  $reference->pictures[0])) .'"></div>';
                $picture .= '<div class="border-rf-mod-4"></div>';
                $picture .= '</div>';
            }
            $picture .= '</div>';

            // Textbox
            $text = '<div class="col-12 col-md-6 text-rf-mod-4">';
            $text .= '<div class="references-content-rf-mod-4">';
            $text .= '<div class="references-title-rf-mod-4">'. rex_escape($reference->name) .'</div>';
            if ('' !== $reference->teaser) {
                $text .= '<div class="references-teaser-rf-mod-4">'. TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->teaser) .'</div>';
            }
//			$external_url = $reference->external_url_lang == "" ? $reference->external_url : $reference->external_url_lang;
            $text .= '</div>';
            $text .= '</div>';

            if ('left' === $pic_orientation) {
                echo $picture. $text;
                $pic_orientation = 'right';
            } else {
                echo $text . $picture;
                $pic_orientation = 'left';
            }

            echo '</div>';
            echo '</div>';

            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
	<h1 style="font-size: 1.5em;">Referenzen</h1>
	Überschrift: REX_VALUE[1]<br>
	Maxmiale Anzahl Referenzen: REX_VALUE[2]<br>
<?php
} else {
    // Get placeholder wildcard tags and other presets
    
    $url_namespace = TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
    $url_id = TobiasKrais\D2UHelper\FrontendHelper::getUrlId();

    $tags = \TobiasKrais\D2UReferences\Tag::getAll(rex_clang::getCurrentId(), true);
    $references = [];
    if (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
        $reference_id = (int) filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
        if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
            $reference_id = $url_id;
        }
        $reference = new \TobiasKrais\D2UReferences\Reference($reference_id, rex_clang::getCurrentId());

        echo '<div class="col-12">';
        echo '<div class="reference-detail">';
        echo '<h1><a href="'. rex_escape(rex_getUrl()) .'"><span class="fa-icon fa-back back-spacer"></span></a>'. rex_escape($reference->name) .'</h1>';
        echo TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->description);
        if ('' !== $reference->external_url_lang || '' !== $reference->external_url) {
            echo '<a href="'. rex_escape('' !== $reference->external_url_lang ? $reference->external_url_lang : $reference->external_url) .'">»&nbsp;&nbsp;'. \Sprog\Wildcard::get('d2u_references_external_url') .'</a>';
        }
        if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && false !== $reference->video) {
            $videomanager = new \TobiasKrais\D2UVideos\Videomanager();
            $videomanager->printVideo($reference->video);
        }
        if (count($reference->pictures) > 1) {
            printImages($reference->pictures);
        }
        echo '</div>';
        echo '</div>';
    } else {
        // Reference list
        $references = \TobiasKrais\D2UReferences\Reference::getAll(rex_clang::getCurrentId(), true);

        printReferenceList_mod_50_4($references, $tags);
    }
}
?>