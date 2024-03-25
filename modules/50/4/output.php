<?php
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
            echo '<a href="index.php?rex_media_type='. $type_detail .'&rex_media_file='. $pic .'" data-toggle="lightbox'. $lightbox_id .'" data-gallery="example-gallery'. $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
            if ($media instanceof rex_media) {
                echo ' data-title="'. $media->getValue('title') .'"';
            }
            echo '>';
            echo '<img src="index.php?rex_media_type='. $type_thumb .'&rex_media_file='. $pic .'" class="img-fluid gallery-pic-box-rf-mod-4"';
            if ($media instanceof rex_media) {
                echo ' alt="'. $media->getValue('title') .'" title="'. $media->getValue('title') .'"';
            }
            echo '>';
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
        echo '<script>';
        echo "$(document).on('click', '[data-toggle=\"lightbox". $lightbox_id ."\"]', function(event) {";
        echo 'event.preventDefault();';
        echo '$(this).ekkoLightbox({ alwaysShowClose: true	});';
        echo '});';
        echo '</script>';
    }
}

if (!function_exists('printReferenceList_mod_50_4')) {
    /**
     * Prints reference list.
     * @param \TobiasKrais\D2UReferences\Reference[] $references Array with reference objects
     */
    function printReferenceList_mod_50_4($references): void
    {
        echo '<div class="col-12 abstand">';

        // Text
        $heading = 'REX_VALUE[1]';
        if ('' !== $heading) {
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo '<h1 class="heading-rf-mod-4">'. $heading .'</h1>';
            echo '</div>';
            echo '</div>';
        }

        echo '<div class="row">';
        $pic_orientation = 'left';
        foreach ($references as $reference) {
            echo '<div class="col-12">';
            echo '<a href="'. $reference->getUrl() .'">';

            echo '<div class="references-mod-4"'. ('' !== $reference->background_color ? ' style="background-color:'. $reference->background_color .'"' : '') .'>';
            echo '<div class="row">';

            // Picture
            $picture = '<div class="col-12 col-md-6 picbox-'. $pic_orientation .'-outer">';
            if (count($reference->pictures) > 0) {
                $picture .= '<div class="picbox-'. $pic_orientation .'-inner">';
                $picture .= '<div><img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='. $reference->pictures[0] .'"></div>';
                $picture .= '<div class="border-rf-mod-4"'. ('' !== $reference->background_color ? ' style="border-color:'. $reference->background_color .'"' : '') .'></div>';
                $picture .= '</div>';
            }
            $picture .= '</div>';

            // Textbox
            $text = '<div class="col-12 col-md-6">';
            $text .= '<div class="references-content-rf-mod-4">';
            $text .= '<div class="references-title-rf-mod-4">'. $reference->name .'</div>';
            if ('' !== $reference->teaser) {
                $text .= '<div class="references-teaser-rf-mod-4">'. $reference->teaser .'</div>';
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
    $sprog = rex_addon::get('sprog');
    $tag_open = $sprog->getConfig('wildcard_open_tag');
    $tag_close = $sprog->getConfig('wildcard_close_tag');

    $url_namespace = TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
    $url_id = TobiasKrais\D2UHelper\FrontendHelper::getUrlId();

    $tags = \TobiasKrais\D2UReferences\Tag::getAll(rex_clang::getCurrentId(), true);
    $tag_selected = false;
    $references = [];
    if (filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'tag_id' === $url_namespace) {
        $tag_id = (int) filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
        if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
            $tag_id = $url_id;
        }
        $tag_selected = new \TobiasKrais\D2UReferences\Tag($tag_id, rex_clang::getCurrentId());
        $references = $tag_selected->getReferences();
        printReferenceList_mod_50_4($references);
    } elseif (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
        $reference_id = (int) filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
        if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
            $reference_id = $url_id;
        }
        $reference = new \TobiasKrais\D2UReferences\Reference($reference_id, rex_clang::getCurrentId());

        echo '<div class="col-12">';
        echo '<div class="reference-detail">';
        echo '<h1><a href="'. rex_getUrl() .'"><span class="fa-icon fa-back back-spacer"></span></a>'. $reference->name .'</h1>';
        echo TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->description);
        if ('' !== $reference->external_url_lang || '' !== $reference->external_url) {
            echo '<a href="'. ('' !== $reference->external_url_lang ? $reference->external_url_lang : $reference->external_url) .'">»&nbsp;&nbsp;'. $tag_open .'d2u_references_external_url'. $tag_close .'</a>';
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

        printReferenceList_mod_50_4($references);
    }
}
?>