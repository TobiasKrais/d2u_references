<?php
if(!function_exists('printImages')) {
	/**
	 * Prints images in Ekko Lightbox module format
	 * @param string[] $pics Array with images
	 */
	function printImages($pics) {
		$type_thumb = "d2u_helper_gallery_thumb";
		$type_detail = "d2u_helper_gallery_detail";
		$lightbox_id = rand();
		
		print '<div class="col-12 print-border">';
		print '<div class="row">';
		foreach($pics as $pic) {
			$media = rex_media::get($pic);
			print '<a href="index.php?rex_media_type='. $type_detail .'&rex_media_file='. $pic .'" data-toggle="lightbox'. $lightbox_id .'" data-gallery="example-gallery'. $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
			if($media instanceof rex_media) {
				print ' data-title="'. $media->getValue('title') .'"';
			}
			print '>';
			print '<img src="index.php?rex_media_type='. $type_thumb .'&rex_media_file='. $pic .'" class="img-fluid gallery-pic-box"';
			if($media instanceof rex_media) {
				print ' alt="'. $media->getValue('title') .'" title="'. $media->getValue('title') .'"';
			}
			print '>';
			print '</a>';
		}
		print '</div>';
		print '</div>';
		print "<script>";
		print "$(document).on('click', '[data-toggle=\"lightbox". $lightbox_id ."\"]', function(event) {";
		print "event.preventDefault();";
		print "$(this).ekkoLightbox({ alwaysShowClose: true	});";
		print "});";
		print "</script>";
	}
}

if(!function_exists('printReferenceList')) {
	/**
	 * Prints reference list
	 * @param Reference[] $references Array with reference objects
	 * @param Tag[] $tags Array with tag objects
	 * @param Tag $tag_selected Selected Tag object, default is FALSE
	 */
	function printReferenceList($references, $tags, $tag_selected = FALSE) {
		$sprog = rex_addon::get("sprog");
		$tag_open = $sprog->getConfig('wildcard_open_tag');
		$tag_close = $sprog->getConfig('wildcard_close_tag');
		// Text
		print '<div class="col-12">';
		if ('REX_VALUE[id=1 isset=1]') {
			echo "REX_VALUE[id=1 output=html]";
		}
		print '</div>';

		// Tags
		if(count($tags) > 0){
			print '<div class="col-12">';
			print '<ul class="tag-list">';
			print '<li'. (($tag_selected === FALSE) ? ' class="active"' : '') .'><span class="icon tags"></span><a href="'. rex_getUrl() .'">'. $tag_open .'d2u_references_all_tags'. $tag_close .'</a></li>';
			foreach($tags as $tag) {
				$class = ($tag_selected !== FALSE && $tag->tag_id == $tag_selected->tag_id) ? ' class="active"' : '';
				print '<li'. $class .'><span class="icon tag"></span><a href="'. $tag->getURL() .'">'. $tag->name .'</a></li>';
			}
			print '</ul>';
			print '</div>';
		}

		// Reference List
		$year = 0;
		foreach($references as $reference) {
			if($year != date("Y", strtotime($reference->date))) {
				$year = date("Y", strtotime($reference->date));
				print '<div class="col-12 abstand">';
				print '<h2 class="section-title">'. $tag_open .'d2u_references_references'. $tag_close .' '. $year .'</h2>';
				print '</div>';
			}
			print '<div class="col-12 col-md-6 abstand">';
			print '<div class="reference-box">'; // START reference-box
			print '<div class="reference-box-heading"><h3>'. $reference->name .'</h3></div>';

			if(strlen($reference->name) > 10) {
				print '<a href="'. $reference->getURL() .'">';
			}
			print '<div class="reference-box-image">';
			if(count($reference->pictures) > 0) {
				print '<img src="index.php?rex_media_type=d2u_references_list_flat&amp;rex_media_file='. $reference->pictures[0].'" alt="'. $reference->name .'" title="'. $reference->name .'">';
			}
			if(strlen($reference->name) > 10) {
				print '<span class="icon go-details"></span>';
			}
			print '</div>';
			if(strlen($reference->name) > 10) {
				print '</a>';
			}

			print '</div>'; // END reference-box
			print '</div>';
		}
	}
}

// Get placeholder wildcard tags and other presets
$urlParamKey = "";
if(rex_addon::get("url")->isAvailable()) {
	$url_data = UrlGenerator::getData();
	$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
}

$tags = Tag::getAll(rex_clang::getCurrentId(), TRUE);
$tag_selected = FALSE;
$references = [];
if(filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "tag_id")) {
	$tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
	if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
		$tag_id = UrlGenerator::getId();
	}
	$tag_selected = new Tag($tag_id, rex_clang::getCurrentId());
	$references = $tag_selected->getReferences();
	printReferenceList($references, $tags, $tag_selected);
}
else if(filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "reference_id")) {
	$reference_id = filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
	if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
		$reference_id = UrlGenerator::getId();
	}
	$reference = new Reference($reference_id, rex_clang::getCurrentId());

	print '<div class="col-12">';
	print '<div class="reference-detail">';
	print '<h1>'. $reference->name .'</h1>';
    print $reference->description;
	if($reference->external_url != "") {
		print '<a href="'. $reference->external_url .'">'. $tag_open .'d2u_references_external_url'. $tag_close .'</a>';
	}
	if(count($reference->pictures) > 0) {
		printImages($reference->pictures);
	}
	print '</div>';
	print '</div>';
}
else {
	// Reference list
	if(count($references) == 0) {
		$references = Reference::getAll(rex_clang::getCurrentId());
	}
	
	printReferenceList($references, $tags);
}
?>