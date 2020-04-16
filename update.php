<?php
// Update language replacements
if(!class_exists('d2u_references_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_references_lang_helper.php';
}
d2u_references_lang_helper::factory()->install();

// Update modules
if(class_exists('D2UModuleManager')) {
	$modules = [];
	$modules[] = new D2UModule("50-1",
		"D2U Referenzen - Vertikale Referenzboxen ohne Detailansicht",
		3);
	$modules[] = new D2UModule("50-2",
		"D2U Referenzen - Horizontale Referenzboxen mit Detailansicht",
		3);
	$modules[] = new D2UModule("50-3",
		"D2U Referenzen - Horizontale Mini Referenzboxen mit Detailansicht",
		3);
	$modules[] = new D2UModule("50-4",
		"D2U Referenzen - Farbboxen mit seitlichem Bild",
		2);
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

// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_references_url_references AS
	SELECT lang.reference_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(refs.pictures, ",", 1) as picture, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_references_references_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_references_references AS refs ON lang.reference_id = refs.reference_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND refs.online_status = "online";');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_references_url_tags AS
	SELECT lang.tag_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.name AS seo_description, tags. picture, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_references_tags_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_references_tags AS tags ON lang.tag_id = tags.tag_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND lang.tag_id IN (SELECT tag_id FROM `'. rex::getTablePrefix() .'d2u_references_tag2refs` GROUP BY tag_id);');

// Insert url schemes
if(\rex_addon::get('url')->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$article_id = rex_config::get('d2u_references', 'article_id', 0) > 0 ? rex_config::get('d2u_references', 'article_id') : rex_article::getSiteStartArticleId(); 
	if(rex_string::versionCompare(\rex_addon::get('url')->getVersion(), '1.5', '>=')) {
		// Insert url schemes Version 2.x
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'reference_id';");
		$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('reference_id', "
			. $article_id .", "
			. $clang_id .", "
			. "'1_xxx_". rex::getTablePrefix() ."d2u_references_url_references', "
			. "'{\"column_id\":\"reference_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'tag_id';");
		$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('tag_id', "
			. $article_id .", "
			. $clang_id .", "
			. "'1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags', "
			. "'{\"column_id\":\"tag_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.5\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
		\d2u_addon_backend_helper::generateUrlCache('reference_id');
		\d2u_addon_backend_helper::generateUrlCache('tag_id');
	}
	else {
		// Insert url schemes Version 1.x
		$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_references_url_references';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". rex::getTablePrefix() ."d2u_references_url_references', "
			. "'{\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_field_1\":\"reference_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_field_2\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_id\":\"reference_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_url_param_key\":\"reference_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_seo_image\":\"picture\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_references_relation_field\":\"\"}', "
			. "'', '[]', 'before', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."')");
		$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags', "
			. "'{\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_field_1\":\"tag_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_field_2\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_id\":\"tag_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_url_param_key\":\"tag_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_seo_image\":\"picture\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_sitemap_priority\":\"0.3\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_references_url_tags_relation_field\":\"\"}', "
			. "'', '[]', 'before', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."');");
		\d2u_addon_backend_helper::generateUrlCache();
	}
	\d2u_addon_backend_helper::update_searchit_url_index();
}

// Update database to 1.0.5
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_references_references` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_references_references_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_references_tags` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_references_tags_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_references_tag2refs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_references_url_tags2ref');

if (rex_string::versionCompare($this->getVersion(), '1.0.5', '<')) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_references_references_lang DROP updateuser;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_references_tags_lang DROP updateuser;");
	
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_references_references_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_references_references_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_references_references_lang DROP updatedate;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_references_references_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;");

	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_references_tags_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_references_tags_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_references_tags_lang DROP updatedate;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_references_tags_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;");
}

// remove default lang setting
if (!$this->hasConfig()) {
	$this->removeConfig('default_lang');
}