<?php

/* ===========================================================================
 * Компонент: Редактирование товара. Администрирование
 * Описание: Компонент позволяет выполнять следующие действия:
 *  - Добавлять/изменять в каталогах новые товары;
 *
 * 2015, Антон Калашников.
 * ===========================================================================
 */

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/engine/classes/CForms.php';
include_once 'classes/CCatalogGood.php';

class CEditGoodsAdmin {

    const SCRIPT_FILE_NAME = "script.js";
    const STYLE_FILE_NAME  = "style.css";
    const GOODS_TABLE_NAME = "sf_catalogs_goods";

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
            $this->html .= CCatalogGood::renderForm();

            //$this->html .= self::getGroupsList();

            return $this->html;
        } else {
            return "";
        }
    }

}
