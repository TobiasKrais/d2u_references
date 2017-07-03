<?php
/**
 * Offers helper functions for frontend
 */
class d2u_references_frontend_helper {
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
		if(rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
		}
		if(filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "reference_id")) {
			$reference_id = filter_input(INPUT_GET, 'reference_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$reference_id = UrlGenerator::getId();
			}
			$reference = new Reference($reference_id, rex_clang::getCurrentId());
		}
		if(filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "tag_id")) {
			$tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
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
}