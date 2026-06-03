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

if (!function_exists('printReferenceList_mod_50_2')) {
    /**
     * Prints reference list.
     * @param \TobiasKrais\D2UReferences\Reference[] $references Array with reference objects
     * @param \TobiasKrais\D2UReferences\Tag[] $tags Array with tag objects
     */
    function printReferenceList_mod_50_2($references, $tags): void
    {
        echo '<div data-d2u-reference-filter-root>';

        // Text
        echo '<div class="col-12">';
        if ('' !== 'REX_VALUE[id=1 isset=1]') {
            echo 'REX_VALUE[id=1 output=html]';
        }
        echo '</div>';

        echo \TobiasKrais\D2UReferences\FrontendHelper::getTagFilterMarkup($tags);

        // Reference List
        $year = 0;
        $year_group_open = false;
        foreach ($references as $reference) {
            if (strtotime($reference->date) > 0 && $year !== date('Y', strtotime($reference->date))) {
                if ($year_group_open) {
                    echo '</div>';
                }
                $year = date('Y', strtotime($reference->date));
                echo '<div data-d2u-reference-filter-year-group>';
                echo '<div class="col-12 abstand">';
                echo '<h2 class="section-title">'. \Sprog\Wildcard::get('d2u_references_references') .' '. $year .'</h2>';
                echo '</div>';
                $year_group_open = true;
            } elseif (!$year_group_open) {
                echo '<div data-d2u-reference-filter-year-group>';
                $year_group_open = true;
            }
            echo '<div class="col-12 col-lg-6 abstand"'. \TobiasKrais\D2UReferences\FrontendHelper::getReferenceFilterAttributes($reference) .'>';
            echo '<div class="reference-box-mod-50-2">'; // START reference-box
            echo '<div class="reference-box-heading-mod-50-2"><h3>'. rex_escape($reference->name) .'</h3></div>';

            if (strlen($reference->name) > 10) {
                echo '<a href="'. $reference->getUrl() .'">';
            }
            echo '<div class="reference-box-image">';
            if (count($reference->pictures) > 0) {
                echo '<img src="'. rex_media_manager::getUrl('d2u_references_list_flat',  $reference->pictures[0]) .'" alt="'. rex_escape($reference->name, 'html_attr') .'" title="'. rex_escape($reference->name, 'html_attr') .'">';
            }
            if (strlen($reference->name) > 10) {
                echo '<span class="icon go-details"></span>';
            }
            echo '</div>';
            if (strlen($reference->name) > 10) {
                echo '</a>';
            }

            echo '</div>'; // END reference-box
            echo '</div>';
        }

        if ($year_group_open) {
            echo '</div>';
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

    echo '<div class="col-12">';
    echo '<div class="reference-detail">';
    echo '<h1>'. rex_escape($reference->name) .'</h1>';
    echo TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->description);
    if ('' !== $reference->external_url_lang || '' !== $reference->external_url) {
        $external_url = TobiasKrais\D2UHelper\FrontendHelper::sanitizeUrl('' !== $reference->external_url_lang ? $reference->external_url_lang : $reference->external_url);
        if ('' !== $external_url) {
            echo '<a href="'. rex_escape($external_url, 'html_attr') .'">»&nbsp;&nbsp;'. \Sprog\Wildcard::get('d2u_references_external_url') .'</a>';
        }
    }
    if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && false !== $reference->video) {
        $videomanager = new \TobiasKrais\D2UVideos\Videomanager();
        $videomanager->printVideo($reference->video);
    }
    if (count($reference->pictures) > 0) {
        printImages($reference->pictures);
    }
    echo '</div>';
    echo '</div>';
} else {
    // Reference list
    $references = \TobiasKrais\D2UReferences\Reference::getAll(rex_clang::getCurrentId(), true);

    printReferenceList_mod_50_2($references, $tags);
}
