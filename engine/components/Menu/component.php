<?php

/* ===========================================================================
 * Компонент: Главное меню
 * Описание: Компонент формирует блок меню на основании конфигурации,
 *           описанной в файле .menu.php
 *
 * Структура файла .menu.php
 *      Массив элементов меню.
 *          Массив атрибутов элемента меню:
 *              0 - Заголовок (строка)
 *              1 - адрес страницы (строка)
 *              2 - Требуется права администратора (булево)
 * ===========================================================================
 */

class CMenu {

    public function __construct() {

    }

    public function __destruct() {

    }

    public function render($param_array=[]) {
        /*
         * Попытка локально получить структуру меню из файла .menu.php
         * Иначе, получаем структуру из корневого файла
         */
        $path = CApp::getFullCurrentPath();
        $stop = FALSE;

        $file_path = $path . "" . (".menu.php");

        while (!$stop) {
            if (file_exists($file_path)) {
                include_once $file_path;
                $stop = TRUE;
            } elseif ($path == CApp::GetRootFolder() . "/") {
                CLogger::writeLog("Не удалось получить структуру меню.");
                die();
            } else {
                $path      = dirname($path) . "/";
                $file_path = $path . "" . (".menu.php");
            }
        }

        // Если структура меню найдена, то достпен массив пунктов меню $menu
        $component = '<ul class="sf-menu-group">';

        foreach ($menu as $menu_item) {
            $menu_title  = $menu_item[0];
            $menu_link   = $menu_item[1];
            $menu_secure = $menu_item[2];
            $menu_html   = "";

            if (CApp::checkAuthorize() || !$menu_secure) {
                if (!$menu_secure) {
                    // Авторизация выполнена или пункт меню не требует авторизации
                    if ((stristr(CApp::getCurrentPage(), $menu_link) !== FALSE && $menu_link != '/') ||
                            $menu_link == CApp::getCurrentPage() ||
                            $menu_link . "index.php" == CApp::getCurrentPage() ||
                            $menu_link . "index.html" == CApp::getCurrentPage() ||
                            $menu_link . "index.htm" == CApp::getCurrentPage()) {
                        $menu_html = '<li><a href="' . $menu_link . '" class="sf-menu-item-selected">' . $menu_title . '</a></li>';
                    } else {
                        $menu_html = '<li><a href="' . $menu_link . '" class="sf-menu-item">' . $menu_title . '</a></li>';
                    }
                } else {
                    // Авторизация выполнена и пункт меню требует авторизации
                    if ((stristr(CApp::getCurrentPage(), $menu_link) !== FALSE && $menu_link != '/') ||
                            $menu_link == CApp::getCurrentPage() ||
                            $menu_link . "index.php" == CApp::getCurrentPage() ||
                            $menu_link . "index.html" == CApp::getCurrentPage() ||
                            $menu_link . "index.htm" == CApp::getCurrentPage()) {
                        $menu_html = '<li><a href="' . $menu_link . '" class="sf-menu-item-secured-selected">' . $menu_title . '</a></li>';
                    } else {
                        $menu_html = '<li><a href="' . $menu_link . '" class="sf-menu-item-secured">' . $menu_title . '</a></li>';
                    }
                }
            }

            $component = $component . "" . $menu_html;
        }
        $component = $component . "</ul>";

        return $component;
    }
}
