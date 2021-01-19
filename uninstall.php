<?php

/**
 * uninstall the plugin
 */
require_once __DIR__.'/lib/onlyofficePlugin.php';
OnlyofficePlugin::create()->uninstall();