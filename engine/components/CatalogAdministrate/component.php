<?php

/* ===========================================================================
 * Компонент: Каталог. Администрирование
 * Описание: Компонент позволяет производить действия с каталогами.
 * ===========================================================================
 */

include_once 'classes/CCatalogGroupList.php';

class CCatalogAdministrate {

    const SCRIPT_FILE_NAME = "script.js";
    const STYLE_FILE_NAME = "style.css";
    const GROUP_TABLE_NAME = "sf_catalogs_groups";
    const CATALOG_TABLE_NAME = "sf_catalogs";
    const CATALOG_ITEM_TABLE_NAME = "sf_catalogs_items";

    protected $html;

    public function __construct() {
        $this->html .= $this->readScript();
        $this->html .= $this->readCSS();
    }

    public function __destruct() {

    }

    /**
     * Метод читает файл style.css и возвращает содержимое в виде строки
     * @return string Содержимое файла style.css
     */
    protected function readScript() {
        // Проверить наличие файла скриптов
        $data = "";

        if (file_exists(__DIR__ . "/" . self::SCRIPT_FILE_NAME)) {
            $f = fopen(__DIR__ . "/" . self::SCRIPT_FILE_NAME, "r");

            // Читать построчно до конца файла
            while (!feof($f)) {
                $data .= fgets($f) . "\n";
            }

            fclose($f);
        }

        return "<script>" . $data . "</script>";
    }

    /**
     * Метод читает файл style.css и возвращает содержимое в виде строки
     * @return string Содержимое файла style.css
     */
    protected function readCSS() {
        // Проверить наличие таблицы стилей
        $data = "";

        if (file_exists(__DIR__ . "/" . self::STYLE_FILE_NAME)) {
            $f = fopen(__DIR__ . "/" . self::STYLE_FILE_NAME, "r");

            // Читать построчно до конца файла
            while (!feof($f)) {
                $data .= fgets($f) . "\n";
            }

            fclose($f);
        }

        return "<style>" . $data . "</style>";
    }

    protected function renderList() {
        $html = '<div class="sf-catalog-settings-area">
                    <div class="sf-catalog-setting-buttons">
                        <div class="sf-image-button" id="SFAddGroup"><div class="sf-image-button-icon add-catalog-group"></div><a href="#">Добавить группу</a></div>
                        <div class="sf-image-button" id="SFAddCatalog"><div class="sf-image-button-icon add-catalog-item"></div><a href="#">Добавить каталог</a></div>
                    </div>';

        // Получить группы из базы данных
        $group_list = new CCatalogGroupList();

        for ($i = 1; $i < $group_list->getItemsCount(); $i++) {
            $group =  $group_list->items($i);

            $html .= '<div class="sf-catalog-group-item"><input type="hidden" value="' . $group->getId() . '" class="sf-catalog-group-item-id"><div class="sf-image-button-icon add-catalog-group"></div><div class="sf-catalog-group-title">' . $group->getName() . '</div></div>';
        }

//        echo $group_list->getItemsCount();

        //$con = new CConnection();
        //$result = $con->query("select id, name from " . self::GROUP_TABLE_NAME . " where state = 'E' order by name");
        //unset($con);

//        if ($result->num_rows > 0) {
//            while ($row = $result->fetch_object()) {
//                $html .= '<div class="sf-catalog-group-item"><input type="hidden" value="' . $row->id . '" class="sf-catalog-group-item-id"><div class="sf-image-button-icon add-catalog-group"></div><div class="sf-catalog-group-title">' . $row->name . '</div></div>';
//
//                // Получить каталоги группы
//                $catalog_res = $con->query("select id, name from " . self::CATALOG_TABLE_NAME . " where state = 'E' and group_id='" . $row->id . "' order by name");
//
//                if ($catalog_res->num_rows > 0) {
//                    while ($catalog_row = $catalog_res->fetch_object()) {
//                        $html .= '<div class="sf-catalog-item"><input type="hidden" value="' . $catalog_row->id . '" class="sf-catalog-item-id"><div class="sf-image-button-icon add-catalog-item"></div><div class="sf-catalog-title">' . $catalog_row->name . '</div></div>';
//                    }
//                }
//            }
//        } else {
//            $html .= '<div class="sf-catalog-group-not-found"><p><b>Пока нет ни одной группы.</b></p><p></p>Для добавления новой группы каталогов нажмите кнопку "Добавить группу".</div>';
//        }

        // Карточка группы
        $group_form = '<form id="SFGroupForm" action="" method="post">
        <div class="sh-catalog-frame">
            <div class="sh-catalog-form"><h2>Группа каталога</h2>
                <input type="hidden" id="SFCatalogAdminAction" value="AddGroup">
                <div class="sf-catalog-error-box"></div>
                <div class="sf-catalog-label">*Наименование</div>
                 <div class="sf-input">
                    <input type="text" alt="Наименование группы" name="SFGroupName" id="SFGroupName" maxlength="50" value="">
                </div>
                <div class="sf-catalog-label">Примечание</div>
                <div class="sf-input">
                    <textarea alt="Примечание" name="SFGroupComment" id="SFGroupComment" maxlength="200"></textarea>
                </div>
                <div class="sf-catalog-footer">
                    <div class="sf-image-button" id="SFSaveGroup"><div class="sf-image-button-icon save-catalog-item"></div><a href="#">Сохранить</a></div>
                    <div class="sf-image-button" id="SFCancelGroup"><div class="sf-image-button-icon cancel-catalog-item"></div><a href="#">Отмена</a></div>
                </div>
            </div>
        </div>
        </form>';

        // Карточка каталога
        $catalog_form = '<form id="SFCatalogForm" action="" method="post">
        <div class="sh-catalog-frame">
            <div class="sh-catalog-form"><h2>Каталог</h2>
                <div class="sf-catalog-error-box"></div>
                <div class="sf-catalog-label">*Наименование</div>
                <div class="sf-input">
                    <input type="text" alt="Наименование группы" name="SFCatalogName" id="SFCatalogName" maxlength="50" value="">
                </div>
                <div class="sf-catalog-label">*Группа</div>
                <div class="sf-select">
                    <select id="SFCatalogGroupName">
                    </select>
                </div>
                <div class="sf-catalog-label">Примечание</div>
                <div class="sf-input">
                    <textarea alt="Примечание" name="SFCatalogComment" id="SFCatalogComment" maxlength="200"></textarea>
                </div>
                <div class="sf-catalog-footer">
                    <div class="sf-image-button" id="SFSaveCatalog"><div class="sf-image-button-icon save-catalog-item"></div><a href="#">Сохранить</a></div>
                    <div class="sf-image-button" id="SFCancelCatalog"><div class="sf-image-button-icon cancel-catalog-item"></div><a href="#">Отмена</a></div>
                </div>
            </div>
        </div>
        </form>';


        $html .='<div class="sf-catalog-background"><div class="sf-catalog-background-grey"></div>';
        $html .= $group_form;
        $html .= $catalog_form;
        $html .= '</div>';

        return $html;
    }

    public function renderCatalog($id) {
        $html = "";

        if (!isset($id)) {
            die();
        } else {
            // Получить наименование группы

            // Сформировать панель навигации
            $nav = '<div class="sf-catalog-navigation">'
                        . '<div class="sf-button" id="SFCatalogNavRoot">'
                        .   '<a href="#">&gt;</a>'
                        . '</div>'
                        . '<div class="sf-button" id="SFCatalogNavGroup">'
                        .   '<a href="#"></a>'
                        . '</div>'
                    . '</div>';

            $html .= $nav;
            // Получить содержимое каталога из базы данных
            $con = new CConnection();
            $result = $con->query("select id, name from " . self::CATALOG_ITEM_TABLE_NAME . " where state = 'E' and catalog_id='" . $id . "' order by name");
            unset($con);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_object()) {
                    $html .= '<div class="sf-catalog-group-item"><input type="hidden" value="' . $row->id . '" class="sf-catalog-group-item-id"><div class="sf-image-button-icon add-catalog-group"></div><div class="sf-catalog-group-title">' . $row->name . '</div></div>';
                }
            } else {
                $html .= '<div class="sf-catalog-group-not-found"><p><b>Пока нет ни одной группы.</b></p><p></p>Для добавления новой группы каталогов нажмите кнопку "Добавить группу".</div>';
            }
        }

        return $html;
    }

    /**
     *
     * @param array $param_array  Массив параметров в формате "Имя параметра" => "Значение параметра"
     * @return string Djpdhfoftn HTML код компонента
     */
    public function render($param_array = []) {
        /* Работа с каталогами допускается только администраторам системы,
         * поэтому выполняем проверку авторизации
         */

        if (Capp::checkAuthorize()) {
            $action = CApp::readURLProperty("action");
            $id = CApp::readURLProperty("id");

            if (!isset($action) || !isset($id)) {
                $this->html .= $this->renderList();
            } else {
                switch ($action) {
                    case "catalog":
                        $this->html .= $this->renderCatalog($id);

                        break;

                    default:
                        $this->html .= $this->renderList();
                        break;
                }
            }

            return $this->html;
        } else {
            return "";
        }
    }

}
