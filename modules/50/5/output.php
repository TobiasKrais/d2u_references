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

echo '<div class="d2u-references-mod-50-5" data-d2u-reference-filter-root>';
echo \TobiasKrais\D2UReferences\FrontendHelper::getTagFilterMarkup($tags);

echo '<div class="col-12 abstand d2u-references-mod-50-5-list">';
echo '<div class="row g-4 align-items-stretch">';

foreach ($references as $reference) {
    echo '<div class="col-sm-12 col-md-6 col-lg-4 d-flex"'. \TobiasKrais\D2UReferences\FrontendHelper::getReferenceFilterAttributes($reference) .'>';
    $style_vars = [];
    if ('' !== $reference->background_color) {
        $style_vars[] = '--reference-bg-color: '. $reference->background_color;
    }
    if ('' !== $reference->background_color_dark) {
        $style_vars[] = '--reference-bg-color-dark: '. $reference->background_color_dark;
    }
    $box_style = count($style_vars) > 0 ? ' style="'. implode('; ', $style_vars) .';"' : '';
    echo '<div class="reference-box d-flex flex-column h-100 w-100"'. $box_style .'>'; // START reference-box

    if ('' !== $reference->external_url_lang || '' !== $reference->external_url) {
        echo '<a href="'. rex_escape('' !== $reference->external_url_lang ? $reference->external_url_lang : $reference->external_url) .'">';
    }
    echo '<div class="reference-box-media ratio ratio-4x3">';
    if (count($reference->pictures) > 0) {
        echo '<img src="'. rex_escape(rex_media_manager::getUrl('d2u_helper_sm', $reference->pictures[0])) .'" alt="'. rex_escape($reference->name) .'" title="'. rex_escape($reference->name) .'">';
    }
    echo '</div>';

    echo '<div class="reference-box-content d-flex flex-column flex-grow-1">';
    echo '<div class="reference-box-heading-mod-50-1"><b>'. rex_escape($reference->name) .'</b></div>';
    if ('' !== $reference->external_url_lang || '' !== $reference->external_url) {
        echo '</a>';
    }

    echo '<div class="reference-box-text-mod-50-1">';
    echo TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($reference->teaser);
    echo '</div>';
    echo '</div>';

    echo '</div>'; // END reference-box
    echo '</div>';
}
echo '</div>'; // END row
echo '</div>';
echo '</div>';
