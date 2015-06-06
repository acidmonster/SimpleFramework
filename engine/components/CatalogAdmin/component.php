<?php

/* ===========================================================================
 * Компонент: Каталоги. Администрирование
 * Описание: Компонент позволяет выполнять следующие действия:
 *  - создавать новый каталог;
 *  - изменять существующие каталоги;
 *  - удалять каталоги.
 *
 * 2015, Антон Калашников.
 * ===========================================================================
 */

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CForms.php';
include_once 'classes/CCatalog.php';
include_once 'classes/CCatalogGroup.php';

class CCatalogAdmin {

    const SCRIPT_FILE_NAME = "script.js";
    const STYLE_FILE_NAME = "style.css";

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


    protected function getCatalogsList() {
        $html = '';

        // Получить каталоги из базы данных
        $catalog_list = CCatalog::getCatalogsList(TRUE);
        $old_group_id = "";

        for ($i = 0; $i < $catalog_list->getItemsCount(); $i++) {
            $catalog = $catalog_list->items($i);

            if ($old_group_id != $catalog->getGroup_id()) {
                $group = CCatalogGroup::getObjectByID($catalog->getGroup_id());

                // Отобразить каталог
                $html .= '<div class="sf-catalog-group-item">
                            <div class="sf-item-check-layer">
                            </div><input type="hidden" value="' . $group->getId() . '" class="sf-catalog-group-item-id">
                            <div class="sf-image-button-icon sf-group-icon"></div>
                            <div class="sf-catalog-group-title">' . $group->getName() . '</div>
                          </div>';

                $old_group_id = $catalog->getGroup_id();
            }


            $html .= '<div class="sf-catalog-item">'
                        . '<div class="sf-item-check-layer"><input type="checkbox" class="sf-item-check" value="' . $catalog->getId() . '">'
                        . '</div><input type="hidden" value="' . $catalog->getId() . '" class="sf-catalog-item-id">'
                        . '<div class="sf-image-button-icon sf-catalog-icon"></div>'
                        . '<div class="sf-catalog-title">' . $catalog->getName() . '</div>'
                    . '</div>';
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

            $this->html .= self::getCatalogsList();

            return $this->html;
        } else {
            return "";
        }
    }

}
