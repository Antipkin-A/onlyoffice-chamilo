<?php

require_once __DIR__.'/../../main/inc/global.inc.php';

$plugin = OnlyofficePlugin::create();

$isEnable = $plugin->get("enableOnlyofficePlugin") === 'true';
if (!$isEnable) {
    die ("Document server is't enable");
    return;
}

$documentServerUrl = $plugin->get("documentServerUrl");
if (empty($documentServerUrl)) {
    die ("Document server is't configured");
    return;
}

$config = [];

$docApiUrl = $documentServerUrl . "/web-apps/apps/api/documents/api.js";

$docId = $_GET["docId"];
$groupId = isset($_GET["groupId"]) && !empty($_GET["groupId"]) ? $_GET["groupId"] : null;

$userId = api_get_user_id();

$userInfo = api_get_user_info($userId);

$sessionId = api_get_session_id();
$courseId = api_get_course_int_id();
$courseInfo = api_get_course_info();
$courseCode = $courseInfo["code"];

$docInfo = DocumentManager::get_document_data_by_id($docId, $courseCode, false, $sessionId);

$extension = strtolower(pathinfo($docInfo["title"], PATHINFO_EXTENSION));

$langId = SubLanguageManager::get_platform_language_id();
$lang = api_get_language_info($langId);

$docType = FileUtility::getDocType($extension);
$key = FileUtility::getKey($courseCode, $docId);
$fileUrl = FileUtility::getFileUrl($courseId, $userId, $docId, $sessionId, $groupId);

$isAllowToEdit = api_is_allowed_to_edit(true, true);
$isMyDir = DocumentManager::is_my_shared_folder($userId, $docInfo["absolute_parent_path"], $sessionId);

$config = [
    "type" => "desktop",
    "documentType" => $docType,
    "document" => [
        "fileType" => $extension,
        "key" => $key,
        "title" => $docInfo["title"],
        "url" => $fileUrl
    ],
    "editorConfig" => [
        "lang" => $lang["isocode"],
        "region" => $lang["isocode"],
        "user" => [
            "id" => $userId,
            "name" => $userInfo["username"]
        ]
    ]
];

$isGroupAccess = false;
if (!empty($groupId)) {
    $groupProperties = GroupManager::get_group_properties($groupId);
    $docInfoGroup = api_get_item_property_info(api_get_course_int_id(), 'document', $docId, $sessionId);
    $isGroupAccess = GroupManager::allowUploadEditDocument($userId, $courseCode, $groupProperties, $docInfoGroup);
}

$accessRights = $isAllowToEdit || $isMyDir || $isGroupAccess ? true : false;
$canEdit = in_array($extension, FileUtility::$can_edit_types) ? true : false;

if ($canEdit && $accessRights) {
    $config["editorConfig"]["mode"] = "edit";
    $config["editorConfig"]["callbackUrl"] = getCallbackUrl($docId, $userId, $courseId, $sessionId, $groupId);
} else {
    $canView = in_array($extension, FileUtility::$can_view_types) ? true : false;
    if ($canView) {
        $config["editorConfig"]["mode"] = "view";
    } else {
        api_not_allowed(true);
    }
}
$config["document"]["permissions"]["edit"] = $accessRights;

/**
 * Return callback url
 * 
 * @param int $docId - identifier of document
 * @param int $userId - identifier of user
 * @param int $courseId - identifier of course
 * @param int $sessionId - identifier of session
 * @param int $groupId - identifier of group or null if file out of group
 * 
 * @return string
 */
function getCallbackUrl($docId, $userId, $courseId, $sessionId, $groupId) {
    $url = "";
    if (!empty($docId) && !empty($userId) && !empty($courseId)) {
        $url = $url . api_get_path(WEB_PLUGIN_PATH) . "onlyoffice/callback.php?type=track"
                                                    . "&courseId=" . $courseId
                                                    . "&userId=" . $userId
                                                    . "&sessionId=" . $sessionId
                                                    . "&docId=" . $docId;
    }

    if (!empty($groupId)) {
        $url = $url . "&groupId=" . $groupId;
    }

    return $url;
}

?>
<title>ONLYOFFICE</title>
<style>
    #app-onlyoffice {
        display: flex;
        min-height: calc(100% - 135px);
        width: 112.1%;
        box-sizing: border-box;
        position: relative;
        margin-left: -69px;
    }
    #app > iframe {
        position: absolute;
        top: -21px;
        left: 0px;
    }
    body {
        height: 100%;
        width: 100%;
        overflow-y: hidden;
    }
    .pull-right,
    .breadcrumb {
        display: none;
    }
    .navbar-default {
        margin-bottom: 0px;
    }
</style>
<script type="text/javascript" src=<?php echo $docApiUrl?>></script>
<script type="text/javascript">
    var onAppReady = function () {
        innerAlert("Document editor ready");
    };
    var connectEditor = function () {
        var config = <?php echo json_encode($config)?>;
        config.events = {
            "onAppReady": onAppReady
        };

        docEditor = new DocsAPI.DocEditor("iframeEditor", config);
    }

    if (window.addEventListener) {
        window.addEventListener("load", connectEditor);
    } else if (window.attachEvent) {
        window.attachEvent("load", connectEditor);
    }

</script>
<?php echo Display::display_header(); ?>
<div id="app-onlyoffice">
    <div id="app">
        <div id="iframeEditor">
        </div>
    </div>
</div>