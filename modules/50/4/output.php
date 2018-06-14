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
			print '<img src="index.php?rex_media_type='. $type_thumb .'&rex_media_file='. $pic .'" class="img-fluid gallery-pic-box-rf-mod-4"';
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
		
		$number_references = 'REX_VALUE[2]' > 0 ? 'REX_VALUE[2]' : 6;
		
		print '<div class="col-12 abstand">';

		// Text
		$heading = "REX_VALUE[1]";
		if($heading != "") {
			print '<div class="row">';
			print '<div class="col-12">';
			print '<h1 class="heading-rf-mod-4">'. $heading .'</h1>';
			print '</div>';	
			print '</div>';	
		}
		
		print '<div class="row">';
		$pic_orientation = "left";
		foreach($references as $reference) {
			print '<div class="col-12">';
			print '<a href="'. $reference->getURL() .'">';

			print '<div class="references-mod-4"'. ($reference->background_color != '' ? ' style="background-color:'. $reference->background_color .'"' : '') .'>';
			print '<div class="row">';

			// Picture
			$picture = '<div class="col-12 col-md-6 picbox-'. $pic_orientation .'-outer">';
			if(count($reference->pictures) > 0) {
				$picture .= '<div class="picbox-'. $pic_orientation .'-inner">';
				$picture .= '<div><img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='. $reference->pictures[0] .'"></div>';
				$picture .= '<div class="border-rf-mod-4"'. ($reference->background_color != '' ? ' style="border-color:'. $reference->background_color .'"' : '') .'></div>';
				$picture .=  '</div>';
			}
			$picture .=  '</div>';

			// Textbox
			$text = '<div class="col-12 col-md-6">';
			$text .= '<div class="references-content-rf-mod-4">';
			$text .= '<div class="references-title-rf-mod-4">'. $reference->name .'</div>';
			if($reference->teaser != '') {
				$text .= '<div class="references-teaser-rf-mod-4">'. $reference->teaser .'</div>';
			}
//			$external_url = $reference->external_url_lang == "" ? $reference->external_url : $reference->external_url_lang;
			$text .= '</div>';
			$text .= '</div>';
			
			if($pic_orientation == 'left') {
				print $picture. $text;
				$pic_orientation = "right";
			}
			else {
				print $text . $picture;
				$pic_orientation = "left";
			}

			print '</div>';
			print '</div>';

			print '</a>';
			print '</div>';
		}
		
		print '</div>';
		print '</div>';
	}
}

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">Referenzen</h1>
	Überschrift: REX_VALUE[1]<br>
	Maxmiale Anzahl Referenzen: REX_VALUE[2]<br>
<?php
}
else {
	// Get placeholder wildcard tags and other presets
	$sprog = rex_addon::get("sprog");
	$tag_open = $sprog->getConfig('wildcard_open_tag');
	$tag_close = $sprog->getConfig('wildcard_close_tag');
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
		print '<h1><a href="'. rex_getUrl() .'"><span class="fa-icon fa-back back-spacer"></span></a>'. $reference->name .'</h1>';
		print d2u_addon_frontend_helper::prepareEditorField($reference->description);
		if($reference->external_url_lang != '' || $reference->external_url != "") {
			print '<a href="'. ($reference->external_url_lang != '' ? $reference->external_url_lang : $reference->external_url) .'">»&nbsp;&nbsp;'. $tag_open .'d2u_references_external_url'. $tag_close .'</a>';
		}
		if(rex_addon::get('d2u_videos')->isAvailable() && $reference->video !== FALSE) {
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
			$references = Reference::getAll(rex_clang::getCurrentId(), TRUE);
		}

		printReferenceList($references, $tags);
	}
}
?>