<?php

include_once '../../../engine/classes/CConnection.php';
include_once '../../../engine/classes/CLogger.php';

class СCatalogGroup {

    const GROUP_TABLE_NAME = "sf_catalogs_groups";

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
    public static function getGroupByID($id, $only_active = TRUE) {
        $con = new CConnection();
        $query = "select id, name, comment, state from " . self::GROUP_TABLE_NAME . " where id='" . $id . "'";
        
        if ($only_active) {
            $query .= " and state='E'";
        }
        
        $result = $con->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();
            $group = new СCatalogGroup();
            $group->id = $row->id;
            $group->name = $row->name;
            $group->comment = $row->comment;
            $group->state = $row->state;
            
            return $this;
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
    public static function getGroupByName($name, $only_active = TRUE) {
        
        $con = new CConnection();
        $query = "select id, name, comment, state from " . self::GROUP_TABLE_NAME . " where LTRIM(name)=LTRIM('" . $name . "')";
        
        if ($only_active) {
            $query .= " and state='E'";
        }
        
        $query .= " limit 1";        
        $result = $con->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_object();

            $group = new СCatalogGroup();
            $group->id = $row->id;
            $group->name = $row->name;
            $group->comment = $row->comment;
            $group->state = $row->state;
            CLogger::writeLog($result->num_rows);
            return $group;
        } else {
            CLogger::writeLog("Группа с именем '". $name . "' не найдена.");
            return NULL;
        }
    }

}
