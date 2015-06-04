<?php

include_once '../../classes/CApp.php';
include_once '../../classes/CConnection.php';
include_once '../../classes/CLogger.php';
include_once './classes/CCatalogGroup.php';

        const GROUP_TABLE_NAME = "sf_catalogs_groups";
        const CATALOG_TABLE_NAME = "sf_catalogs";

CApp::initialize();

// Получить действие
$action = CApp::readURLProperty("action");

if (isset($action)) {
    switch ($action) {
        case "getGroupForm":
            $id = CApp::readURLProperty("id");

            if (isset($id)) {
                echo CCatalogGroup::renderForm($id);
            } else {
                echo CCatalogGroup::renderForm();
            }

            break;

        case "validateGroup":
            $group_name = CApp::readURLProperty("SFGroupName");
            $group_id   = CApp::readURLProperty("SFCatalogID");

            echo validateGroupName($group_name, $group_id);
            break;

        case "saveGroup":
            $group_id      = CApp::readURLProperty("SFGroupID");
            $group_name    = CApp::readURLProperty("SFGroupName");
            $group_comment = CApp::readURLProperty("SFGroupComment");

            if (validateGroupName($group_name, $group_id)) {
                CLogger::writeLog("Валидация прошла");
                // Создать запись
                if (!isset($group_comment)) {
                    $group_comment = "";
                }

                if ($group_id == "") {
                    CLogger::writeLog("Не существует");
                    $query = "insert into " . GROUP_TABLE_NAME . " (id, name, comment) VALUES('" . CApp::getGUID() . "','" . $group_name . "','" . $group_comment . "')";
                    CLogger::writeLog($query);
                } else {
                    CLogger::writeLog("Существует");
                    $query = "update " . GROUP_TABLE_NAME . " "
                            . "set  name='" . $group_name . "',"
                            . "comment='" . $group_comment . "' "
                            . "where id='" . $group_id . "'";
                    CLogger::writeLog($query);
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

        case "get_group":
            $group_id = CApp::readURLProperty("SFGroupID");

            if (!isset($group_id)) {
                CLogger::writeLog("Create.php: Не удалось получить ИД группы.");
                echo 'error';
            } else {
                $form = CCatalogGroup::renderForm($group_id);
                echo $from;
            }
            break;

        case "get_groups_list":
            $con    = new CConnection();
            $result = $con->query("select id, name from " . GROUP_TABLE_NAME . " where state='e' order by name");

            if ($result->num_rows > 0) {
                $js = "";

                while ($row = $result->fetch_object()) {
                    $js .= '<option value="' . $row->id . '">' . $row->name . '</option>';
                }

                echo $js;
            } else {
                echo '';
            }

            break;

        case "create_catalog":
            $catalogname = CApp::readURLProperty("SFCatalogName");
            $group_id    = CApp::readURLProperty("SFCatalogGroupName");
            $comment     = CApp::readURLProperty("SFCatalogComment");


            if (validateGroupName($catalogname)) {
                // Создать запись
                if (!isset($comment)) {
                    $comment = "";
                }

                $create_query = "insert into " . CATALOG_TABLE_NAME . " "
                        . "(id, group_id, name, comment) "
                        . "VALUES('" . CApp::getGUID() . "','" . $group_id . "','" . $catalogname . "','" . $comment . "')";
                $con          = new CConnection();

                if ($con->query($create_query) === TRUE) {
                    echo TRUE;
                } else {
                    echo FALSE;
                }
            } else {
                CLogger::writeLog("action.php: Каталог с названием '" . $catalogname . "' уже существует.");
                die();
            }
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
 */
function validateGroupName($groupname, $group_id = "") {
    if (isset($groupname)) {
        $group = CCatalogGroup::getObjectByName($groupname, FALSE);

        if (!isset($group)) {
            return TRUE;
        } else {
            CLogger::writeLog($group_id);
            CLogger::writeLog($group->getId());
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
