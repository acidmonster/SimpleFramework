<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/engine/classes/CConnection.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/engine/classes/CLogger.php';
include_once 'CCatalogGroup.php';

class CCatalog {

    const GROUP_TABLE_NAME = "sf_catalogs_groups";
    const СATALOG_TABLE_NAME = "sf_catalogs";

    /**
     * ID каталога
     * @var string
     */
    protected $id;

    /**
     * Наименование каталога
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
     * ИД одительской группы
     * @var string
     */
    protected $group_id;

    /**
     * Констурктор
     * @param string $id
     * @param string $name
     * @param string $state
     * @param CCatalogGroup $group_id
     * @param string $comment
     */
    public function __construct($id, $name, $state, $group_id, $comment = "") {
        $this->id = $id;
        $this->name = $name;
        $this->state = $state;
        $this->group_id = $group_id;
        $this->comment = $comment;
    }

    /**
     *
     * @method string getId(void)
     * @method string getName(void)
     * @method string getComment(void)
     * @method string getGroup_id(void)
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
     * Получает родительскую группу
     * @return CCatalogGroup
     */
    public function getParent() {
        // Получить родительскую группу
        $parent = CCatalogGroup::getObjectByID($this->group_id);

        return $parent;
    }

    /**
     * Получает каталог по ИД
     * @param string $id
     * @param boolean $only_active
     * @return СCatalog
     */
    public static function getObjectByID($id, $only_active = TRUE) {
        $con = new CConnection();
        $query = "select id, name, comment, state, group_id from " . self::СATALOG_TABLE_NAME . " where id='" . $id . "'";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $result = $con->query($query);
        unset($con);

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();
            $object = new CCatalog($row->id, $row->name, $row->state, $row->group_id, $row->comment);

            return $object;
        } else {
            CLogger::writeLog("CCatalog::getObjectByName(): Каталог с ИД '". $id . "' не найден.");

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
        $query = "select id, name, comment, state, group_id from " . self::СATALOG_TABLE_NAME . " where LTRIM(name)=LTRIM('" . $name . "')";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $query .= " limit 1";
        $result = $con->query($query);
        unset($con);

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();

            // Получить родительскую группу
            $object = new CCatalog($row->id, $row->name, $row->state, $row->group_id, $row->comment);

            return $object;
        } else {
            CLogger::writeLog("CCatalog::getObjectByName(): Каталог с именем '". $name . "' не найден.");
            return NULL;
        }
    }

    public static function getCatalogsByGroupID($id, $only_active = TRUE) {
        $catalog_list = new CBaseList();
        $con = new CConnection();
        $query = "select id, name, comment, state, group_id from " . self::СATALOG_TABLE_NAME . " where group_id='". $id ."'";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $query .= " order by name";

        if ($result = $con->query($query)) {
            while ($row = $result->fetch_object()) {
                 $catalog = new CCatalog($row->id, $row->name, $row->state, $row->group_id, $row->comment);
                 $catalog_list->add($catalog);
            }

            $result->close();
        } else {
            CLogger::writeLog("CCatalog::getObjectByName(): Ошибка выполнения запроса: ".$con->getError());
            die();
        }

        unset($con);

        return $catalog_list;
    }

}
