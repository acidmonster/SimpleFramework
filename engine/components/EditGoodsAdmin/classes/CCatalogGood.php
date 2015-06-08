<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CBaseList.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CConnection.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CLogger.php';

class CCatalogGood {

    const GOODS_TABLE_NAME   = "sf_catalogs_goods";
    const PHOTOS_TABLE_NAME = "sf_goods_photos";
    const CATALOG_TABLE_NAME = "sf_catalogs";

    /**
     * ID товара
     * @var string
     */
    protected $id;

    /**
     * Наименование товара
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
     * ИД Каталога
     * @var string
     */
    protected $catalog_id;

    /**
     * Описание товара
     * @var string
     */
    protected $description;

    /**
     * Стоимость
     * @var string
     */
    protected $cost;

    /**
     *
     * @param string $id
     * @param string $name
     * @param string $state
     * @param string $comment
     */
    public function __construct($id, $ctalog_id, $name, $cost, $description, $state, $comment = "") {
        $this->id          = $id;
        $this->catalog_id  = $ctalog_id;
        $this->name        = $name;
        $this->state       = $state;
        $this->comment     = $comment;
        $this->cost        = $cost;
        $this->description = $description;
    }

    /**
     *
     * @method string getId(void)
     * @method string getCatalog_id(void)
     * @method string getName(void)
     * @method string getCost(void)
     * @method string getDescription(void)
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
     * Получает товар по ИД
     * @param string $id
     * @param boolean $only_active
     * @return \CGood
     */
    public static function getObjectByID($id, $only_active = TRUE) {
        $con   = new CConnection();
        $query = "select id, catalog_id, name, cost, description, comment, state from " . self::CATALOG_TABLE_NAME . " where id='" . $id . "'";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $result = $con->query($query);
        unset($con);

        if ($result->num_rows > 0) {
            $row    = $result->fetch_object();
            $object = new CCatalogGood($row->id, $row->catalog_id, $row->name, $row->cost, $row->description, $row->state, $row->comment);

            return $object;
        } else {
            CLogger::writeLog("Товар с ИД '" . $id . "' не найдена.");

            return NULL;
        }
    }

    /**
     * Получает товар по имени
     * @param string $name
     * @param string $only_active
     * @return \CCatalogGood
     */
    public static function getObjectByName($name, $only_active = TRUE) {

        $con   = new CConnection();
        $query = "select id, catalog_id, name, cost, description, comment, state from " . self::CATALOG_TABLE_NAME . " where LTRIM(name)=LTRIM('" . $name . "')";

        if ($only_active) {
            $query .= " and state='E'";
        }

        $query .= " limit 1";
        $result = $con->query($query);

        if ($result->num_rows > 0) {
            $row    = $result->fetch_object();
            $object = new CCatalogGood($row->id, $row->catalog_id, $row->name, $row->cost, $row->description, $row->state, $row->comment);
            return $object;
        } else {
            CLogger::writeLog("Товар с именем '" . $name . "' не найдена.");
            return NULL;
        }

        $result->close();
        unset($con);
    }

    public static function renderForm($object_id = null) {
        $title           = "Добавление нового товара";
        $id              = "";
        $name            = "";
        $cost            = "";
        $p_description   = "";
        $description     = "";
        $comment         = "";
        $options         = "";
        $catalog_id      = "";
        $avaliable_value = "N";

        if (isset($object_id)) {
            $good = self::getObjectByID($object_id);

            if (isset($good)) {
                $title           = "Редактирование атрибутов товара";
                $id              = $good->getId();
                $name            = $good->getName();
                $comment         = $good->getComment();
                $cost            = $good->getCost();
                $description     = $good->getDescription();
                $p_description   = $good->getPreviewDescription();
                $catalog_id      = $good->catalog_id;
                $avaliable_value = $good->avaliable;
            }
        }

        // Получить список каталогов
        $conn   = new CConnection();
        if ($result = $conn->query("select id, name from " . self::CATALOG_TABLE_NAME . " where state='E' order by name")) {

            while ($row = $result->fetch_object()) {
                if ($row->id == $catalog_id) {
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

        // Получить фотографию
        $main_photo_path = "/photos/no-image.png";
        $sub_photos_layers = "";

        if ($result = $conn->query("select id, preview, image from " . self::PHOTOS_TABLE_NAME . " where good_id = '" . $object_id . "' order by create_date")) {

            $p_count = 0;

            while ($row = $result->fetch_object()) {
                $p_count++;

                if ($p_count == 1) {
                    $main_photo_path = $row->image;
                }

                $sub_photos_layers .= '<div div style="background-image: url('. $row->preview .')"></div>';
            }

            $result->close();
        } else {
            CLogger::writeLog("CCatalog::renderForm(): Ошибка выполнения запроса: " . $conn->getError());
            die();
        }


        // Сформировать список наличия
        if ($avaliable_value == "Y") {
            $aval_options = '<option value="Y" selected>Да</option>'
                        .   '<option value="N">Нет</option>';
        } else {
            $aval_options = '<option value="Y">Да</option>'
                        .   '<option value="N" selected>Нет</option>';
        }




        // Карточка группы
        $form = '<form id="SFAddGoodForm" action="" method="post">
                <div class="sh-form-frame">
                    <div class="sh-form-panel"><h2>' . $title . '</h2>
                        <div class="sf-form-error-box"></div><br>
                        <input type="hidden" id="SFGoodID" value="' . $id . '">
                        <div class="sf-goods-images-area">
                            <div class="sf-goods-preview" style="background-image: url('. $main_photo_path .')"></div>
                            <div class="sf-goods-small-previews-area"></div>
                        </div>
                        <div class="sf-goods-fields-area">
                            <div class="sf-catalog-label">*Наименование</div>
                            <div class="sf-input">
                                <input type="text" name="SFGoodName" id="SFGroupName" maxlength="50" value="' . $name . '">
                            </div>
                            <div class="sf-catalog-label">*Цена</div>
                            <div class="sf-input">
                                <input type="text" name="SFGoodCost" id="SFGoodCost" maxlength="50" value="' . $cost . '">
                            </div>
                            <div class="sf-catalog-label">*В наличии</div>
                            <div class="sf-select">
                                <select id="SFGoodAvaliable">
                                    ' . $aval_options . '
                                </select>
                            </div>
                            <div class="sf-catalog-label">*Каталог</div>
                            <div class="sf-select">
                                <select id="SFGoodCatalog">
                                    ' . $options . '
                                </select>
                            </div>
                            <div class="sf-catalog-label">*Краткое описание</div>
                            <div class="sf-input">
                                <textarea name="SFGoodPreviewDescription" id="SFGoodPreviewDescription" maxlength="200">' . $p_description . '</textarea>
                            </div>
                            <div class="sf-catalog-label">*Подробное описание</div>
                            <div class="sf-input">
                                <textarea name="SFGoodDescription" id="SFGoodDescription" maxlength="400" style="min-height:60px;">' . $description . '</textarea>
                            </div>
                            <div class="sf-catalog-label">Примечание</div>
                            <div class="sf-input">
                                <textarea name="SFGoodComment" id="SFGroupComment" maxlength="200">' . $comment . '</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="sf-catalog-footer">
                        <div class="sf-image-button" id="SFSave"><div class="sf-image-button-icon ' . CForms::getButtonStyle(CForms::BUTTON_OK) . '"></div><a href="#">Сохранить</a></div>
                        <div class="sf-image-button" id="SFCancel"><div class="sf-image-button-icon ' . CForms::getButtonStyle(CForms::BUTTON_CANCEL) . '"></div><a href="#">Отмена</a></div>
                    </div>
                </div>
                </form>';

        return $form;
    }

}
