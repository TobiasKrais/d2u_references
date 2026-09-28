<?php
/* d2u_translate: 1:html */

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

            $reference_link = $reference->getLinkUrl();
            if ('' !== $reference_link) {
                echo '<a href="'. rex_escape($reference_link) .'">';
            }
            echo '<div class="reference-box-image">';
            if (count($reference->pictures) > 0) {
                echo '<img src="'. rex_media_manager::getUrl('d2u_references_list_flat',  $reference->pictures[0]) .'" alt="'. rex_escape($reference->name, 'html_attr') .'" title="'. rex_escape($reference->name, 'html_attr') .'">';
            }
            echo '</div>';
            echo '<div class="reference-box-heading-mod-50-3"><b>'. rex_escape($reference->name) .'</b><br>'
                . TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->teaser) .'</div>';
            if ('' !== $reference_link) {
                echo '</a>';
            }

            echo '</div>'; // END reference-box
            echo '</div>';
            if ($counter >= 'REX_VALUE[2]') {
                break;
            }
            ++$counter;
        }
    }
}

// Get placeholder wildcard tags and other presets

$url_namespace = TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
$url_id = TobiasKrais\D2UHelper\FrontendHelper::getUrlId();

$tags = \TobiasKrais\D2UReferences\Tag::getAll(rex_clang::getCurrentId(), true);

// Reference list
$references = \TobiasKrais\D2UReferences\Reference::getAll(rex_clang::getCurrentId(), true);

printReferenceList_mod_50_3($references, $tags);
