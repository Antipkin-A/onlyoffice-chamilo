<?php

require_once __DIR__.'/HookDocumentItemActionOnlyoffice.php';

/**
 * Plugin class for the Onlyoffice plugin.
 *
 * @author Asensio System SIA
 */
class OnlyofficePlugin extends Plugin implements HookPluginInterface
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
        $this->installHook();
    }

    /**
     * This method drops the plugin tables.
     */
    public function uninstall()
    {
        $this->uninstallHook();
    }

    /**
     * Install the create hooks.
     */
    public function installHook()
    {
        $observer = HookDocumentItemActionOnlyoffice::create();
        HookDocumentItemAction::create()->attach($observer);
    }

    /**
     * Uninstall the create hooks.
     */
    public function uninstallHook()
    {
        $observer = HookDocumentItemActionOnlyoffice::create();
        $event = HookDocumentItemAction::create()->detach($observer);
    }
}
