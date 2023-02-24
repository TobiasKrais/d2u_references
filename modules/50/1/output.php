<?php

// Get placeholder wildcard tags and other presets
$sprog = rex_addon::get('sprog');
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();

$tag_id = 0;
$tags = Tag::getAll(rex_clang::getCurrentId());
$tag_selected = false;
$references = [];
if (filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'tag_id' === $url_namespace) {
    $tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $tag_id = $url_id;
    }
    $tag_selected = new Tag($tag_id, rex_clang::getCurrentId());
    $references = $tag_selected->getReferences();
} elseif (filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'reference_id' === $url_namespace) {
    header('Location: '. rex_getUrl());
    exit;
} else {
    $references = Reference::getAll(rex_clang::getCurrentId(), true);
}

if (count($tags) > 0) {
    echo '<div class="col-12">';
    echo '<ul class="tag-list">';
    echo '<li'. (0 === $tag_id ? ' class="active"' : '') .'><span class="icon tags"></span><a href="'. rex_getUrl() .'">'. $tag_open .'d2u_references_all_tags'. $tag_close .'</a></li>';
    foreach ($tags as $tag) {
        $class = (false !== $tag_selected && $tag->tag_id == $tag_selected->tag_id) ? ' class="active"' : '';
        echo '<li'. $class .'><span class="icon tag"></span><a href="'. $tag->getUrl() .'">'. $tag->name .'</a></li>';
    }
    echo '</ul>';
    echo '</div>';
}

echo '<div class="col-12 abstand">';
echo '<div class="row" data-match-height>';

foreach ($references as $reference) {
    echo '<div class="col-sm-12 col-md-6 col-lg-4 abstand">';
    $bg_color = '';
    if ('' != $reference->background_color) {
        $bg_color = ' style="background-color: '. $reference->background_color .'"';
    }
    echo '<div class="reference-box"'. $bg_color .' data-height-watch>'; // START reference-box

    if ('' != $reference->external_url_lang || '' != $reference->external_url) {
        echo '<a href="'. ('' != $reference->external_url_lang ? $reference->external_url_lang : $reference->external_url) .'">';
    }
    if (count($reference->pictures) > 0) {
        echo '<img src="index.php?rex_media_type=d2u_helper_sm&amp;rex_media_file='. $reference->pictures[0].'" alt="'. $reference->name .'" title="'. $reference->name .'">';
    }

    echo '<div class="reference-box-heading"><b>'. $reference->name .'</b></div>';
    if ('' != $reference->external_url_lang || '' != $reference->external_url) {
        echo '</a>';
    }

    echo '<div class="reference-box-text">';
    echo d2u_addon_frontend_helper::prepareEditorField($reference->teaser);
    echo '</div>';

    echo '</div>'; // END reference-box
    echo '</div>';
}
echo '</div>'; // END row
echo '</div>';
