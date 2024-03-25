<?php

if ('install/packages/update' !== rex_be_controller::getCurrentPage()) {
    if (false === \TobiasKrais\D2UHelper\Module::isModuleIDInstalled('03-2')) {
        echo rex_view::warning(rex_i18n::msg('d2u_helper_modules_install_module_03_2'));
    }
}
