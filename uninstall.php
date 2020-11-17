<?php

/**
 * uninstall the plugin
 */
require_once __DIR__.'/onlyoffice_plugin.php';
OnlyofficePlugin::create()->uninstall();