<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CApp.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CConnection.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CLogger.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CForms.php';
include_once 'classes/CCatalogGroup.php';

        const GROUP_TABLE_NAME = "sf_catalogs_groups";

CApp::initialize();

// Получить действие
$action = CApp::readURLProperty("action");

if (isset($action)) {
    switch ($action) {
        case "form":
            $id = CApp::readURLProperty("id");

            if (isset($id)) {
                echo CCatalogGroup::renderForm($id);
            } else {
                echo CCatalogGroup::renderForm();
            }

            break;

        case "validate":
            $group_name = CApp::readURLProperty("SFGroupName");
            $group_id   = CApp::readURLProperty("SFGroupID");

            echo validateName($group_name, $group_id);
            break;

        case "save":
            $group_id      = CApp::readURLProperty("SFGroupID");
            $group_name    = CApp::readURLProperty("SFGroupName");
            $group_comment = CApp::readURLProperty("SFGroupComment");

            if (validateName($group_name, $group_id)) {
                // Создать запись
                if (!isset($group_comment)) {
                    $group_comment = "";
                }

                if ($group_id == "") {
                    $query = "insert into " . GROUP_TABLE_NAME . " (id, name, comment) VALUES('" . CApp::getGUID() . "','" . $group_name . "','" . $group_comment . "')";
                } else {
                    $query = "update " . GROUP_TABLE_NAME . " "
                            . "set  name='" . $group_name . "',"
                            . "comment='" . $group_comment . "' "
                            . "where id='" . $group_id . "'";
                }
                $con = new CConnection();

                if ($con->query($query) === TRUE) {
                    echo TRUE;
                } else {
                    echo FALSE;
                }
            } else {
                CLogger::writeLog("action.php: Группа с названием '" . $group_name . "' уже существует.");
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
            $groups_id = CApp::readURLProperty("id");

            if ($groups_id != "") {
                $id_array = explode(",", $groups_id);

                $conn = new CConnection();

                foreach ($id_array as $group_id) {
                    $query = "update " . GROUP_TABLE_NAME . " "
                            . "set  state='D'"
                            . "where id='" . $group_id . "'";

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
function validateName($groupname, $group_id = "") {
    if (isset($groupname)) {
        $group = CCatalogGroup::getObjectByName($groupname, TRUE);

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
