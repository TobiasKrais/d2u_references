<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (intval(filter_input(INPUT_POST, "btn_save")) === 1 || intval(filter_input(INPUT_POST, "btn_apply")) === 1) {
	$form = rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media_list = rex_post('REX_INPUT_MEDIALIST', 'array', []);

	$success = true;
	$reference = false;
	$reference_id = $form['reference_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($reference === false) {
			$reference = new Reference($reference_id, $rex_clang->getId());
			$reference->reference_id = $reference_id; // Ensure correct ID in case first language has no object
			$reference->pictures = preg_grep('/^\s*$/s', explode(",", $input_media_list[1]), PREG_GREP_INVERT);
			$reference->background_color = $form['background_color'];
			$reference->video = $form['video_id'] > 0 ? new Video($form['video_id'], $rex_clang->getId()) : false;
			$reference->external_url = $form['url'];
			$reference->date = $form['date'];
			$reference->online_status = array_key_exists('online_status', $form) ? "online" : "offline";
			$reference->tag_ids = isset($form['tag_ids']) ? $form['tag_ids'] : [];
		}
		else {
			$reference->clang_id = $rex_clang->getId();
		}
		$reference->name = $form['lang'][$rex_clang->getId()]['name'];
		$reference->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
		$reference->description = $form['lang'][$rex_clang->getId()]['description'];
		$reference->external_url_lang = $form['lang'][$rex_clang->getId()]['url_lang'];
		$reference->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		
		if($reference->translation_needs_update === "delete") {
			$reference->delete(false);
		}
		else if($reference->save() > 0){
			$success = false;
		}
		else {
			// remember id, for each database lang object needs same id
			$reference_id = $reference->reference_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(intval(filter_input(INPUT_POST, "btn_apply", FILTER_VALIDATE_INT)) === 1 &&$reference !== false) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$reference->reference_id, "func"=>'edit', "message"=>$message), false));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), false));
	}
	exit;
}
// Delete
else if(intval(filter_input(INPUT_POST, "btn_delete", FILTER_VALIDATE_INT)) === 1 || $func === 'delete') {
	$reference_id = $entry_id;
	if($reference_id === 0) {
		$form = rex_post('form', 'array', []);
		$reference_id = $form['reference_id'];
	}
	$reference = new Reference($reference_id, intval(rex_config::get("d2u_helper", "default_lang")));
	$reference->reference_id = $reference_id; // Ensure correct ID in case first language has no object
	$reference->delete();
	
	$func = '';
}
// Change online status of reference
else if($func === 'changestatus') {
	$reference_id = $entry_id;
	$reference = new Reference($reference_id, intval(rex_config::get("d2u_helper", "default_lang")));
	$reference->reference_id = $reference_id; // Ensure correct ID in case first language has no object
	$reference->changeStatus();

	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Eingabeformular
if ($func === 'edit' || $func === 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_references_reference'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[reference_id]" value="<?php echo $entry_id; ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$reference = new Reference($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() === intval(rex_config::get("d2u_helper", "default_lang")) ? true : false;
						
						$readonly_lang = true;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_references[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = false;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() !== intval(rex_config::get("d2u_helper", "default_lang"))) {
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$reference->translation_needs_update], 1, false, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}
							?>
							<script>
								// Hide on document load
								$(document).ready(function() {
									toggleClangDetailsView(<?php print $rex_clang->getId(); ?>);
								});

								// Hide on selection change
								$("select[name='form[lang][<?php print $rex_clang->getId(); ?>][translation_needs_update]']").on('change', function(e) {
									toggleClangDetailsView(<?php print $rex_clang->getId(); ?>);
								});
							</script>
							<div id="details_clang_<?php print $rex_clang->getId(); ?>">
								<?php
									d2u_addon_backend_helper::form_input('d2u_helper_name', "form[lang][". $rex_clang->getId() ."][name]", $reference->name, $required, $readonly_lang, "text");
									d2u_addon_backend_helper::form_textarea('d2u_references_teaser', "form[lang][". $rex_clang->getId() ."][teaser]", $reference->teaser, 5, false, $readonly_lang, true);
									d2u_addon_backend_helper::form_textarea('d2u_helper_description', "form[lang][". $rex_clang->getId() ."][description]", $reference->description, 5, false, $readonly_lang, true);
									d2u_addon_backend_helper::form_input('d2u_references_url', "form[lang][". $rex_clang->getId() ."][url_lang]", $reference->external_url_lang, false, $readonly_lang, "text");
								?>
							</div>
						</div>
					</fieldset>
				<?php
					}
				?>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_helper_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							// Do not use last object from translations, because you don't know if it exists in DB
							$reference = new Reference($entry_id, intval(rex_config::get("d2u_helper", "default_lang")));
							$readonly = true;
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_references[edit_data]')) {
								$readonly = false;
							}
							
							d2u_addon_backend_helper::form_medialistfield('d2u_helper_pictures', '1', $reference->pictures, $readonly);
							d2u_addon_backend_helper::form_input('d2u_references_background_color', 'form[background_color]', $reference->background_color, false, false, "color");
							d2u_addon_backend_helper::form_input('d2u_references_url', "form[url]", $reference->external_url, false, $readonly, "text");
							d2u_addon_backend_helper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', $reference->online_status === "online", $readonly);
							d2u_addon_backend_helper::form_input('d2u_references_date', "form[date]", $reference->date, true, $readonly, "date");
							$options_tags = [];
							foreach (Tag::getAll(intval(rex_config::get("d2u_helper", "default_lang"))) as $tag) {
								$options_tags[$tag->tag_id] = $tag->name;
							}
							d2u_addon_backend_helper::form_select('d2u_references_tags', 'form[tag_ids][]', $options_tags, $reference->tag_ids, 10, true, $readonly);

							if(\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable()) {
								$options_videos = [0 => rex_i18n::msg('d2u_references_no_video')];
								foreach(Video::getAll(intval(rex_config::get("d2u_helper", "default_lang"))) as $video) {
									$options_videos[$video->video_id] = $video->name;
								}
								d2u_addon_backend_helper::form_select('d2u_videos_video', 'form[video_id]', $options_videos, [($reference->video !== false ? $reference->video->video_id : 0)], 1, false, $readonly);
							}
						?>
					</div>
				</fieldset>
			</div>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?php echo rex_i18n::msg('form_save'); ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?php echo rex_i18n::msg('form_apply'); ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?php echo rex_i18n::msg('form_abort'); ?></button>
						<?php
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_references[edit_data]')) {
								print '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
							}
						?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
		print d2u_addon_backend_helper::getCSS();
		print d2u_addon_backend_helper::getJS();
}

if ($func === '') {
	$query = 'SELECT refs.reference_id, name, `date`, online_status '
		. 'FROM '. rex::getTablePrefix() .'d2u_references_references AS refs '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_references_references_lang AS lang '
			. 'ON refs.reference_id = lang.reference_id AND lang.clang_id = '. intval(rex_config::get("d2u_helper", "default_lang")) .' '
		.'ORDER BY `date` DESC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-thumbs-o-up"></i>';
	$thIcon = "";
	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_references[edit_data]')) {
	    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###reference_id###']);

    $list->setColumnLabel('reference_id', rex_i18n::msg('id'));
    $list->setColumnLayout('reference_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###reference_id###']);

    $list->setColumnLabel('date', rex_i18n::msg('d2u_references_date'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###reference_id###']);

	$list->removeColumn('online_status');
	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_references[edit_data]')) {
		$list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###reference_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
		$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###reference_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_references_no_references_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_references_references'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}