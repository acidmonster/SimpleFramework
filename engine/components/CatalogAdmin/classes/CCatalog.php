<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CConnection.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CLogger.php';
//include_once 'CCatalogGroup.php';

class CCatalog {

    const GROUP_TABLE_NAME   = "sf_catalogs_groups";
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
     * @param string $group_id
     * @param string $comment
     */
    public function __construct($id, $name, $state, $group_id, $comment = "") {
        $this->id       = $id;
        $this->name     = $name;
        $this->state    = $state;
        $this->group_id = $group_id;
        $this->comment  = $comment;
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
        $con   = new CConnection();
        $query = "select id, name, comment, state, group_id from " . self::СATALOG_TABLE_NAME . " where id='" . $id . "'";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $result = $con->query($query);
        unset($con);

        if ($result->num_rows > 0) {
            $row    = $result->fetch_object();
            $object = new CCatalog($row->id, $row->name, $row->state, $row->group_id, $row->comment);

            return $object;
        } else {
            CLogger::writeLog("CCatalog::getObjectByName(): Каталог с ИД '" . $id . "' не найден.");

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
            CLogger::writeLog("CCatalog::getObjectByName(): Каталог с именем '" . $name . "' не найден.");
            return NULL;
        }
    }

    public static function getCatalogsByGroupID($id, $only_active = TRUE) {
        $catalog_list = new CBaseList();
        $con          = new CConnection();
        $query        = "select id, name, comment, state, group_id from " . self::СATALOG_TABLE_NAME . " where group_id='" . $id . "'";
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
            CLogger::writeLog("CCatalog::getObjectByName(): Ошибка выполнения запроса: " . $con->getError());
            die();
        }

        unset($con);

        return $catalog_list;
    }

    public static function renderForm($object_id = null) {
        $id       = "";
        $name     = "";
        $comment  = "";
        $group_id = "";
        $options  = "";

        if (isset($object_id)) {
            $catalog = self::getObjectByID($object_id);

            if (isset($catalog)) {
                $id       = $catalog->getId();
                $name     = $catalog->getName();
                $comment  = $catalog->getComment();
                $group_id = $catalog->getGroup_id();
            }
        }

        // Получить список групп
        $conn   = new CConnection();
        if ($result = $conn->query("select id, name from " . self::GROUP_TABLE_NAME . " where state='E' order by name")) {
            while ($row = $result->fetch_object()) {
                if ($row->id == $group_id) {
                    $options .= '<option value="' . $row->id . '" selected>' . $row->name . '</option>';
                } else {
                    $options .= '<option value="' . $row->id . '">' . $row->name . '</option>';
                }
            }

            $result->close();
        } else {
            CLogger::writeLog("CCatalog::renderForm(): Ошибка выполнения запроса: " . $conn->getError());
            die();
        }



        // Карточка группы
        $form = '<div class="sf-form-background"><div class="sf-form-background-grey"></div>
                    <form id="SFCatalogForm" class="modal-form" action="" method="post">
                    <div class="sh-form-frame">
                        <div class="sh-form-panel"><h2>Каталог</h2>
                            <input type="hidden" id="SFCatalogID" value="' . $id . '">
                            <div class="sf-form-error-box"></div>
                            <div class="sf-catalog-label">*Наименование</div>
                            <div class="sf-input">
                                <input type="text" alt="Наименование группы" name="SFCatalogName" id="SFCatalogName" maxlength="50" value="' . $name . '">
                            </div>
                            <div class="sf-catalog-label">*Группа</div>
                            <div class="sf-select">
                                <select id="SFCatalogGroupName">
                                    ' . $options . '
                                </select>
                            </div>
                            <div class="sf-catalog-label">Примечание</div>
                            <div class="sf-input">
                                <textarea alt="Примечание" name="SFCatalogComment" id="SFCatalogComment" maxlength="200">' . $comment . '</textarea>
                            </div>
                            <div class="sf-catalog-footer">
                                <div class="sf-image-button" id="SFSave"><div class="sf-image-button-icon ' . CForms::getButtonStyle(CForms::BUTTON_OK) . '"></div><a href="#">Сохранить</a></div>
                                <div class="sf-image-button" id="SFCancel"><div class="sf-image-button-icon ' . CForms::getButtonStyle(CForms::BUTTON_CANCEL) . '"></div><a href="#">Отмена</a></div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>';

        return $form;
    }

    /**
     * Метод возвращает список каталогов
     * @param boolean $only_active
     * @return \CBaseList
     */
    public static function getCatalogsList($only_active = TRUE) {
        $groups_list = new CBaseList();

        // Получить группы каталогов
        $conn  = new CConnection();

        $query =   "SELECT  g.id group_id,
                            g.name group_name,
                            c.id catalog_id,
                            c.name catalog_name
                    FROM    ".self::СATALOG_TABLE_NAME." c
                    INNER JOIN ".self::GROUP_TABLE_NAME." g ON g.id = c.group_id";


        if ($only_active) {
            $query .= " WHERE g.state='E' AND"
                    . "       c.state='E'";
        }

        $query .= " ORDER BY group_id,
                             group_name,
                             catalog_name";

        $res = $conn->query($query);

        if ($res->num_rows > 0) {
            while ($row = $res->fetch_object()) {
                $group = self::getObjectByID($row->catalog_id);

                if (isset($group)) {
                    $groups_list->add($group);
                }
            }
        }
        $res->close();
        unset($conn);

        return $groups_list;
    }
}
