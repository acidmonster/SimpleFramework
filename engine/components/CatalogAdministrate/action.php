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

            case "check_group":
                $groupname = CApp::readURLProperty("SFGroupName");
                echo checkGroupName($groupname);

                break;

            case "create_group":
                $groupname = CApp::readURLProperty("SFGroupName");
                $comment = CApp::readURLProperty("SFGroupComment");


                if (checkGroupName($groupname)) {
                    // Создать запись
                    if (!isset($comment)) {
                        $comment = "";
                    }

                    $create_query = "insert into " . GROUP_TABLE_NAME . " (id, name, comment) VALUES('" . CApp::getGUID() . "','" . $groupname . "','" . $comment . "')";
                    $con = new CConnection();

                    if ($con->query($create_query) === TRUE) {
                        echo TRUE;
                    } else {
                        echo FALSE;
                    }
                } else {
                    CLogger::writeLog("action.php: Группа с названием '" . $groupname . "' уже существует.");
                    die();
                }
                break;

            case "get_group":
                $group_id = CApp::readURLProperty("SFGroupID");

                if (!isset($group_id)) {
                    CLogger::writeLog("Create.php: Не удалось получить ИД группы.");
                    echo 'error';
                } else {
                    $con = new CConnection();
                    $result = $con->query("select id, name, comment from " . GROUP_TABLE_NAME . " where id='" . $group_id . "' limit 1");

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_object();

                        // JSON
                        $js = "<id>" . $row->id . "</id>"
                                . "<name>" . $row->name . "</name>"
                                . "<comment>" . $row->comment . "</comment>";

                        echo $js;
                    } else {
                        CLogger::writeLog("Create.php: Группа с ИД " . $group_id . " не найдена.");
                        echo 'error';
                    }
                }
                break;

            case "get_groups_list":
                $con = new CConnection();
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
                $group_id = CApp::readURLProperty("SFCatalogGroupName");
                $comment = CApp::readURLProperty("SFCatalogComment");


                if (checkGroupName($catalogname)) {
                    // Создать запись
                    if (!isset($comment)) {
                        $comment = "";
                    }

                    $create_query = "insert into " . CATALOG_TABLE_NAME . " "
                            . "(id, group_id, name, comment) "
                            . "VALUES('" . CApp::getGUID() . "','" . $group_id . "','" . $catalogname . "','" . $comment . "')";
                    $con = new CConnection();

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
    function checkGroupName($groupname) {
        if (isset($groupname)) {
            $group = СCatalogGroup::getGroupByName($groupname, FALSE);

            if (!isset($group)) {
                return true;
            } else {
                return false;
            }
                
        } else {
            CLogger::writeLog("action.php: Не удалось получить наименование группы.");
            die();
        }
    }
    