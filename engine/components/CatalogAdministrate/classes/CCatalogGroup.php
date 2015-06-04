<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/engine/classes/CBaseList.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/engine/classes/CConnection.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/engine/classes/CLogger.php';
include_once 'CCatalog.php';

class CCatalogGroup {

    const GROUP_TABLE_NAME = "sf_catalogs_groups";
    const СATALOG_TABLE_NAME = "sf_catalogs";

    /**
     * ID группы
     * @var string
     */
    protected $id;

    /**
     * Наименование группы
     * @var type
     */
    protected $name;

    /**
     * Примечание
     * @var string
     */
    protected $comment;

    /**
     * Состояние записи
     * @var string
     */
    protected $state;

    /**
     *
     * @var CBaseList
     */
    protected $catalogs;


    /**
     *
     * @param string $id
     * @param string $name
     * @param string $state
     * @param string $comment
     */
    public function __construct($id, $name, $state, $comment = "") {
        $this->id = $id;
        $this->name = $name;
        $this->state = $state;
        $this->comment = $comment;

//        // Получить каталоги группы
//        $con = new CConnection();
//        $query = "select id from " . self::СATALOG_TABLE_NAME . " where group_id='" . $id . "'";
//        $result = $con->query($query);
//        unset($con);
//
//        if ($result->num_rows > 0) {
//            while($row = $result->fetch_object()) {
//                $catalog = CCatalog::getObjectByID($row->id);
//
//                if (isset($catalog)) {
//                    $this->catalogs->add($catalog);
//                }
//            }
//
//        }
    }

    /**
     *
     * @method string getId(void)
     * @method string getName(void)
     * @method string getComment(void)
     * @method string getState(void)
     */
    public function __call($method_name, $arguments) {
        $args = preg_split('/(?<=\w)(?=[A-Z])/', $method_name);
        $action = array_shift($args);
        $property_name = strtolower(implode('_', $args));

        switch ($action) {
            case 'get':
                return isset($this->$property_name) ? $this->$property_name : null;

            case 'set':
                $this->$property_name = $arguments[0];
                return $this;
        }
    }

    /**
     * Получает группу каталога по ИД
     * @param string $id
     * @param boolean $only_active
     * @return \СCatalogGroup
     */
    public static function getObjectByID($id, $only_active = TRUE) {
        $con = new CConnection();
        $query = "select id, name, comment, state from " . self::GROUP_TABLE_NAME . " where id='" . $id . "'";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $result = $con->query($query);
        unset($con);

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();
            $object = new CCatalogGroup($row->id, $row->name, $row->state, $row->comment);

            return $object;
        } else {
            CLogger::writeLog("Группа с ИД '". $id . "' не найдена.");

            return NULL;
        }
    }

    /**
     * Получает группу каталогов по имени
     * @param string $name
     * @param string $only_active
     * @return \СCatalogGroup
     */
    public static function getObjectByName($name, $only_active = TRUE) {

        $con = new CConnection();
        $query = "select id, name, comment, state from " . self::GROUP_TABLE_NAME . " where LTRIM(name)=LTRIM('" . $name . "')";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $query .= " limit 1";
        $result = $con->query($query);
        unset($con);

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();
            $object = new CCatalogGroup($row->id, $row->name, $row->state, $row->comment);
            return $object;
        } else {
            CLogger::writeLog("Группа с именем '". $name . "' не найдена.");
            return NULL;
        }

        $result->close();
    }
}
