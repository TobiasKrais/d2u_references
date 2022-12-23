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
	 * @param Tag $tag_selected Selected Tag object, default is false
	 */
	function printReferenceList($references, $tags, $tag_selected = false) {
		$sprog = rex_addon::get("sprog");
		$tag_open = $sprog->getConfig('wildcard_open_tag');
		$tag_close = $sprog->getConfig('wildcard_close_tag');
		
		$number_references = 'REX_VALUE[2]' > 0 ? 'REX_VALUE[2]' : 6;
		
		// Text
		if ('REX_VALUE[id=1 isset=1]') {
			print '<div class="col-12">';
			echo "REX_VALUE[id=1 output=html]";
			print '</div>';
		}
	
		// Reference List
		$counter = 1;
		foreach($references as $reference) {
			print '<div class="col-6 col-sm-4 col-md-3 col-lg-2 abstand">';
			print '<div class="reference-box">'; // START reference-box

			if(strlen($reference->name) > 10) {
				print '<a href="'. $reference->getURL() .'">';
			}
			print '<div class="reference-box-image">';
			if(count($reference->pictures) > 0) {
				print '<img src="index.php?rex_media_type=d2u_references_list_flat&amp;rex_media_file='. $reference->pictures[0].'" alt="'. $reference->name .'" title="'. $reference->name .'">';
			}
			print '</div>';
			print '<div class="reference-box-heading"><b>'. $reference->name .'</b><br>'
				. d2u_addon_frontend_helper::prepareEditorField($reference->teaser) .'</div>';
			if(strlen($reference->name) > 10) {
				print '</a>';
			}

			print '</div>'; // END reference-box
			print '</div>';
			if($counter >= 'REX_VALUE[2]') {
				break;
			}
			$counter++;
		}
	}
}

// Get placeholder wildcard tags and other presets
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();

$tags = Tag::getAll(rex_clang::getCurrentId(), true);
$tag_selected = false;
$references = [];
if(filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "tag_id") {
	$tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
	if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
		$tag_id = $url_id;
	}
	$tag_selected = new Tag($tag_id, rex_clang::getCurrentId());
	$references = $tag_selected->getReferences();
	printReferenceList($references, $tags, $tag_selected);
}
else if(filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "reference_id") {
	$reference_id = filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
	if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
		$reference_id = $url_id;
	}
	$reference = new Reference($reference_id, rex_clang::getCurrentId());

	$cols_lg = "REX_VALUE[20]";
	if($cols_lg == "") {
		$cols_lg = 8;
	}
	$offset_lg_cols = intval("REX_VALUE[17]");
	$offset_lg = "";
	if($offset_lg_cols > 0) {
		$offset_lg = " mr-lg-auto ml-lg-auto ";
	}
	
	print '<div class="col-12 col-lg-'. $cols_lg . $offset_lg .'">';
	print '<div class="reference-detail">';
	print '<h1>'. $reference->name .'</h1>';
    print d2u_addon_frontend_helper::prepareEditorField($reference->description);
	if($reference->external_url_lang != '' || $reference->external_url != "") {
		print '<a href="'. ($reference->external_url_lang != '' ? $reference->external_url_lang : $reference->external_url) .'">»&nbsp;&nbsp;'. $tag_open .'d2u_references_external_url'. $tag_close .'</a>';
	}
	if(\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && $reference->video !== false) {
		$videomanager = new Videomanager();
		$videomanager->printVideo($reference->video);
	}
	if(count($reference->pictures) > 1) {
		printImages($reference->pictures);
	}
	print '</div>';
	print '</div>';
}
else {
	// Reference list
	if(count($references) == 0) {
		$references = Reference::getAll(rex_clang::getCurrentId(), true);
	}
	
	printReferenceList($references, $tags);
}
?>