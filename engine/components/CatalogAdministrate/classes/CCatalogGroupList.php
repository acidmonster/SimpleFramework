<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/engine/classes/CApp.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/engine/classes/CBaseList.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/engine/classes/CConnection.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/engine/classes/CLogger.php';
include_once 'CCatalogGroup.php';


class CCatalogGroupList extends CBaseList {

    const GROUP_TABLE_NAME = "sf_catalogs_groups";
    const СATALOG_TABLE_NAME = "sf_catalogs";

    public function __construct($only_active = TRUE) {
        parent::__construct();

        // Получить группы каталогов
        $conn = new CConnection();
        $query = "select id from " . self::GROUP_TABLE_NAME;

        if ($only_active) {
            $query .= " where state='E'";
        }

        $query .= " order by name";

        $res = $conn->query($query);

        if ($res->num_rows > 0) {
            while($row = $res->fetch_object()) {
                $group = CCatalogGroup::getObjectByID($row->id);

                if (isset($group)) {
                    $this->add($group);
                }
            }
        }
        $res->close();
        unset($conn);
    }
}
