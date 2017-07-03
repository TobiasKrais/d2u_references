<?php
// Get placeholder wildcard tags and other presets
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');
$urlParamKey = "";
if(rex_addon::get("url")->isAvailable()) {
	$url_data = UrlGenerator::getData();
	$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
}

$tags = Tag::getAll(rex_clang::getCurrentId());
$tag_selected = FALSE;
$references = [];
if(filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "tag_id")) {
	$tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
	if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
		$tag_id = UrlGenerator::getId();
	}
	$tag_selected = new Tag($tag_id, rex_clang::getCurrentId());
	$references = $tag_selected->getReferences();
}
else if(filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "reference_id")) {
	header("Location: ". rex_getUrl());
	exit;
}
else {
	$references = Reference::getAll(rex_clang::getCurrentId());
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

foreach($references as $reference) {
	print '<div class="col-sm-12 col-md-6 col-lg-4 abstand">';
	print '<div class="reference-box">'; // START reference-box

	if($reference->external_url != '') {
		print '<a href="'. $reference->external_url .'">';
	}
	if(count($reference->pictures) > 0) {
		print '<img src="index.php?rex_media_type=d2u_helper_sm&amp;rex_media_file='. $reference->pictures[0].'" alt="'. $reference->name .'" title="'. $reference->name .'">';
	}

	print '<div class="reference-box-heading"><b>'. $reference->name .'</b></div>';
	if($reference->external_url != '') {
		print '</a>';
	}

	print '<div class="reference-box-text">';
	print $reference->teaser;
	print '</div>';
	
	print '</div>'; // END reference-box
	print '</div>';
}
?>