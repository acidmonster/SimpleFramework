<?php

/* ===========================================================================
 * Компонент: Группы каталогов. Администрирование
 * Описание: Компонент позволяет выполнять следующие действия:
 *  - создавать новую группу;
 *  - изменять существующие группы;
 *  - удалять группы.
 *
 * 2015, Антон Калашников.
 * ===========================================================================
 */

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CForms.php';
include_once 'classes/CCatalogGroup.php';

class CGroupAdmin {

    const SCRIPT_FILE_NAME = "script.js";
    const STYLE_FILE_NAME  = "style.css";
    const GROUP_TABLE_NAME = "sf_catalogs_groups";

    protected $html;

    public function __construct() {
        $this->html .= $this->readScript();
        $this->html .= $this->readCSS();
    }

    public function __destruct() {

    }

    /**
     * Метод читает файл script.js и возвращает содержимое в виде строки
     * @return string Содержимое файла script.js
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

    protected function getGroupsList() {
        $html = '';

        // Получить группы из базы данных
        $group_list = CCatalogGroup::getGroupsList(TRUE);

        for ($i = 0; $i < $group_list->getItemsCount(); $i++) {
            $group = $group_list->items($i);
            $html .= '<div class="sf-catalog-group-item"><div class="sf-item-check-layer"><input type="checkbox" class="sf-item-check" value="' . $group->getId() . '"></div><input type="hidden" value="' . $group->getId() . '" class="sf-catalog-group-item-id"><div class="sf-image-button-icon sf-group-icon"></div><div class="sf-catalog-group-title">' . $group->getName() . '</div></div>';
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
            $this->html .= '<div id="SFCatalogFormLayer"></div>
                            <div class="sf-catalog-settings-area">
                            <div class="sf-catalog-setting-buttons">
                                <div class="sf-image-button" id="SFAdd">
                                    <div class="sf-image-button-icon ' . CForms::getButtonStyle(CForms::BUTTON_ADD) . '"></div>
                                    <a href="#">Добавить</a>
                                </div>
                                <div class="sf-image-button" id="SFDelete">
                                    <div class="sf-image-button-icon '. CForms::getButtonStyle(CForms::BUTTON_DELETE) .'"></div>
                                    <a href="#">Удалить</a>
                                </div>
                            </div>';

            $this->html .= self::getGroupsList();

            return $this->html;
        } else {
            return "";
        }
    }

}
