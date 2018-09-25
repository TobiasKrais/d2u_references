<?php
/**
 * Offers helper functions for frontend
 */
class d2u_references_frontend_helper {
	/**
	 * Returns alternate URLs. Key is Redaxo language id, value is URL
	 * @return string[] alternate URLs
	 */
	public static function getAlternateURLs() {
		$alternate_URLs = [];

		// Prepare objects first for sorting in correct order
		$urlParamKey = "";
		if(\rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
			$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
		}		
		
		if(filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "reference_id")) {
			$reference_id = filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$reference_id = UrlGenerator::getId();
			}
			foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
				$lang_references = new Reference($reference_id, $this_lang_key);
				if($lang_references->translation_needs_update != "delete") {
					$alternate_URLs[$this_lang_key] = $lang_references->getURL();
				}
			}
		}
		else if(filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "tag_id")) {
			$tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$tag_id = UrlGenerator::getId();
			}
			foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
				$lang_tag = new Tag($tag_id, $this_lang_key);
				if($lang_tag->translation_needs_update != "delete") {
					$alternate_URLs[$this_lang_key] = $lang_tag->getURL();
				}
			}
		}
		
		return $alternate_URLs;
	}

	/**
	 * Returns breadcrumbs. Not from article path, but only part from this addon.
	 * @return string[] Breadcrumb elements
	 */
	public static function getBreadcrumbs() {
		$breadcrumbs = [];

		// Prepare objects first for sorting in correct order
		$tag = FALSE;
		$reference = FALSE;
		$url_data = [];
		if(\rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
		}
		if(filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "reference_id")) {
			$reference_id = filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$reference_id = UrlGenerator::getId();
			}
			$reference = new Reference($reference_id, rex_clang::getCurrentId());
		}
		if(filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "tag_id")) {
			$tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$tag_id = UrlGenerator::getId();
			}
			$tag = new Tag($tag_id, rex_clang::getCurrentId());
		}

		// Breadcrumbs
		if($tag !== FALSE) {
			$breadcrumbs[] = '<a href="' . $tag->getUrl() . '">' . $tag->name . '</a>';
		}
		if($reference !== FALSE) {
			$breadcrumbs[] = '<a href="' . $reference->getUrl() . '">' . $reference->name . '</a>';
		}
		
		return $breadcrumbs;
	}
		
	/**
	 * Returns breadcrumbs. Not from article path, but only part from this addon.
	 * @return string[] Breadcrumb elements
	 */
	public static function getMetaTags() {
		$meta_tags = "";

		// Prepare objects first for sorting in correct order
		$urlParamKey = "";
		if(\rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
			$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
		}

		// References
		if(filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "reference_id")) {
			$reference_id = filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$reference_id = UrlGenerator::getId();
			}
			$reference = new Reference($reference_id, rex_clang::getCurrentId());
			$meta_tags .= $reference->getMetaAlternateHreflangTags();
			$meta_tags .= $reference->getCanonicalTag() . PHP_EOL;
			$meta_tags .= $reference->getMetaDescriptionTag() . PHP_EOL;
			$meta_tags .= $reference->getTitleTag() . PHP_EOL;
		}
		// Tags
		if(filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "tag_id")) {
			$tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$tag_id = UrlGenerator::getId();
			}
			$tag = new Tag($tag_id, rex_clang::getCurrentId());
			$meta_tags .= $tag->getMetaAlternateHreflangTags();
			$meta_tags .= $tag->getCanonicalTag() . PHP_EOL;
			$meta_tags .= $tag->getMetaDescriptionTag() . PHP_EOL;
			$meta_tags .= $tag->getTitleTag() . PHP_EOL;
		}

		return $meta_tags;
	}
}