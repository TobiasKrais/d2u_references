<?php
// Update language replacements
d2u_references_lang_helper::factory()->install();

// Update modules
if(class_exists('D2UModuleManager')) {
	$modules = [];
	$modules[] = new D2UModule("50-1",
		"D2U Referenzen - Vertikale Referenzboxen ohne Detailansicht",
		2);
	$modules[] = new D2UModule("50-2",
		"D2U Referenzen - Horizontale Referenzboxen mit Detailansicht",
		2);
	$modules[] = new D2UModule("50-3",
		"D2U Referenzen - Horizontale Mini Referenzboxen mit Detailansicht",
		2);
	$modules[] = new D2UModule("50-4",
		"D2U Referenzen - Farbboxen mit seitlichem Bild",
		1);
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_references");
	$d2u_module_manager->autoupdate();
}

// 1.0.1 Update database: add video support
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_references_references LIKE 'video_id';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_references_references "
		. "ADD video_id INT(10) NULL DEFAULT NULL AFTER pictures;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_references_references LIKE 'background_color';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_references_references "
		. "ADD background_color VARCHAR(7) NULL DEFAULT NULL AFTER pictures;");
}

if(\rex_addon::get("url")->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$sql_replace = rex_sql::factory();
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_references_url_references'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_references_url_references';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_references_url_references', '{\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_field_1\":\"reference_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_field_2\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_id\":\"reference_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_url_param_key\":\"reference_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_references_addon_installer', UNIX_TIMESTAMP(), 'd2u_references_addon_installer')");
	}
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_references_url_tags';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags', '{\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_field_1\":\"tag_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_field_2\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_id\":\"tag_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_url_param_key\":\"tag_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_sitemap_priority\":\"0.3\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_references_addon_installer', UNIX_TIMESTAMP(), 'd2u_references_addon_installer');");
	}

	UrlGenerator::generatePathFile([]);
}

// remove default lang setting
if (!$this->hasConfig()) {
	$this->removeConfig('default_lang');
}