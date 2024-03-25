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
            echo '<img src="index.php?rex_media_type='. $type_thumb .'&rex_media_file='. $pic .'" class="img-fluid gallery-pic-box"';
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
     * @param \TobiasKrais\D2UReferences\Tag|false $tag_selected Selected Tag object, default is false
     */
    function printReferenceList_mod_50_2($references, $tags, $tag_selected = false): void
    {
        $sprog = rex_addon::get('sprog');
        $tag_open = $sprog->getConfig('wildcard_open_tag');
        $tag_close = $sprog->getConfig('wildcard_close_tag');
        // Text
        echo '<div class="col-12">';
        if ('' !== 'REX_VALUE[id=1 isset=1]') {
            echo 'REX_VALUE[id=1 output=html]';
        }
        echo '</div>';

        // Tags
        if (count($tags) > 0) {
            echo '<div class="col-12">';
            echo '<ul class="tag-list">';
            echo '<li'. ((false === $tag_selected) ? ' class="active"' : '') .'><span class="icon tags"></span><a href="'. rex_getUrl() .'">'. $tag_open .'d2u_references_all_tags'. $tag_close .'</a></li>';
            foreach ($tags as $tag) {
                $class = (false !== $tag_selected && $tag->tag_id === $tag_selected->tag_id) ? ' class="active"' : '';
                echo '<li'. $class .'><span class="icon tag"></span><a href="'. $tag->getUrl() .'">'. $tag->name .'</a></li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        // Reference List
        $year = 0;
        foreach ($references as $reference) {
            if (strtotime($reference->date) > 0 && $year !== date('Y', strtotime($reference->date))) {
                $year = date('Y', strtotime($reference->date));
                echo '<div class="col-12 abstand">';
                echo '<h2 class="section-title">'. $tag_open .'d2u_references_references'. $tag_close .' '. $year .'</h2>';
                echo '</div>';
            }
            echo '<div class="col-12 col-lg-6 abstand">';
            echo '<div class="reference-box-mod-50-2">'; // START reference-box
            echo '<div class="reference-box-heading-mod-50-2"><h3>'. $reference->name .'</h3></div>';

            if (strlen($reference->name) > 10) {
                echo '<a href="'. $reference->getUrl() .'">';
            }
            echo '<div class="reference-box-image">';
            if (count($reference->pictures) > 0) {
                echo '<img src="index.php?rex_media_type=d2u_references_list_flat&amp;rex_media_file='. $reference->pictures[0].'" alt="'. $reference->name .'" title="'. $reference->name .'">';
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
    }
}

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
    printReferenceList_mod_50_2($references, $tags, $tag_selected);
} elseif (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
    $reference_id = (int) filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $reference_id = $url_id;
    }
    $reference = new \TobiasKrais\D2UReferences\Reference($reference_id, rex_clang::getCurrentId());

    echo '<div class="col-12">';
    echo '<div class="reference-detail">';
    echo '<h1>'. $reference->name .'</h1>';
    echo TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->description);
    if ('' !== $reference->external_url_lang || '' !== $reference->external_url) {
        echo '<a href="'. ('' !== $reference->external_url_lang ? $reference->external_url_lang : $reference->external_url) .'">»&nbsp;&nbsp;'. $tag_open .'d2u_references_external_url'. $tag_close .'</a>';
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
