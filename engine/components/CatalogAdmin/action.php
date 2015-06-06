<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CApp.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CConnection.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CLogger.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CForms.php';
include_once 'classes/CCatalog.php';

        const CATALOG_TABLE_NAME = "sf_catalogs";

CApp::initialize();

// Получить действие
$action = CApp::readURLProperty("action");

if (isset($action)) {
    switch ($action) {
        case "form":
            $id = CApp::readURLProperty("id");

            if (isset($id)) {
                echo CCatalog::renderForm($id);
            } else {
                echo CCatalog::renderForm();
            }

            break;

        case "validate":
            $catalog_name = CApp::readURLProperty("SFCatalogName");
            $catalog_id   = CApp::readURLProperty("SFCatalogID");

            echo validateName($catalog_name, $catalog_id);
            break;

        case "save":

            $catalog_id       = CApp::readURLProperty("SFCatalogID");
            $catalog_group_id = CApp::readURLProperty("SFCatalogGroupName");
            $catalog_name     = CApp::readURLProperty("SFCatalogName");
            $catalog_comment  = CApp::readURLProperty("SFCatalogComment");

            if (validateName($catalog_name, $catalog_id)) {
                // Создать запись
                if (!isset($catalog_comment)) {
                    $catalog_comment = "";
                }

                if ($catalog_id == "") {
                    $query = "insert into " . CATALOG_TABLE_NAME . " (id, group_id, name, comment) VALUES('" . CApp::getGUID() . "', '" .$catalog_group_id. "', '" . $catalog_name . "','" . $catalog_comment. "')";
                } else {
                    $query = "update " . CATALOG_TABLE_NAME . " "
                            . "set  name='" . $catalog_name . "',"
                            . "group_id='" . $catalog_group_id . "',"
                            . "comment='" . $catalog_comment . "' "
                            . "where id='" . $catalog_id . "'";
                }
                $con = new CConnection();

                if ($con->query($query) === TRUE) {
                    echo TRUE;
                } else {
                    echo FALSE;
                }
            } else {
                CLogger::writeLog("action.php: Каталог с названием '" . $group_name . "' уже существует.");
                die();
            }
            break;

        case "confirmDelete":
            echo CForms::message_box("SFConfirmForm",
                    CForms::CONFIRM_CAPTION,
                    "Удалить выбранные записи?",
                    CForms::BUTTON_OK . ":SFConfYes:Да|" . CForms::BUTTON_CANCEL . ":SFConfNo:Нет");
            break;

        case "delete":
            $catalogs_id = CApp::readURLProperty("id");

            if ($catalogs_id != "") {
                $id_array = explode(",", $catalogs_id);

                $conn = new CConnection();

                foreach ($id_array as $catalog_id) {
                    $query = "update " . CATALOG_TABLE_NAME . " "
                            . "set  state='D'"
                            . "where id='" . $catalog_id . "'";

                    if (!($res = $conn->query($query))) {
                        CLogger::writeLog("Create.php: Ошбика при удалении группы: " . $conn->getError());
                        die();
                    }
                }

                unset($conn);
            }

            echo TRUE;

            break;

        default:
            break;
    }
} else {
    CLogger::writeLog("Create.php: Не удалось получить действие action при работе с группой/каталогом.");
    die();
}

/**
 * Проверяет имя группы на совпадение
 * @param boolean $groupname
 * @param string $group_id
 */
function validateGroupName($groupname, $group_id = "") {
    if (isset($groupname)) {
        $group = CCatalogGroup::getObjectByName($groupname, FALSE);

        if (!isset($group)) {
            return TRUE;
        } else {
            if (($group_id == $group->getId()) && ($group_id != "")) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    } else {
        CLogger::writeLog("action.php: Не удалось получить наименование группы.");
        die();
    }
}

/**
 * Проверяет имя каталога на совпадение
 * @param boolean $catalogname
 * @param string $catalog_id
 */
function validateName($catalogname, $catalog_id = "") {
    if (isset($catalogname)) {
        $catalog = CCatalog::getObjectByName($catalogname, FALSE);

        if (!isset($catalog)) {
            return TRUE;
        } else {
            if (($catalog_id == $catalog->getId()) && ($catalog_id != "")) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    } else {
        CLogger::writeLog("action.php: Не удалось получить наименование каталога.");
        die();
    }
}
