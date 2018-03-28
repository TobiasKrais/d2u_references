<?php
if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_references[]', rex_i18n::msg('d2u_references_rights'));
	rex_perm::register('d2u_references[edit_data]', rex_i18n::msg('d2u_references_rights_edit_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_references[edit_lang]', rex_i18n::msg('d2u_references_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_references[settings]', rex_i18n::msg('d2u_references_rights_settings'), rex_perm::OPTIONS);
}

if(rex::isBackend()) {
	rex_extension::register('ART_PRE_DELETED', 'rex_d2u_references_article_is_in_use');
	rex_extension::register('CLANG_DELETED', 'rex_d2u_references_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_references_media_is_in_use');
}

/**
 * Checks if article is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 * @throws rex_api_exception If article is used
 */
function rex_d2u_references_article_is_in_use(rex_extension_point $ep) {
	$warning = [];
	$params = $ep->getParams();
	$article_id = $params['id'];

	// Prepare warnings
	// Settings
	$addon = rex_addon::get("d2u_references");
	if($addon->hasConfig("article_id") && $addon->getConfig("article_id") == $article_id) {
		$message = '<a href="index.php?page=d2u_references/settings">'.
			 rex_i18n::msg('d2u_references_rights') ." - ". rex_i18n::msg('d2u_references_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}

	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') ."<ul><li>". implode("</li><li>", $warning) ."</li></ul>");
	}
	else {
		return "";
	}
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_references_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$references = Reference::getAll($clang_id, FALSE);
	foreach ($references as $reference) {
		$reference->delete(FALSE);
	}
	$tags = Tag::getAll($clang_id, FALSE);
	foreach ($tags as $tag) {
		$tag->delete(FALSE);
	}

	// Delete language settings
	if(rex_config::has('d2u_references', 'lang_replacement_'. $clang_id)) {
		rex_config::remove('d2u_references', 'lang_replacement_'. $clang_id);
	}
	// Delete language replacements
	d2u_references_lang_helper::factory()->uninstall($clang_id);

	return $warning;
}

/**
 * Checks if media is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_references_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// References
	$sql_references = rex_sql::factory();
	$sql_references->setQuery('SELECT lang.reference_id, name FROM `' . rex::getTablePrefix() . 'd2u_references_references_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_references_references` AS refs ON lang.reference_id = refs.reference_id '
		.'WHERE pictures LIKE "%'. $filename .'%"');  

	// Tags
	$sql_tags = rex_sql::factory();
	$sql_tags->setQuery('SELECT lang.tag_id, name FROM `' . rex::getTablePrefix() . 'd2u_references_tags_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_references_tags` AS tags ON lang.tag_id = tags.tag_id '
		.'WHERE picture = "'. $filename .'"');  

	// Prepare warnings
	// References
	for($i = 0; $i < $sql_references->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_references/reference&func=edit&entry_id='.
			$sql_references->getValue('reference_id') .'\')">'. rex_i18n::msg('d2u_references_rights') ." - ". rex_i18n::msg('d2u_references_references') .': '. $sql_references->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }
	
	// Tags
	for($i = 0; $i < $sql_tags->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_references/tag&func=edit&entry_id='. $sql_tags->getValue('tag_id') .'\')">'.
			 rex_i18n::msg('d2u_references_rights') ." - ". rex_i18n::msg('d2u_references_tags') .': '. $sql_tags->getValue('name') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }

	return $warning;
}