<?php

\rex_sql_table::get(\rex::getTable('d2u_references_references'))
    ->ensureColumn(new rex_sql_column('reference_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('reference_id')
    ->ensureColumn(new \rex_sql_column('pictures', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('background_color', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('background_color_dark', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('video_id', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('article_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('url', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', false, 'online'))
    ->ensureColumn(new \rex_sql_column('date', 'VARCHAR(10)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_references_references_lang'))
    ->ensureColumn(new rex_sql_column('reference_id', 'INT(11)', false, null))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, (string) rex_clang::getStartId()))
    ->setPrimaryKey(['reference_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('url_lang', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_references_tags'))
    ->ensureColumn(new rex_sql_column('tag_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('tag_id')
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_references_tags_lang'))
    ->ensureColumn(new rex_sql_column('tag_id', 'INT(11)', false, null))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, (string) rex_clang::getStartId()))
    ->setPrimaryKey(['tag_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_references_tag2refs'))
    ->ensureColumn(new rex_sql_column('tag_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('reference_id', 'INT(11)', false))
    ->setPrimaryKey(['tag_id', 'reference_id'])
    ->ensure();

$sql = rex_sql::factory();
// References no longer generate their own detail URLs: drop the url addon views.
$sql->setQuery('DROP VIEW IF EXISTS '. rex::getTablePrefix() .'d2u_references_url_references;');
$sql->setQuery('DROP VIEW IF EXISTS '. rex::getTablePrefix() .'d2u_references_url_tags;');
// Remove any url addon profiles that older versions created for references.
if (\rex_addon::get('url')->isAvailable()) {
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'reference_id';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'tag_id';");
}

// Media Manager media types
$sql->setQuery('SELECT * FROM '. rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_references_list_flat'");
if (0 === $sql->getRows()) {
    $sql->setQuery('INSERT INTO '. rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(0, 'd2u_references_list_flat', 'Liste Vorschaubild Modul 50-2');");
    $last_id_list_tile = $sql->getLastId();
    $sql->setQuery('INSERT INTO `'. rex::getTablePrefix() .'media_manager_type_effect` (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		('. $last_id_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"125\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rotate\":{\"rex_effect_rotate_rotate\":\"0\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_references');");
}

// Insert frontend translations
if (!class_exists(TobiasKrais\D2UReferences\LangHelper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/LangHelper.php';
}
\TobiasKrais\D2UReferences\LangHelper::factory()->install();

// Update modules
include __DIR__ . DIRECTORY_SEPARATOR .'lib'. DIRECTORY_SEPARATOR .'Module.php';
$d2u_module_manager = new \TobiasKrais\D2UHelper\ModuleManager(\TobiasKrais\D2UReferences\Module::getModules(), '', 'd2u_references');
$d2u_module_manager->autoupdate();

// Init Config
if (!rex_config::has('d2u_references', 'article_id')) {
    rex_config::set('d2u_references', 'article_id', rex_article::getSiteStartArticleId());
}
