<?php
if(rex_be_controller::getCurrentPage() != "install/packages/update") {
	if(D2UModule::isModuleIDInstalled("03-2") === FALSE) {
		print rex_view::warning(rex_i18n::msg('d2u_helper_modules_install_module_03_2'));
	}
}