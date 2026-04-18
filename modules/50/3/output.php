<?php

if (!rex::isBackend()) {
    echo \TobiasKrais\D2UReferences\FrontendHelper::getTagFilterAssets();
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
            echo '<a href="'. rex_media_manager::getUrl($type_detail, $pic) .'" data-toggle="lightbox'. $lightbox_id .'" data-gallery="example-gallery'. $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
            if ($media instanceof rex_media) {
                echo ' data-title="'. $media->getValue('title') .'"';
            }
            echo '>';
            echo '<img src="'. rex_media_manager::getUrl($type_thumb, $pic) .'" class="img-fluid gallery-pic-box"';
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

if (!function_exists('printReferenceList_mod_50_3')) {
    /**
     * Prints reference list.
     * @param \TobiasKrais\D2UReferences\Reference[] $references Array with reference objects
     * @param \TobiasKrais\D2UReferences\Tag[] $tags Array with tag objects
     */
    function printReferenceList_mod_50_3($references, $tags): void
    {
        echo '<div data-d2u-reference-filter-root>';

        // Text
        if ('' !== 'REX_VALUE[id=1 isset=1]') {
            echo '<div class="col-12">';
            echo 'REX_VALUE[id=1 output=html]';
            echo '</div>';
        }

        echo \TobiasKrais\D2UReferences\FrontendHelper::getTagFilterMarkup($tags);

        // Reference List
        $counter = 1;
        foreach ($references as $reference) {
            echo '<div class="col-6 col-sm-4 col-md-3 col-lg-2 abstand"'. \TobiasKrais\D2UReferences\FrontendHelper::getReferenceFilterAttributes($reference) .'>';
            echo '<div class="reference-box">'; // START reference-box

            if (strlen($reference->name) > 10) {
                echo '<a href="'. $reference->getUrl() .'">';
            }
            echo '<div class="reference-box-image">';
            if (count($reference->pictures) > 0) {
                echo '<img src="'. rex_media_manager::getUrl('d2u_references_list_flat',  $reference->pictures[0]) .'" alt="'. $reference->name .'" title="'. $reference->name .'">';
            }
            echo '</div>';
            echo '<div class="reference-box-heading-mod-50-3"><b>'. $reference->name .'</b><br>'
                . TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->teaser) .'</div>';
            if (strlen($reference->name) > 10) {
                echo '</a>';
            }

            echo '</div>'; // END reference-box
            echo '</div>';
            if ($counter >= 'REX_VALUE[2]') {
                break;
            }
            ++$counter;
        }

        echo '</div>';
    }
}

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

    $cols_lg = 'REX_VALUE[20]';
    if ('' === $cols_lg) {
        $cols_lg = 8;
    }
    $offset_lg_cols = (int) 'REX_VALUE[17]';
    $offset_lg = '';
    if ($offset_lg_cols > 0) { /** @phpstan-ignore-line */
        $offset_lg = ' mr-lg-auto ml-lg-auto ';
    }

    echo '<div class="col-12 col-lg-'. $cols_lg . $offset_lg .'">';
    echo '<div class="reference-detail">';
    echo '<h1>'. $reference->name .'</h1>';
    echo TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->description);
    if ('' !== $reference->external_url_lang || '' !== $reference->external_url) {
        echo '<a href="'. ('' !== $reference->external_url_lang ? $reference->external_url_lang : $reference->external_url) .'">»&nbsp;&nbsp;'. \Sprog\Wildcard::get('d2u_references_external_url') .'</a>';
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

    printReferenceList_mod_50_3($references, $tags);
}
