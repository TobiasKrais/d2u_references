<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_references_references (
	reference_id int(10) unsigned NOT NULL auto_increment,
	pictures text collate utf8mb4_unicode_ci default NULL,
	background_color varchar(7) collate utf8mb4_unicode_ci default NULL,
	video_id int(10) NULL default NULL,
	url varchar(255) collate utf8mb4_unicode_ci default NULL,
	online_status varchar(10) collate utf8mb4_unicode_ci default 'online',
	`date` varchar(10) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (reference_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_references_references_lang (
	reference_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	teaser text collate utf8mb4_unicode_ci default NULL,
	description text collate utf8mb4_unicode_ci default NULL,
	url_lang varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	updatedate DATETIME default NULL,
	PRIMARY KEY (reference_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_references_tags (
	tag_id int(10) unsigned NOT NULL auto_increment,
	picture varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (tag_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_references_tags_lang (
	tag_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	updatedate DATETIME default NULL,
	PRIMARY KEY (tag_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_references_tag2refs (
	tag_id int(10) unsigned NOT NULL,
	reference_id int(10) unsigned NOT NULL,
	PRIMARY KEY (tag_id, reference_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

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
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'reference_id';");
		$sql->setQuery("INSERT INTO `rex_url_generator_profile` (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('reference_id', "
			. $article_id .", "
			. $clang_id .", "
			. "'1_xxx_rex_d2u_references_url_references', "
			. "'{\"column_id\":\"reference_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'tag_id';");
		$sql->setQuery("INSERT INTO `rex_url_generator_profile` (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('tag_id', "
			. $article_id .", "
			. $clang_id .", "
			. "'1_xxx_rex_d2u_references_url_tags', "
			. "'{\"column_id\":\"tag_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.5\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
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
	}

	d2u_addon_backend_helper::generateUrlCache();
}

// Media Manager media types
$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_references_list_flat'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(0, 'd2u_references_list_flat', 'Liste Vorschaubild Modul 50-2');");
	$last_id_d2u_machinery_list_tile = $sql->getLastId();
	$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."media_manager_type_effect` (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		(". $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"125\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rotate\":{\"rex_effect_rotate_rotate\":\"0\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_machinery');");
}

// Insert frontend translations
if(class_exists('d2u_references_lang_helper')) {
	d2u_references_lang_helper::factory()->install();
}

// Init Config
if (!$this->hasConfig()) {
    $this->setConfig('article_id', rex_article::getSiteStartArticleId());
}