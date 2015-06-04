<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CBaseList.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CConnection.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CLogger.php';
include_once 'CCatalog.php';

class CCatalogGroup {

    const GROUP_TABLE_NAME   = "sf_catalogs_groups";
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
        $this->id      = $id;
        $this->name    = $name;
        $this->state   = $state;
        $this->comment = $comment;
    }

    /**
     *
     * @method string getId(void)
     * @method string getName(void)
     * @method string getComment(void)
     * @method string getState(void)
     */
    public function __call($method_name, $arguments) {
        $args          = preg_split('/(?<=\w)(?=[A-Z])/', $method_name);
        $action        = array_shift($args);
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
        $con   = new CConnection();
        $query = "select id, name, comment, state from " . self::GROUP_TABLE_NAME . " where id='" . $id . "'";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $result = $con->query($query);
        unset($con);

        if ($result->num_rows > 0) {
            $row    = $result->fetch_object();
            $object = new CCatalogGroup($row->id, $row->name, $row->state, $row->comment);

            return $object;
        } else {
            CLogger::writeLog("Группа с ИД '" . $id . "' не найдена.");

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

        $con   = new CConnection();
        $query = "select id, name, comment, state from " . self::GROUP_TABLE_NAME . " where LTRIM(name)=LTRIM('" . $name . "')";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $query .= " limit 1";
        $result = $con->query($query);

        if ($result->num_rows > 0) {
            $row    = $result->fetch_object();
            $object = new CCatalogGroup($row->id, $row->name, $row->state, $row->comment);
            return $object;
        } else {
            CLogger::writeLog("Группа с именем '" . $name . "' не найдена.");
            return NULL;
        }

        $result->close();
        unset($con);
    }

    public static function renderForm($object_id = null) {
        $id      = "";
        $name    = "";
        $comment = "";

        if (isset($object_id)) {
            $group = self::getObjectByID($object_id);

            if (isset($group)) {
                $id      = $group->getId();
                $name    = $group->getName();
                $comment = $group->getComment();
            }
        }



        // Карточка группы
        $form = '<div class="sf-catalog-background"><div class="sf-catalog-background-grey"></div>
                <form id="SFGroupForm" action="" method="post">
                <div class="sh-catalog-frame">
                    <div class="sh-catalog-form"><h2>Группа каталога</h2>
                        <input type="hidden" id="SFCatalogAdminAction" value="AddGroup">
                        <input type="hidden" id="SFGroupID" value="' . $id . '">
                        <div class="sf-catalog-error-box"></div>
                        <div class="sf-catalog-label">*Наименование</div>
                         <div class="sf-input">
                            <input type="text" alt="Наименование группы" name="SFGroupName" id="SFGroupName" maxlength="50" value="' . $name . '">
                        </div>
                        <div class="sf-catalog-label">Примечание</div>
                        <div class="sf-input">
                            <textarea alt="Примечание" name="SFGroupComment" id="SFGroupComment" maxlength="200">' . $comment . '</textarea>
                        </div>
                        <div class="sf-catalog-footer">
                            <div class="sf-image-button" id="SFSaveGroup"><div class="sf-image-button-icon save-catalog-item"></div><a href="#">Сохранить</a></div>
                            <div class="sf-image-button" id="SFCancelGroup"><div class="sf-image-button-icon cancel-catalog-item"></div><a href="#">Отмена</a></div>
                        </div>
                    </div>
                </div>
                </form>
                </div>';

        return $form;
    }

}
