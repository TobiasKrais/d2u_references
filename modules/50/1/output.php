<?php
// Get placeholder wildcard tags and other presets
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();

$tags = Tag::getAll(rex_clang::getCurrentId());
$tag_selected = FALSE;
$references = [];
if(filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "tag_id") {
	$tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
	if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
		$tag_id = $url_id;
	}
	$tag_selected = new Tag($tag_id, rex_clang::getCurrentId());
	$references = $tag_selected->getReferences();
}
else if(filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "reference_id") {
	header("Location: ". rex_getUrl());
	exit;
}
else {
	$references = Reference::getAll(rex_clang::getCurrentId(), TRUE);
}

if(count($tags) > 0){
	print '<div class="col-12">';
	print '<ul class="tag-list">';
	print '<li'. ($tag_id == 0 ? ' class="active"' : '') .'><span class="icon tags"></span><a href="'. rex_getUrl() .'">'. $tag_open .'d2u_references_all_tags'. $tag_close .'</a></li>';
	foreach($tags as $tag) {
		$class = ($tag_selected !== FALSE && $tag->tag_id == $tag_selected->tag_id) ? ' class="active"' : '';
		print '<li'. $class .'><span class="icon tag"></span><a href="'. $tag->getURL() .'">'. $tag->name .'</a></li>';
	}
	print '</ul>';
	print '</div>';
}

print '<div class="col-12 abstand">';
print '<div class="row" data-match-height>';

foreach($references as $reference) {
	print '<div class="col-sm-12 col-md-6 col-lg-4 abstand">';
	$bg_color = "";
	if($reference->background_color != "") {
		$bg_color = ' style="background-color: '. $reference->background_color .'"';
	}
	print '<div class="reference-box"'. $bg_color .' data-height-watch>'; // START reference-box

	if($reference->external_url_lang != '' || $reference->external_url != '') {
		print '<a href="'. ($reference->external_url_lang != '' ? $reference->external_url_lang : $reference->external_url) .'">';
	}
	if(count($reference->pictures) > 0) {
		print '<img src="index.php?rex_media_type=d2u_helper_sm&amp;rex_media_file='. $reference->pictures[0].'" alt="'. $reference->name .'" title="'. $reference->name .'">';
	}

	print '<div class="reference-box-heading"><b>'. $reference->name .'</b></div>';
	if($reference->external_url_lang != '' || $reference->external_url != '') {
		print '</a>';
	}

	print '<div class="reference-box-text">';
	print d2u_addon_frontend_helper::prepareEditorField($reference->teaser);
	print '</div>';
	
	print '</div>'; // END reference-box
	print '</div>';
}
print '</div>'; // END row
print '</div>';
