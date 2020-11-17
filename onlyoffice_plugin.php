<?php

/**
 * Plugin class for the Onlyoffice plugin.
 *
 * @author Asensio System SIA
 */
class OnlyofficePlugin extends Plugin
{
    /**
     * OnlyofficePlugin constructor.
     */
    protected function __construct()
    {
        parent::__construct(
            "1.0",
            "Asensio System SIA",
            [
                "enableOnlyofficePlugin" => "boolean",
                "documentServerUrl" => "text"
            ]
        );
    }

    /**
     * Create OnlyofficePlugin
     * 
     * @return OnlyofficePlugin
     */
    public static function create()
    {
        static $result = null;

        return $result ?: $result = new self();
    }

    /**
     * This method install the plugin tables.
     */
    public function install()
    {

    }

    /**
     * This method drops the plugin tables.
     */
    public function uninstall()
    {

    }
}
