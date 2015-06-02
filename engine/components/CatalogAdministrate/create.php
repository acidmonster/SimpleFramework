<?php

include_once '../../classes/CApp.php';
include_once '../../classes/CConnection.php';
include_once '../../classes/CLogger.php';

const TABLE_NAME = "sf_catalogs_groups";

CApp::initialize();

// Получить действие
$action = CApp::readURLProperty("action");

if (isset($action)) {
    switch ($action) {

        case "check_group":
            $groupname = CApp::readURLProperty("SFGroupName");
            echo checkGroupName($groupname);

            break;

        case "create_group":
            $groupname = CApp::readURLProperty("SFGroupName");
            $comment   = CApp::readURLProperty("SFGroupComment");

            if (checkGroupName($groupname)) {
                // Создать запись
                if (!isset($comment))
                    $comment = "";

                $create_query = "insert into " . TABLE_NAME . " ('id', 'name', 'comment') VALUES('" . CApp::getGUID() . "','" . $groupname . "','" . $comment . "')";
                $conn         = new CConnection();

                if ($conn->query($create_query) === TRUE) {
                    echo TRUE;
                } else {
                    echo 'sdfsdf';
                    //echo FALSE;
                }
            } else {
                CLogger::writeLog("Create.php: Группа с названием '" . $groupname . "' уже существует.");
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
function checkGroupName($groupname) {
    if (isset($groupname)) {
        $con    = new CConnection();
        $result = $con->query("select count(1) count from " . TABLE_NAME . " where LTRIM(name) = LTRIM('" . $groupname . "') ");

        if ($result->num_rows > 0) {
            $query_data = $result->fetch_object();

            if ($query_data->count > 0) {
                return false;
            } else {
                return true;
            }
        }
    } else {
        CLogger::writeLog("Crete.php: Не удалось получить наименование группы.");
        die();
    }
}
