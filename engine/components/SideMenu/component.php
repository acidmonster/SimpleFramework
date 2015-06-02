<?php
/* ===========================================================================
 * Компонент: дополнительное меню
 * Описание: Компонент формирует блок меню на основании конфигурации,
 *           описанной в файле .side-menu.php
 *
 * Структура файла .menu.php
 *      Массив элементов меню.
 *          Массив атрибутов элемента меню:
 *              0 - Заголовок (строка)
 *              1 - адрес страницы (строка)
 *              2 - Требуется права администратора (булево)
 * ===========================================================================
 */

class CSideMenu {

    public function __construct() {

    }

    public function __destruct() {

    }

    public function render($param_array=[]) {

        /*
         * Попытка получить структуру меню из файла .side-menu.php
         * Поиск начинается в текущем каталоге. Если файл не найден, то
         * поднимаемся выше, пока не дойдем до корневого каталога
         */

        $path = CApp::getFullCurrentPath();
        $stop         = FALSE;

        $file_path = $path . "" . (".side-menu.php");

        while (!$stop) {
            if (file_exists($file_path)) {
                include_once $file_path;
                $stop = TRUE;
            } elseif ($path == CApp::GetRootFolder() . "/") {
                CLogger::writeLog("Не удалось получить структуру меню.");
                die();
            } else {
                $path = dirname($path) . "/";
                $file_path = $path . "" . (".side-menu.php");
            }
        }

        // Если структура меню найдена, то достпен массив пунктов меню $menu
        $component = '<ul class="sf-side-menu-group">';

        foreach ($menu as $menu_item) {
            $menu_title  = $menu_item[0];
            $menu_link   = $menu_item[1];
            $menu_secure = $menu_item[2];
            $menu_html   = "";

            if (CApp::checkAuthorize() || !$menu_secure) {
                if (!$menu_secure) {
                    // Авторизация выполнена или пункт меню не требует авторизации
                    if ($menu_link == CApp::getCurrentPage() ||
                            $menu_link . "index.php" == CApp::getCurrentPage() ||
                            $menu_link . "index.html" == CApp::getCurrentPage() ||
                            $menu_link . "index.htm" == CApp::getCurrentPage()) {
                        $menu_html = '<li><a href="' . $menu_link . '" class="sf-side-menu-item-selected">' . $menu_title . '</a></li>';
                    } else {
                        $menu_html = '<li><a href="' . $menu_link . '" class="sf-side-menu-item">' . $menu_title . '</a></li>';
                    }
                } else {
                    // Авторизация выполнена и пункт меню требует авторизации
                    if ($menu_link == CApp::getCurrentPage() ||
                            $menu_link . "index.php" == CApp::getCurrentPage() ||
                            $menu_link . "index.html" == CApp::getCurrentPage() ||
                            $menu_link . "index.htm" == CApp::getCurrentPage()) {
                        $menu_html = '<li><a href="' . $menu_link . '" class="sf-side-menu-item-secured-selected">' . $menu_title . '</a></li>';
                    } else {
                        $menu_html = '<li><a href="' . $menu_link . '" class="sf-side-menu-item-secured">' . $menu_title . '</a></li>';
                    }
                }
            }
            $component = $component . "" . $menu_html;
        }
        $component = $component . "</ul>";

        return $component;
    }
}
