<?php
if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_references[]', rex_i18n::msg('d2u_references_rights'));
	rex_perm::register('d2u_references[edit_data]', rex_i18n::msg('d2u_references_rights_edit_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_references[edit_lang]', rex_i18n::msg('d2u_references_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_references[settings]', rex_i18n::msg('d2u_references_rights_settings'), rex_perm::OPTIONS);
}