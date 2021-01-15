<?php

require_once __DIR__.'/fileutility.php';
require_once __DIR__.'/onlyoffice_plugin.php';

class OnlyofficeTools {

    /**
     * Return button-link to onlyoffice editor for file
     * 
     * @param array $document_data - document info
     * 
     * @return Display
     */
    public static function getButtonEdit ($document_data) {

        $plugin = OnlyofficePlugin::create();

        $isEnable = $plugin->get("enableOnlyofficePlugin") === 'true';
        if (!$isEnable) {
            return;
        }

        $urlToEdit = api_get_path(WEB_PLUGIN_PATH) . "onlyoffice/editor.php";

        $extension = strtolower(pathinfo($document_data["title"], PATHINFO_EXTENSION));

        $canEdit = in_array($extension, FileUtility::$can_edit_types) ? true : false;
        $canView = in_array($extension, FileUtility::$can_view_types) ? true : false;

        $groupId = api_get_group_id();
        if (!empty($groupId)) {
            $urlToEdit = $urlToEdit . "?groupId=" . $groupId . "&";
        } else {
            $urlToEdit = $urlToEdit . "?";
        }

        $documentId = $document_data["id"];
        $urlToEdit = $urlToEdit . "docId=" . $documentId;

        $attr = [
            "style" => "float:right; margin-left:5px;",
            "target" => "_blank"
        ];

        if ($canEdit || $canView) {
            return Display::url(Display::return_icon('../../plugin/onlyoffice/resources/onlyoffice_edit.png', $plugin->get_lang('openByOnlyoffice')), $urlToEdit, $attr);
        }
    }
}