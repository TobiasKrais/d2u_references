<?php

if (!rex::isBackend()) {
    echo \TobiasKrais\D2UReferences\FrontendHelper::getTagFilterAssets();
}

// Get placeholder wildcard tags and other presets

$url_namespace = TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
$url_id = TobiasKrais\D2UHelper\FrontendHelper::getUrlId();

$tags = \TobiasKrais\D2UReferences\Tag::getAll(rex_clang::getCurrentId());
$references = [];
if (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
    header('Location: '. rex_getUrl());
    exit;
} else {
    $references = \TobiasKrais\D2UReferences\Reference::getAll(rex_clang::getCurrentId(), true);
}

echo '<div data-d2u-reference-filter-root>';
echo \TobiasKrais\D2UReferences\FrontendHelper::getTagFilterMarkup($tags);

echo '<div class="col-12 abstand">';
echo '<div class="row" data-match-height>';

foreach ($references as $reference) {
    echo '<div class="col-sm-12 col-md-6 col-lg-4 abstand"'. \TobiasKrais\D2UReferences\FrontendHelper::getReferenceFilterAttributes($reference) .'>';
    $bg_color = '';
    if ('' !== $reference->background_color) {
        $bg_color = ' style="background-color: '. $reference->background_color .'"';
    }
    echo '<div class="reference-box"'. $bg_color .' data-height-watch>'; // START reference-box

    if ('' !== $reference->external_url_lang || '' !== $reference->external_url) {
        echo '<a href="'. rex_escape(TobiasKrais\D2UHelper\FrontendHelper::sanitizeUrl('' !== $reference->external_url_lang ? $reference->external_url_lang : $reference->external_url), 'html_attr') .'">';
    }
    if (count($reference->pictures) > 0) {
        echo '<img src="'. rex_media_manager::getUrl('d2u_helper_sm', $reference->pictures[0]) .'" alt="'. rex_escape($reference->name, 'html_attr') .'" title="'. rex_escape($reference->name, 'html_attr') .'">';
    }

    echo '<div class="reference-box-heading-mod-50-1"><b>'. rex_escape($reference->name) .'</b></div>';
    if ('' !== $reference->external_url_lang || '' !== $reference->external_url) {
        echo '</a>';
    }

    echo '<div class="reference-box-text-mod-50-1">';
    echo TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->teaser);
    echo '</div>';

    echo '</div>'; // END reference-box
    echo '</div>';
}
echo '</div>'; // END row
echo '</div>';
echo '</div>';
